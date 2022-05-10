<?php

namespace App\Entity;

use App\Repository\LinkInstructorClassRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkInstructorClassRepository::class)]
class LinkInstructorClass
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $instructor_id;

    #[ORM\Column(type: 'integer')]
    private $class_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstructorId(): ?int
    {
        return $this->instructor_id;
    }

    public function setInstructorId(int $instructor_id): self
    {
        $this->instructor_id = $instructor_id;

        return $this;
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
}
