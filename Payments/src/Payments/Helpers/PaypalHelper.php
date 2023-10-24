<?php

namespace Payments\Helpers;

use Payments\Model\PaymentDetails;
use Payum\Core\Request\GetHumanStatus;

class PaypalHelper extends AbstractHelper
{
    /**
     * Zend Service Locator.
     * @var $serviceManager
     */
    private $serviceManager;


    public function __construct( $service ){

        $this->serviceManager = $service;
    }

	/**
	 * @param $coupon
	 *
	 * @param $responseURL
	 *
	 * @return mixed
	 */
	public function payUsingPaypal ( $coupon, $responseURL ) {
		try {

            /** @var PaymentDetails $storage */
            $storage = $this->serviceManager->get('payum')->getStorage( 'Payments\Model\PaymentDetails' );

			$details                                  = $storage->create ();
			$details['PAYMENTREQUEST_0_CURRENCYCODE'] = $coupon['currency'];
			$details['PAYMENTREQUEST_0_AMT']          = $coupon['price'];
			$details['L_PAYMENTREQUEST_0_AMT0']       = $coupon['price'];
			$details['L_PAYMENTREQUEST_0_QTY0']       = $coupon['quantity'];
			$details['L_PAYMENTREQUEST_0_DESC0']      = $coupon['description'];
			$details['ORDER_CUSTOM_ID']               = $coupon['orderID'];
			$details['IS_EMAIL']                      = $coupon['is_email'];
			$details['ORDER_TYPE']                    = $coupon['order_type'];

			$storage->update ( $details );

            $captureTokenFactory = $this->serviceManager->get ( 'custom_payum.security.token_factory' );
            $captureToken = $captureTokenFactory->createCaptureToken ( 'paypal_ec', $details, $responseURL );

			return $captureToken->getTargetUrl ();

		} catch ( \Exception $exception ) {
			return null;
		}
	}

	/**
	 * @param $token
	 *
	 * @return array
	 */
	public function getPaymentObjectByToken ( $token ) {

	    $gateway = $this->serviceManager->get ( 'payum' )->getgateway ( $token->getgatewayName () );

		$gateway->execute ( $status = new GetHumanStatus( $token ) );
		$payment = $status->getFirstModel ();

		$transactionObject = [

			'trans_id'       => $payment['PAYMENTINFO_0_TRANSACTIONID'] ? $payment['PAYMENTINFO_0_TRANSACTIONID'] : $payment['PAYMENTREQUESTINFO_0_TRANSACTIONID'],

            'payment_status' => $payment['PAYMENTINFO_0_PAYMENTSTATUS'] ? $payment['PAYMENTINFO_0_PAYMENTSTATUS'] : $payment['PAYMENTREQUEST_0_PAYMENTSTATUS'],

            'payorder_time'  => new \DateTime( $payment['PAYMENTINFO_0_ORDERTIME'] ? $payment['PAYMENTINFO_0_ORDERTIME'] : $payment['PAYMENTREQUEST_0_ORDERTIME'] ),

            'order_id'       => $payment['ORDER_CUSTOM_ID'],
			'is_email'       => $payment['IS_EMAIL'],
			'order_type'     => $payment['ORDER_TYPE'],
			'first_name'     => $payment['FIRSTNAME'],
			'last_name'      => $payment['LASTNAME'],
			'total_amount'   => $payment['AMT'],
            'pay_method'     => 'paypal',

        ];

		return $transactionObject;
	}

}
