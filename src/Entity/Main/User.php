<?php

namespace App\Entity\Main;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\DiscriminatorMap(["admin" => Admin::class, "instructor" => Instructor::class, "student" => Student::class])]
#[ORM\DiscriminatorColumn(name: "discr", type: "string")]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['user:read'])]
    private string $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['user:read'])]
    private $roles = [];

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups(['user:read'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 150)]
    #[Groups(['user:read'])]
    private $lastName;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['user:read'])]
    private $birthDate;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $moodleId;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $suiviId;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Groups(['user:read'])]
    private $updatedAt;

    #[ORM\PrePersist]
    public function setCreatedAtValue():void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdateAtValue():void
    {
        $this->updatedAt = new \DateTime();
    }

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Qcm::class)]
    private $qcms;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Cookie $cookie = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BugReport::class)]
    private Collection $bugReports;

    public function __construct()
    {
        $this->qcms = new ArrayCollection();
        $this->bugReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getMoodleId(): ?int
    {
        return $this->moodleId;
    }

    public function setMoodleId(int $moodleId): self
    {
        $this->moodleId = $moodleId;

        return $this;
    }

    public function setSuiviId(int $suiviId): self
    {
        $this->suiviId = $suiviId;

        return $this;
    }

    public function getSuiviId(): ?int
    {
        return $this->suiviId;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Qcm>
     */
    public function getQcms(): Collection
    {
        return $this->qcms;
    }

    public function addQcm(Qcm $qcm): self
    {
        if (!$this->qcms->contains($qcm)) {
            $this->qcms[] = $qcm;
            $qcm->setAuthor($this);
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        if ($this->qcms->removeElement($qcm)) {
            // set the owning side to null (unless already changed)
            if ($qcm->getAuthor() === $this) {
                $qcm->setAuthor(null);
            }
        }

        return $this;
    }

    public function getCookie(): ?Cookie
    {
        return $this->cookie;
    }

    public function setCookie(Cookie $cookie): self
    {
        // set the owning side of the relation if necessary
        if ($cookie->getUser() !== $this) {
            $cookie->setUser($this);
        }

        $this->cookie = $cookie;

        return $this;
    }

    /**
     * @return Collection<int, BugReport>
     */
    public function getBugReports(): Collection
    {
        return $this->bugReports;
    }

    public function addBugReport(BugReport $bugReport): self
    {
        if (!$this->bugReports->contains($bugReport)) {
            $this->bugReports->add($bugReport);
            $bugReport->setUser($this);
        }

        return $this;
    }

    public function removeBugReport(BugReport $bugReport): self
    {
        if ($this->bugReports->removeElement($bugReport)) {
            // set the owning side to null (unless already changed)
            if ($bugReport->getUser() === $this) {
                $bugReport->setUser(null);
            }
        }

        return $this;
    }
}
