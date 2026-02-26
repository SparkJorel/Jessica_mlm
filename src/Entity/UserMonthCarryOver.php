<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Class UserMonthCarryOver
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\UserMonthCarryOverRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class UserMonthCarryOver implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $co;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $binaire;

    /**
    * @var float
    * @ORM\Column(type="float", nullable=true)
    */
    private $svGain;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $gain;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $leftSideSponsoringSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $leftSideAchatSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $leftSideTotalSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $rightSideSponsoringSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $rightSideAchatSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $rightSideTotalSV;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $oldCO;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $leftOrRightSideNewTotalSV;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $position;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $oldPosition;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $month;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $year;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $recordDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $user;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getCo(): float
    {
        return $this->co;
    }

    /**
     * @param float $co
     * @return UserMonthCarryOver
     */
    public function setCo(float $co): self
    {
        $this->co = $co;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBinaire(): ?float
    {
        return $this->binaire;
    }

    /**
     * @param float $binaire|null
     * @return UserMonthCarryOver
     */
    public function setBinaire(?float $binaire): self
    {
        $this->binaire = $binaire;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getSvGain(): ?float
    {
        return $this->svGain;
    }

    /**
     * @param float $svGain|null
     * @return UserMonthCarryOver
     */
    public function setSvGain(?float $svGain): self
    {
        $this->svGain = $svGain;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeftSideAchatSV(): ?float
    {
        return $this->leftSideAchatSV;
    }

    /**
     * @param float $leftSideAchatSV|null
     * @return UserMonthCarryOver
     */
    public function setLeftSideAchatSV(?float $leftSideAchatSV): self
    {
        $this->leftSideAchatSV = $leftSideAchatSV;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeftSideSponsoringSV(): ?float
    {
        return $this->leftSideSponsoringSV;
    }

    /**
     * @param float $leftSideSponsoringSV|null
     * @return UserMonthCarryOver
     */
    public function setLeftSideSponsoringSV(?float $leftSideSponsoringSV): self
    {
        $this->leftSideSponsoringSV = $leftSideSponsoringSV;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeftSideTotalSV(): ?float
    {
        return $this->leftSideTotalSV;
    }

    /**
     * @param float $leftSideTotalSV|null
     * @return UserMonthCarryOver
     */
    public function setLeftSideTotalSV(?float $leftSideTotalSV): self
    {
        $this->leftSideTotalSV = $leftSideTotalSV;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRightSideSponsoringSV(): ?float
    {
        return $this->rightSideSponsoringSV;
    }

    /**
     * @param float $rightSideSponsoringSV|null
     * @return UserMonthCarryOver
     */
    public function setRightSideSponsoringSV(?float $rightSideSponsoringSV): self
    {
        $this->rightSideSponsoringSV = $rightSideSponsoringSV;
        return $this;
    }
    /**
     * @return float|null
     */
    public function getRightSideAchatSV(): ?float
    {
        return $this->rightSideAchatSV;
    }

    /**
     * @param float $rightSideAchatSV|null
     * @return UserMonthCarryOver
     */
    public function setRightSideAchatSV(?float $rightSideAchatSV): self
    {
        $this->rightSideAchatSV = $rightSideAchatSV;
        return $this;
    }
    /**
     * @return float|null
     */
    public function getRightSideTotalSV(): ?float
    {
        return $this->rightSideTotalSV;
    }

    /**
     * @param float $rightSideTotalSV|null
     * @return UserMonthCarryOver
     */
    public function setRightSideTotalSV(?float $rightSideTotalSV): self
    {
        $this->rightSideTotalSV = $rightSideTotalSV;
        return $this;
    }
    /**
     * @return float|null
     */
    public function getOldCO(): ?float
    {
        return $this->oldCO;
    }

    /**
     * @param float $oldCO|null
     * @return UserMonthCarryOver
     */
    public function setOldCO(?float $oldCO): self
    {
        $this->oldCO = $oldCO;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getGain(): ?float
    {
        return $this->gain;
    }

    /**
     * @param float $gain|null
     * @return UserMonthCarryOver
     */
    public function setGain(?float $gain): self
    {
        $this->gain = $gain;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLeftOrRightSideNewTotalSV(): ?float
    {
        return $this->leftOrRightSideNewTotalSV;
    }

    /**
     * @param float $leftOrRightSideNewTotalSV|null
     * @return UserMonthCarryOver
     */
    public function setLeftOrRightSideNewTotalSV(?float $leftOrRightSideNewTotalSV): self
    {
        $this->leftOrRightSideNewTotalSV = $leftOrRightSideNewTotalSV;
        return $this;
    }

    /**
     * @return string
     */
    public function getPosition(): ?string
    {
        return $this->position;
    }

    /**
     * @param string $position
     * @return UserMonthCarryOver
     */
    public function setPosition(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string
     */
    public function getOldPosition(): ?string
    {
        return $this->oldPosition;
    }

    /**
     * @param string $oldPosition
     * @return UserMonthCarryOver
     */
    public function setOldPosition(string $oldPosition): self
    {
        $this->oldPosition = $oldPosition;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getRecordDate(): DateTime
    {
        return $this->recordDate;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function setRecordDate(): void
    {
        $tz = new DateTimeZone("Africa/Douala");
        $this->recordDate = new DateTime("now", $tz);
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * @param DateTime $endedAt
     * @return UserMonthCarryOver
     */
    public function setEndedAt($endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * @param DateTimeInterface $startedAt
     * @return UserMonthCarryOver
     */
    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return UserMonthCarryOver
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setMonth(string $month)
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        // TODO: Implement isNew() method.
        return is_null($this->id);
    }
}
