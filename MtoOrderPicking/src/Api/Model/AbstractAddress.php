<?php

namespace MtoOrderPicking\Api\Model;

use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Framework\Struct\JsonSerializableTrait;


abstract class AbstractAddress implements \JsonSerializable
{
    use JsonSerializableTrait;

    protected ?string $firstName;

    protected ?string $lastName;

    protected ?string $street;

    protected ?string $zip;

    protected ?string $city;

    protected ?string $country;

    public static function fromOrderAddressEntity(OrderAddressEntity $orderAddressEntity): static
    {
        $address = new static();
        $address->firstName = $orderAddressEntity->getFirstName();
        $address->lastName = $orderAddressEntity->getLastName();
        $address->street = $orderAddressEntity->getStreet();
        $address->zip = $orderAddressEntity->getZipcode();
        $address->city = $orderAddressEntity->getCity();
        $address->country = $orderAddressEntity->getCountry()->getIso();

        return $address;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }
}
