<?php

namespace Payments\Helpers;

use Payum\Sofort\Api;

class SofortHelper extends AbstractHelper
{
    /**
     * Zend Service Locator.
     * @var $serviceManager
     */
    private $serviceManager;

    public function __construct($service){

        $this->serviceManager = $service;
    }

    /**
     * @param $coupon
     * @param $responseUrls
     * @return null
     */
    public function payUsingSofort( $coupon , $responseUrl ) {
        try {

            $config  = $this->serviceManager->get('reisesparer_config_manager')->getConfig();

            $sofortConfigKey = $config['config']['sofort_config_key'];

            $args = [ 'config_key' => $sofortConfigKey ];

            /** @var Api $sofortPay */
            $sofortPay = new Api($args);

            $params = [
                'amount' => $coupon['price'],
                'currency_code' => $coupon['currency'],
                'reason' => $coupon['reason'],
                'reason_2' => $coupon['reason_2'],
                'success_url' => $responseUrl,
                'abort_url' => $responseUrl,
                'customer_protection' => false,
                'notification_url' => 'http://reisesparer-dev.projekte-web.com/testy/sofortResponse',
            ];

            $transactionParams = $sofortPay->createTransaction($params);

            if(array_key_exists('error',$transactionParams)){
                $transactionParams = null;
            }

            return $transactionParams['payment_url'];

        } catch ( \Exception $exception ) {
            return null;
        }
    }

    /**
     * @param $values
     * @return array
     */
    public function getPaymentObjectByValues( $values )
    {
        if($values['sofort_response']['status'] == "untraceable"){
            $paymentStatus = 'Completed';
        }
        else{
            $paymentStatus = 'Pending';
        }

        $transactionObject = [
            'trans_id'          => $values['sofort_response']['transaction'],
            'payment_status'    => $paymentStatus,
            'pay_method'        => 'sofort',
            'payorder_time'     => new \DateTime( $values['sofort_response']['time'] ),
            'order_id'          => $values['custom_data']['order_id'],
            'is_email'          => $values['custom_data']['is_email'],
            'order_type'        => $values['custom_data']['order_type'],
            'first_name'        => $values['sofort_response']['sender_holder'],
            'last_name'         => $values['sofort_response']['sender_holder'],
            'iban_num'          => $values['sofort_response']['sender_iban'],
            'swift_num'         => $values['sofort_response']['sender_bic'],
            'account_holder'    => $values['sofort_response']['sender_holder'],
        ];

        return $transactionObject;
    }
}