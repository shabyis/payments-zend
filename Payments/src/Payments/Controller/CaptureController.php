<?php
namespace Payments\Controller;

use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Reply\ReplyInterface;
use Payum\Core\Request\Capture;
use Payum\PayumModule\Controller\PayumController;
use Zend\Http\Response;
use Zend\View\Model\ViewModel;

/**
 * Class CaptureController
 * @package Payments\Controller
 */
class CaptureController extends PayumController
{
    /**
     * @return Response|ViewModel
     */
    public function doAction()
    {
        try{

            $token = $this->getHttpRequestVerifier()->verify($this);
            $gateway = $this->getPayum()->getGateway($token->getGatewayName());
        }
        catch(\Exception $e) {

            // Custom Addition of error page on any exception on Payum Package.

            $viewModel = new ViewModel([
                'status' => 'processing_error',
                'details' => [
                    'error_message' => $e->getMessage()
                ],
            ]);

            $viewModel->setTemplate('layout/payment_exception_response');
            return $viewModel;
        }

        try {
            $gateway->execute(new Capture($token));
        } catch (ReplyInterface $reply) {
            if ($reply instanceof HttpRedirect) {
                return $this->redirect()->toUrl($reply->getUrl());
            }

            if ($reply instanceof HttpResponse) {
                $this->getResponse()->setContent($reply->getContent());

                $response = new Response();
                $response->setStatusCode(200);
                $response->setContent($reply->getContent());

                return $response;
            }

            throw new \LogicException('Unsupported reply', null, $reply);
        }

        $this->getHttpRequestVerifier()->invalidate($token);

        return $this->redirect()->toUrl($token->getAfterUrl());
    }
}
