<?php

namespace Payments\Service\Factory;

use Payum\Core\Security\GenericTokenFactory;
use Payum\PayumModule\Security\TokenFactory;
use Zend\Mvc\Controller\PluginManager;

/**
 * Class TokenFactoryFactory
 * @package Payments\Service\Factory
 */
class TokenFactoryFactory
{

    /**
     * @param $serviceManager
     * @return GenericTokenFactory
     */
    public function tokenFactoryInitializer( $serviceManager )
    {
        /** @var PluginManager $plugins */
        $plugins = $serviceManager->get('ControllerPluginManager');

        $tokenFactory = new TokenFactory(
            $serviceManager->get('payum.security.token_storage'),
            $serviceManager->get('payum')
        );

        $tokenFactory->setUrlPlugin( $plugins->get('url') );

        $genericTokenFactory = new GenericTokenFactory($tokenFactory, array(
            'capture' => 'custom_payum_capture_do',
            'notify' => 'payum_notify_do',
            'authorize' => 'payum_authorize_do',
            'refund' => 'payum_refund_do'
        ));

        return $genericTokenFactory;
    }
}
