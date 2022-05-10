<?php

namespace App\Entity;

use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructorRepository::class)]
class Instructor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id_tools;

    #[ORM\Column(type: 'string', length: 150)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 150)]
    private $last_name;

    #[ORM\Column(type: 'datetime')]
    private $birth_date;

    #[ORM\Column(type: 'string', length: 12)]
    private $phone_number;

    #[ORM\Column(type: 'string', length: 150)]
    private $email;

    #[ORM\Column(type: 'string', length: 50)]
    private $password;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'instructor', targetEntity: LinkInstructorClass::class)]
    private $link_instructor_class;

    #[ORM\OneToMany(mappedBy: 'instructor', targetEntity: LinkInstructorModule::class)]
    private $link_instructor_module;

    public function __construct()
    {
        $this->link_instructor_class = new ArrayCollection();
        $this->link_instructor_module = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id_tools;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): self
    {
        $this->phone_number = $phone_number;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, LinkInstructorClass>
     */
    public function getLinkInstructorClass(): Collection
    {
        return $this->link_instructor_class;
    }

    public function addLinkInstructorClass(LinkInstructorClass $linkInstructorClass): self
    {
        if (!$this->link_instructor_class->contains($linkInstructorClass)) {
            $this->link_instructor_class[] = $linkInstructorClass;
            $linkInstructorClass->setInstructor($this);
        }

        return $this;
    }

    public function removeLinkInstructorClass(LinkInstructorClass $linkInstructorClass): self
    {
        if ($this->link_instructor_class->removeElement($linkInstructorClass)) {
            // set the owning side to null (unless already changed)
            if ($linkInstructorClass->getInstructor() === $this) {
                $linkInstructorClass->setInstructor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkInstructorModule>
     */
    public function getLinkInstructorModule(): Collection
    {
        return $this->link_instructor_module;
    }

    public function addLinkInstructorModule(LinkInstructorModule $linkInstructorModule): self
    {
        if (!$this->link_instructor_module->contains($linkInstructorModule)) {
            $this->link_instructor_module[] = $linkInstructorModule;
            $linkInstructorModule->setInstructor($this);
        }

        return $this;
    }

    public function removeLinkInstructorModule(LinkInstructorModule $linkInstructorModule): self
    {
        if ($this->link_instructor_module->removeElement($linkInstructorModule)) {
            // set the owning side to null (unless already changed)
            if ($linkInstructorModule->getInstructor() === $this) {
                $linkInstructorModule->setInstructor(null);
            }
        }

        return $this;
    }
}
