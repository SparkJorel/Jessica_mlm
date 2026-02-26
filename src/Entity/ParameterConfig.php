<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ParameterConfig
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ParameterConfigRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class ParameterConfig implements EntityInterface
{
    /**
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/[a-z0-9_]+/", groups={"registration_parameter"})
     * @Assert\NotBlank(groups={"registration_parameter"})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     * @Assert\NotBlank(groups={"registration_parameter"})
     */
    private $value;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $removed;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $recordDate;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deactivatedDate;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ParameterConfig
     */
    public function setName(string $name): self
    {
        $this->name = strtolower(trim($name));
        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return ParameterConfig
     */
    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @return DateTime
     */
    public function getRecordDate(): ?DateTime
    {
        return $this->recordDate;
    }

    /**
     * @return DateTime
     */
    public function getDeactivatedDate(): ?DateTime
    {
        return $this->deactivatedDate;
    }

    /**
     * @return bool
     */
    public function isRemoved(): ?bool
    {
        return $this->removed;
    }

    /**
     * @param bool $removed
     * @return ParameterConfig
     */
    public function setRemoved(bool $removed): self
    {
        $this->removed = $removed;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function prePersist(): void
    {
        $this->status = true;
        $this->removed = false;
        $this->recordDate = new DateTime("now", new DateTimeZone("Africa/Douala"));
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
