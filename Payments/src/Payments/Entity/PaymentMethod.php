<?php

namespace Payments\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentMethod
 *
 * @ORM\Table(name="payment_method")
 * @ORM\Entity
 */
class PaymentMethod {
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=100, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="api_key", type="string", length=200, nullable=true)
	 */
	private $apiKey;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", length=255, nullable=true)
	 */
	private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_activated", type="boolean", nullable=true)
     */
    private $isActivated;

	/**
	 * @return int
	 */
	public function getId () {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId ( $id ) {
		$this->id = $id;
	}

	/**
	 * @return string
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName ( $name ) {
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getApiKey () {
		return $this->apiKey;
	}

	/**
	 * @param string $apiKey
	 */
	public function setApiKey ( $apiKey ) {
		$this->apiKey = $apiKey;
	}

	/**
	 * @return string
	 */
	public function getDescription () {
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription ( $description ) {
		$this->description = $description;
	}

    /**
     * @return bool
     */
    public function isActivated ()
    {
        return $this->isActivated;
    }

    /**
     * @param bool $isActivated
     */
    public function setIsActivated ($isActivated)
    {
        $this->isActivated = $isActivated;
    }

}

