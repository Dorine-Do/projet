<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 10)]
    private $name;

    #[ORM\Column(type: 'smallint')]
    private $school_year;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionStudent::class)]
    private $link_session_student;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionModule::class)]
    private $link_session_module;

    #[ORM\ManyToMany(targetEntity: Instructor::class, inversedBy: 'sessions')]
    private $instructors;

    public function __construct()
    {
        $this->link_session_student = new ArrayCollection();
        $this->link_session_module = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(){
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdateAtValue(){
        $this->updated_at = new \DateTime();
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
     * @return Collection<int, LinkSessionStudent>
     */
    public function getLinkSessionStudent(): Collection
    {
        return $this->link_session_student;
    }

    public function addLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if (!$this->link_session_student->contains($linkSessionStudent)) {
            $this->link_session_student[] = $linkSessionStudent;
            $linkSessionStudent->setSession($this);
        }

        return $this;
    }

    public function removeLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if ($this->link_session_student->removeElement($linkSessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionStudent->getSession() === $this) {
                $linkSessionStudent->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkSessionModule>
     */
    public function getLinkSessionModule(): Collection
    {
        return $this->link_session_module;
    }

    public function addLinkSessionModule(LinkSessionModule $linkSessionModule): self
    {
        if (!$this->link_session_module->contains($linkSessionModule)) {
            $this->link_session_module[] = $linkSessionModule;
            $linkSessionModule->setSession($this);
        }

        return $this;
    }

    public function removeLinkSessionModule(LinkSessionModule $linkSessionModule): self
    {
        if ($this->link_session_module->removeElement($linkSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionModule->getSession() === $this) {
                $linkSessionModule->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Instructor>
     */
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function addInstructor(Instructor $instructor): self
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors[] = $instructor;
        }

        return $this;
    }

    public function removeInstructor(Instructor $instructor): self
    {
        $this->instructors->removeElement($instructor);

        return $this;
    }
}
