<?php

namespace App\Entity;

use App\Repository\LinClassStudentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkClassStudentRepository::class)]
class LinkClassStudent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $class_id;

    #[ORM\Column(type: 'integer')]
    private $student_id;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'link_class_student')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'link_class_student')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClassId(): ?int
    {
        return $this->class_id;
    }

    public function setClassId(int $class_id): self
    {
        $this->class_id = $class_id;

        return $this;
    }

    public function getStudentId(): ?int
    {
        return $this->student_id;
    }

    public function setStudentId(int $student_id): self
    {
        $this->student_id = $student_id;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

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

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(?Session $session): self
    {
        $this->session = $session;

        return $this;
    }
}
