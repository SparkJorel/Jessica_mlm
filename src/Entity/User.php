<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use App\AbstractModel\EntityWithImageToUploadInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as JTWCAssert;

#[Gedmo\Tree(type: 'nested')]
#[ORM\Entity(repositoryClass: \App\Repository\UserRepository::class)]
#[UniqueEntity(fields: ['username'], groups: ['registration', 'change_username'])]
#[UniqueEntity(fields: ['email'], groups: ['registration', 'update_profile'])]
#[UniqueEntity(fields: ['codeDistributor'], groups: ['registration'])]
#[JTWCAssert\UplinePosition(groups: ['registration'])]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
#[ORM\HasLifecycleCallbacks]
class User implements
    UserInterface,
    EntityInterface,
    EntityWithImageToUploadInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['registration', 'change_username'])]
    #[Assert\Length(min: 4, max: 20, minMessage: 'Le code d\'authentification doit avoir au minimum {{ limit }} caractères', maxMessage: 'Le code d\'authentification doit être inférieur à {{ limit }} caractères', groups: ['registration', 'change_username'])]
    private $username;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(groups: ['registration', 'quick_registration', 'update_profile'])]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['registration', 'quick_registration', 'update_profile'])]
    private $fullname;

    #[ORM\Column(type: 'string', nullable: true)]
    private $cni;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $country;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private $entryDate;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(choices: ['Left', 'Right'], groups: ['registration', 'quick_registration'])]
    #[Assert\NotBlank(groups: ['registration', 'quick_registration'])]
    private $position;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Choice(choices: ['admin', 'user'], groups: ['registration', 'quick_registration'])]
    #[Assert\NotBlank(groups: ['registration', 'quick_registration'])]
    private $category;

    #[ORM\Column(type: 'string', length: 255, options: ['default' => 'Actif'], nullable: true)]
    private $state;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: '8', groups: ['registration'])]
    private $password;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'boolean', nullable: false)]
    private $activated;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $dateActivation;

    #[ORM\Column(type: 'boolean', options: ['default' => false], nullable: false)]
    private $expired;

    #[ORM\Column(type: 'boolean', options: ['default' => false], nullable: false)]
    private $deleted;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private $sponsor;

    #[Gedmo\TreeLeft]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $lft;

    #[Gedmo\TreeLevel]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $lvl;

    #[Gedmo\TreeRight]
    #[ORM\Column(type: 'integer', nullable: false)]
    private $rgt;

    #[Gedmo\TreeRoot]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'tree_root', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private $root;

    #[Gedmo\TreeParent]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', onDelete: 'cascade')]
    private $parent;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: true)]
    private $upline;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'parent')]
    #[ORM\OrderBy(['lvl' => 'ASC', 'upline' => 'ASC', 'position' => 'ASC'])]
    private $children;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $membership;

    /**
     * @var  Membership
     */
    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $nextMembership;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false], nullable: false)]
    private $toUpgrade;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(groups: ['registration', 'quick_registration', 'update_profile'])]
    private $mobilePhone;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false], nullable: true)]
    private $served;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Choice(choices: ['M.', 'Mme', 'Mlle', 'Dr', 'Hon.', 'Pr'], groups: ['registration', 'quick_registration', 'update_profile'])]
    private $title;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dateOfBirth;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Choice(choices: ['CNI', 'Passeport', 'Autres'], groups: ['registration', 'quick_registration', 'update_profile'])]
    private $documentType;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nextOfKin;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Choice(choices: ['F', 'M'], groups: ['registration', 'quick_registration', 'update_profile'])]
    private $gender;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private $codeDistributor;

    /**
     * @var UploadedFile
     */
    private $imageFile;

    /**
     *
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageName;

    /**
     *
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $grade;

    #[ORM\ManyToOne(targetEntity: Grade::class)]
    private $userGrade;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $isConcernedByPromo;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $createdBy;

    public function __construct()
    {
        $this->category = 'user';
        $this->state = "Actif";
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->username ?? '';
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;
        return $this;
    }

    public function getCni(): ?int
    {
        return $this->cni;
    }

    public function setCni(?int $cni): self
    {
        $this->cni = $cni;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getEntryDate(): ?DateTimeInterface
    {
        return $this->entryDate;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_JTWC_USER';
        }

        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function __serialize(): array
    {
        return [
            $this->id,
            $this->username,
            $this->fullname,
            $this->position,
            $this->email,
            $this->password,
        ];
    }

    public function __unserialize(array $data): void
    {
        [
            $this->id,
            $this->username,
            $this->fullname,
            $this->position,
            $this->email,
            $this->password,
        ] = $data;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
    }

    private function isAdmin()
    {
        return in_array('ROLE_JTWC_ADMIN', $this->roles);
    }

    public function isActivated(): ?bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): self
    {
        $this->activated = $activated;

        return $this;
    }

    public function isExpired(): ?bool
    {
        return $this->expired;
    }

    public function setExpired(bool $expired): self
    {
        $this->expired = $expired;
        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;
        return $this;
    }

    public function toString(): string
    {
        return $this->getFullname();
    }

    public function isNew(): bool
    {
        // TODO: Implement isNew() method.
        return is_null($this->id);
    }

    public function getUpline(): ?self
    {
        return $this->upline;
    }

    public function setUpline(?self $upline): self
    {
        $this->upline = $upline;
        return $this;
    }

    public function getSponsor(): ?self
    {
        return $this->sponsor;
    }

    public function setSponsor(?self $sponsor): self
    {
        $this->sponsor = $sponsor;
        return $this;
    }

    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function setStatusAccount(): void
    {
        $this->activated = false;
        $this->expired = false;
        $this->deleted = false;
        $this->toUpgrade = false;
        $this->served = false;
        $this->entryDate = new DateTime(
            "now",
            new DateTimeZone("Africa/Douala")
        );
    }

    /**
     * @param DateTime $dateTime
     * @return User
     */
    public function setEntryDate(DateTime $dateTime): self
    {
        $this->entryDate = $dateTime;
        return $this;
    }

    public function getLft(): ?int
    {
        return $this->lft;
    }

    public function setLft(int $lft): self
    {
        $this->lft = $lft;

        return $this;
    }

    public function getRgt(): ?int
    {
        return $this->rgt;
    }

    public function setRgt(int $rgt): self
    {
        $this->rgt = $rgt;

        return $this;
    }

    public function getActivated(): ?bool
    {
        return $this->activated;
    }

    public function getExpired(): ?bool
    {
        return $this->expired;
    }

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function getRoot(): ?User
    {
        return $this->root;
    }

    public function setRoot(?User $root): self
    {
        $this->root = $root;
        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(Membership $membership): self
    {
        $this->membership = $membership;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateActivation(): ?DateTime
    {
        return $this->dateActivation;
    }

    /**
     * @param DateTimeInterface $dateActivation
     * @return User
     */
    public function setDateActivation(DateTimeInterface $dateActivation): self
    {
        $this->dateActivation = $dateActivation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isServed(): ?bool
    {
        return $this->served;
    }

    /**
     * @param bool $served
     */
    public function setServed(bool $served): void
    {
        $this->served = $served;
    }

    /**
     * @return Membership
     */
    public function getNextMembership(): ?Membership
    {
        return $this->nextMembership;
    }

    /**
     * @param Membership $nextMembership
     * @return User
     */
    public function setNextMembership(?Membership $nextMembership): self
    {
        $this->nextMembership = $nextMembership;
        return $this;
    }

    /**
     * @return bool
     */
    public function isToUpgrade(): ?bool
    {
        return $this->toUpgrade;
    }

    /**
     * @param bool $toUpgrade
     * @return User
     */
    public function setToUpgrade(bool $toUpgrade): self
    {
        $this->toUpgrade = $toUpgrade;
        return $this;
    }

    public function __toString(): string
    {
        return $this->fullname;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDateOfBirth(): ?DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getDocumentType(): ?string
    {
        return $this->documentType;
    }

    public function setDocumentType(?string $documentType): self
    {
        $this->documentType = $documentType;

        return $this;
    }

    public function getNextOfKin(): ?string
    {
        return $this->nextOfKin;
    }

    public function setNextOfKin(?string $nextOfKin): self
    {
        $this->nextOfKin = $nextOfKin;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     *
     * @param UploadedFile $imageFile
     * @return User
     */
    public function setImageFile(?UploadedFile $imageFile = null): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function getImageFile(): ?UploadedFile
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @return string
     */
    public function getCategory(): ?string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return User
     */
    public function setCategory($category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function userUpdatedAt(): void
    {
        $this->updatedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): self
    {
        $this->grade = $grade;
        return $this;
    }

    public function getUserGrade(): ?Grade
    {
        return $this->userGrade;
    }

    public function setUserGrade(?Grade $userGrade): self
    {
        $this->userGrade = $userGrade;
        return $this;
    }

    /**
     * @return User
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param User $parent
     * @return User
     */
    public function setParent($parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTime $updatedAt
     * @return User
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getSpecificDirectory(string $baseDirectory, string $type = null): string
    {
        if (!is_dir($baseDirectory."/users")) {
            mkdir($baseDirectory."/users", 0644);
        }

        return $baseDirectory."/users";
    }

    /**
     * @param null $type
     * @return UploadedFile|null
     */
    public function getFile(string $type = null): ?UploadedFile
    {
        return $this->getImageFile();
    }

    public function getIsConcernedByPromo(): ?bool
    {
        return $this->isConcernedByPromo;
    }

    public function setIsConcernedByPromo(?bool $isConcernedByPromo): self
    {
        $this->isConcernedByPromo = $isConcernedByPromo;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodeDistributor(): ?string
    {
        return $this->codeDistributor;
    }

    public function setCodeDistributor(?string $codeDistributor): self
    {
        $this->codeDistributor = $codeDistributor;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $user): self
    {
        $this->createdBy = $user;
        return $this;
    }
}
