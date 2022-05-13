<?php

namespace App\Entity;

use App\Repository\LinkInstructorClassRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkInstructorClassRepository::class)]
class LinkInstructorSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'link_instructor_session')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    #[ORM\ManyToOne(targetEntity: Instructor::class, inversedBy: 'link_instructor_session')]
    #[ORM\JoinColumn(nullable: false)]
    private $instructor;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getInstructor(): ?Instructor
    {
        return $this->instructor;
    }

    public function setInstructor(?Instructor $instructor): self
    {
        $this->instructor = $instructor;

        return $this;
    }
}
