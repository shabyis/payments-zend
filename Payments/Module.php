<?php

namespace Payments;

use Doctrine\ORM\EntityManager;
use Payments\Helpers\CryptoPaymentHelper;
use Payments\Helpers\PaypalHelper;
use Payments\Helpers\SofortHelper;
use Payments\Service\PaymentMethodManager;
use Payments\Service\Factory\TokenFactoryFactory;
use Payments\Controller\CaptureController;

class Module
{
    /**
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

	/**
	 * @return array
	 */
	public function getServiceConfig () {
		return [
			'factories' => [

			    'paypalHelper'              => function ( $serviceLocator ) {
		            return new PaypalHelper( $serviceLocator );
				},

                'sofortHelper'              => function ( $serviceLocator ) {
					return new SofortHelper( $serviceLocator );
				},

                PaymentMethodManager::class => function ( $container ) {
					return new PaymentMethodManager( $container );
				},

                'custom_payum.security.token_factory' => function ( $container ) {
                    $tokenFactory = new TokenFactoryFactory();
                    return $tokenFactory->tokenFactoryInitializer( $container );
                },
                CryptoPaymentHelper::class => function ( $container ) {
                    return new CryptoPaymentHelper();
                },
			],
		];
	}

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return [
            'factories' => [
                CaptureController::class => function ($container) {
                    $sm = $container->getServiceLocator();
                    return new CaptureController($sm->get('payum'), $sm->get('payum.security.http_request_verifier'));
                },
            ],
        ];
    }
}
