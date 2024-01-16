<?php

namespace MtoOrderPicking\Api\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

class OrderAdditional
{
    #[SerializedName("auftraggeber")]
    protected ?string $customer = null;

    #[SerializedName("telefonnummer")]
    protected ?string $phone = null;

    #[SerializedName("neutraler_versand")]
    protected ?string $neutral = null;

    #[SerializedName("avis")]
    protected ?string $avis = null;

    #[SerializedName("fixtermin")]
    protected ?string $fix = null;

    #[SerializedName("lieferdatum")]
    protected ?string $deliveryDate = null;

    #[SerializedName("anmerkungen")]
    protected ?string $notes = null;

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): void
    {
        $this->customer = $customer;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getNeutral(): ?string
    {
        return $this->neutral;
    }

    public function setNeutral(?string $neutral): void
    {
        $this->neutral = (bool) $neutral;
    }

    public function getAvis(): ?string
    {
        return $this->avis;
    }

    public function setAvis(?string $avis): void
    {
        $this->avis = (bool) $avis;
    }

    public function getFix(): ?string
    {
        return $this->fix;
    }

    public function setFix(?string $fix): void
    {
        $this->fix = (bool) $fix;
    }

    public function getDeliveryDate(): ?string
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(?string $deliveryDate): void
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }
}