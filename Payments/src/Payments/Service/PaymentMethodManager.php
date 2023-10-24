<?php
/**
 * Created by PhpStorm.
 * User: hp
 * Date: 9/19/2017
 * Time: 12:06 PM
 */

namespace Payments\Service;

use Payments\Entity\PaymentMethod;
use Payments\Helpers\CryptoPaymentHelper;
use Payments\Helpers\PaypalHelper;
use Payments\Helpers\SofortHelper;
use Reisesparerservice\Service\ReisesBaseService;

/**
 * Class PaymentMethodManager
 * @package Payments\Service
 */
class PaymentMethodManager extends ReisesBaseService {

	/**
	 * PaymentMethod Entity Object
	 * @var PaymentMethod
	 */
	private $paymentMethod;

	/**
	 * @var PaypalHelper
	 */
	private $paypalHelper;

	/**
	 * @var SofortHelper
	 */
	private $sofortHelper;

    /**
     * @var CryptoPaymentHelper $cryptoPayHelper
     */
    private $cryptoPayHelper;

	/**
	 * PaymentMethodManager constructor.
	 *
	 * @param $serviceManager
	 */
	public function __construct ( $serviceManager ) {
        parent::__construct( $serviceManager );

		$this->paypalHelper  = $serviceManager->get('paypalHelper');
		$this->sofortHelper  = $serviceManager->get('sofortHelper');
        $this->cryptoPayHelper  = $serviceManager->get( CryptoPaymentHelper::class );
		$this->paymentMethod = $this->getEntityManager()->getRepository ( PaymentMethod::class );
	}

	/**
	 * @param bool $arrayForm
	 *
	 * @return array
	 */
	public function getPaymentMethods ( $arrayForm = true , $status = null ) {
		if( !is_null($status) ){
            $paymentMethods = $this->paymentMethod->findBy([
                'isActivated' => $status,
            ]);
        }
        else{
            $paymentMethods = $this->paymentMethod->findAll();
        }

		if ( $arrayForm ) {
			$paymentMethods = $this->getPaymentMethodArray ( $paymentMethods );
		}

		return $paymentMethods;
	}

	/**
	 * @param $paymentMethods
	 *
	 * @return array
	 */
	private function getPaymentMethodArray ( $paymentMethods ) {
		$valueOptions = [];
		/**
		 * @var PaymentMethod $paymentMethod
		 */
		foreach ( $paymentMethods as $paymentMethod ) {
			$singleOption['id']    = $paymentMethod->getName ();
			$singleOption['value'] = $paymentMethod->getId ();
			$valueOptions[]        = array_merge ( $valueOptions, $singleOption );
		}

		return $valueOptions;
	}

	/**
	 * @param $id
	 *
	 * @return null|object
	 */
	public function getPaymentMethod ( $id ) {
		return $this->paymentMethod->find ( $id );
	}

	/**
	 * @param $order
	 *
	 * @param $responseURL
	 *
	 * @return array|null
	 */
	public function payWithPayPal ( $order, $responseURL ) {
		$url = $this->paypalHelper->payUsingPaypal ($order, $responseURL);
		if(!is_null ($url)){
			return [ 'status' =>'redirect_succes', 'url' =>$url ];
		}
		return null;
	}

    /**
     * @param $order
     * @param $responseURL
     * @return array|null
     */
    public function payWithSofort($order, $responseURL ) {
        $url = $this->sofortHelper->payUsingSofort($order, $responseURL);
        if(!is_null ($url)){
            return [ 'status' =>'redirect_succes', 'url' =>$url ];
        }
        return null;
    }

	/**
	 * @param $token
	 *
	 * @return array
	 */
	public function getPayPalPaymentObjectByToken ( $token ) {
		return $this->paypalHelper->getPaymentObjectByToken ($token);
	}

    /**
     * @param $token
     * @return array
     */
    public function getSofortPaymentObjectByValues( $values ) {
        return $this->sofortHelper->getPaymentObjectByValues($values);
    }

    /**
     * @param $token
     * @return array
     */
    public function getCryptoPaymentObjectByValues( $values ) {
        return $this->cryptoPayHelper->getPaymentObjectByValues($values);
    }

}
