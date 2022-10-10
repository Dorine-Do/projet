<?php

namespace App\Entity\Main;

use App\Repository\LinkSessionStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkSessionStudentRepository::class)]
class LinkSessionStudent
{

    #[ORM\Column(type: 'boolean')]
    private $isEnabled;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'linkSessionStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'linkSessionStudents')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;


    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
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

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
