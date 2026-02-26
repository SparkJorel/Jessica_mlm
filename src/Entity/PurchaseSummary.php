<?php

declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;


class PurchaseSummary
{
    /** 
     * @var AddressUser
     * @Assert\NotBlank(groups={"test"})
     */
    private $addressUser;

    private $montant;

    private $motif;

    private $provider;

    private $operateur;

    private $success;

    private $notifyPage;

    private $fail;

    private $transaction;

    private $otpCode;

    public function __construct(?float $montant, ?string $motif, string $provider, int $operateur, string $success, string $notifyPage, string $fail)
    {
        $this->montant = $montant;
        $this->motif = $motif;
        $this->provider = $provider;
        $this->fail = $fail;
        $this->success = $success;
        $this->notifyPage = $notifyPage;
        $this->operateur = $operateur;
    }

    public function getAddressUser()
    {
        return $this->addressUser;
    }

    public function setAddressUser(AddressUser $addressUser = null)
    {
        $this->addressUser = $addressUser;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;
        return $this;
    }

    public function getMotif()
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;
        return $this;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider)
    {
        $this->provider = $provider;
        return $this;
    }

    public function setOperateur($operateur)
    {
        $this->operateur = $operateur;
        return $this;
    }

    public function getOperateur()
    {
        return $this->operateur;
    }

    public function setSuccess(string $success)
    {
        $this->success = $success;
        return $this;
    }

    public function getSuccess()
    {
        return $this->success;
    }

    public function setFail(string $fail)
    {
        $this->fail = $fail;
        return $this;
    }

    public function getFail()
    {
        return $this->fail;
    }

    public function setTransaction(?string $transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }

    public function setOtpCode(?string $otpCode)
    {
        $this->otpCode = $otpCode;
        return $this;
    }

    public function getOtpCode(): ?string
    {
        return $this->otpCode;
    }

    public function getNotifyPage(): string
    {
        return $this->notifyPage;
    }

    public function setNotifyPage(string $notifyPage): self
    {
        $this->notifyPage = $notifyPage;
        return $this;
    }
}
