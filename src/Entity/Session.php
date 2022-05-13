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

    #[ORM\Column(type: 'smallint')]
    private $school_year;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionStudent::class)]
    private $link_session_student;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkInstructorSession::class)]
    private $link_instructor_session;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionModule::class)]
    private $link_session_module;

    public function __construct()
    {
        $this->link_session_student = new ArrayCollection();
        $this->link_instructor_session = new ArrayCollection();
        $this->link_session_module = new ArrayCollection();
        $this->created_at = new \DateTime();
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
     * @return Collection<int, LinkInstructorSession>
     */
    public function getLinkInstructorSession(): Collection
    {
        return $this->link_instructor_session;
    }

    public function addLinkInstructorSession(LinkInstructorSession $linkInstructorSession): self
    {
        if (!$this->link_instructor_session->contains($linkInstructorSession)) {
            $this->link_instructor_session[] = $linkInstructorSession;
            $linkInstructorSession->setSession($this);
        }

        return $this;
    }

    public function removeLinkInstructorSession(LinkInstructorSession $linkInstructorSession): self
    {
        if ($this->link_instructor_session->removeElement($linkInstructorSession)) {
            // set the owning side to null (unless already changed)
            if ($linkInstructorSession->getSession() === $this) {
                $linkInstructorSession->setSession(null);
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
}
