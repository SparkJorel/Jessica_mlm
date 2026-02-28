<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Validator\Constraints as JTWCAssert;

class MembershipProducts
{
    private $membership;

    /**
     * @var Collection
     */
    #[JTWCAssert\PackProductQuantityUnique]
    private $membershipProducts;

    public function __construct()
    {
        $this->membershipProducts = new ArrayCollection();
    }

    /**
     * @return Collection|MembershipProduct[]
     */
    public function getMembershipProducts(): Collection
    {
        return $this->membershipProducts;
    }

    /**
     * @param MembershipProduct $membershipProduct
     * @return MembershipProducts
     */
    public function addMembershipProduct(MembershipProduct $membershipProduct): self
    {
        if (!$this->membershipProducts->contains($membershipProduct)) {
            $membershipProduct->setMembership($this->getMembership());
            $this->membershipProducts[] = $membershipProduct;
        }
        return $this;
    }

    /**
     * @param MembershipProduct $membershipProduct
     * @return MembershipProducts
     */
    public function removeMembershipProduct(MembershipProduct $membershipProduct): self
    {
        if ($this->membershipProducts->contains($membershipProduct)) {
            $this->membershipProducts->removeElement($membershipProduct);
        }

        return $this;
    }

    public function getMembership()
    {
        return $this->membership;
    }

    public function setMembership(Membership $membership)
    {
        $this->membership = $membership;
        return $this;
    }
}
