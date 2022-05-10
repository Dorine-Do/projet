<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $name;

    #[ORM\Column(type: 'boolean')]
    private $school_year;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkClassStudent::class)]
    private $link_class_student;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkInstructorClass::class)]
    private $link_instructor_class;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkClassModule::class)]
    private $link_class_module;

    public function __construct()
    {
        $this->link_class_student = new ArrayCollection();
        $this->link_instructor_class = new ArrayCollection();
        $this->link_class_module = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSchoolYear(): ?bool
    {
        return $this->school_year;
    }

    public function setSchoolYear(bool $school_year): self
    {
        $this->school_year = $school_year;

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
     * @return Collection<int, LinkClassStudent>
     */
    public function getLinkClassStudent(): Collection
    {
        return $this->link_class_student;
    }

    public function addLinkClassStudent(LinkClassStudent $linkClassStudent): self
    {
        if (!$this->link_class_student->contains($linkClassStudent)) {
            $this->link_class_student[] = $linkClassStudent;
            $linkClassStudent->setSession($this);
        }

        return $this;
    }

    public function removeLinkClassStudent(LinkClassStudent $linkClassStudent): self
    {
        if ($this->link_class_student->removeElement($linkClassStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkClassStudent->getSession() === $this) {
                $linkClassStudent->setSession(null);
            }
        }

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
            $linkInstructorClass->setSession($this);
        }

        return $this;
    }

    public function removeLinkInstructorClass(LinkInstructorClass $linkInstructorClass): self
    {
        if ($this->link_instructor_class->removeElement($linkInstructorClass)) {
            // set the owning side to null (unless already changed)
            if ($linkInstructorClass->getSession() === $this) {
                $linkInstructorClass->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkClassModule>
     */
    public function getLinkClassModule(): Collection
    {
        return $this->link_class_module;
    }

    public function addLinkClassModule(LinkClassModule $linkClassModule): self
    {
        if (!$this->link_class_module->contains($linkClassModule)) {
            $this->link_class_module[] = $linkClassModule;
            $linkClassModule->setSession($this);
        }

        return $this;
    }

    public function removeLinkClassModule(LinkClassModule $linkClassModule): self
    {
        if ($this->link_class_module->removeElement($linkClassModule)) {
            // set the owning side to null (unless already changed)
            if ($linkClassModule->getSession() === $this) {
                $linkClassModule->setSession(null);
            }
        }

        return $this;
    }
}
