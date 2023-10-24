<?php

namespace Payments\Helpers;

class CryptoPaymentHelper extends AbstractHelper
{

    /**
     * @param $values
     * @return array
     */
    public function getPaymentObjectByValues( $values )
    {
        $paymentStatus = 'Pending';
        if( $values['payment_status'] == "completed" ){
            $paymentStatus = 'Completed';
        }

        $transactionObject = [
            'order_id'          => $values['order_id'],
            'trans_id'          => $values['transaction_id'],
            'payment_status'    => $paymentStatus,
            'pay_method'        => 'crypto',
            'payorder_time'     => new \DateTime('now'),
            'order_type'        => $values['order_type'],
            'is_email'          => $values['is_email'],
        ];


        return $transactionObject;
    }
}