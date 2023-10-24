<?php

namespace Payments\Helpers;

use Orders\Entity\OrderTransaction;

class OfflineHelper extends AbstractHelper
{
    /**
     * Doctrine entity manager.
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * OfflineHelper constructor.
     * @param $em
     */
    public function __construct( $em){

        $this->entityManager = $em;
    }

    /**
     * @param $request
     * @param $coupon
     */
    public function payUsingSepa( $request, $coupon) {

        /** @var OrderTransaction $transactions */
        $transactions = new OrderTransaction;

        $transactions->setAccountHolder($request->getPost('account_holder'));
        $transactions->setIban($request->getPost('account_iban'));
        $transactions->setSwift($request->getPost('account_bic'));
        $transactions->setOrderId($coupon->getId());
        $transactions->setDateUpdated(new \DateTime("now"));
        $transactions->setDateAdded(new \DateTime("now"));
        $transactions->setStatus("Pending");

        $this->entityManager->persist($transactions);
        $this->entityManager->flush();
    }

    /**
     * @param $coupon
     */
    public function payUsingBill( $coupon){

        /** @var OrderTransaction $transactions */
        $transactions = new OrderTransaction;

        $transactions->setOrderId($coupon->getId());
        $transactions->setDateUpdated(new \DateTime("now"));
        $transactions->setDateAdded(new \DateTime("now"));
        $transactions->setStatus("Pending");

        $this->entityManager->persist($transactions);
        $this->entityManager->flush();
    }    
}