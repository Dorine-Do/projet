<?php

namespace App\Entity;

use App\Repository\LinkInstructorModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkInstructorModuleRepository::class)]
class LinkInstructorModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $module_id;

    #[ORM\Column(type: 'integer')]
    private $instructor_id;

    #[ORM\ManyToOne(targetEntity: Instructor::class, inversedBy: 'link_instructor_module')]
    #[ORM\JoinColumn(nullable: false)]
    private $instructor;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'link_instructor_module')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleId(): ?int
    {
        return $this->module_id;
    }

    public function setModuleId(int $module_id): self
    {
        $this->module_id = $module_id;

        return $this;
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

    public function getInstructor(): ?Instructor
    {
        return $this->instructor;
    }

    public function setInstructor(?Instructor $instructor): self
    {
        $this->instructor = $instructor;

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

        return $this;
    }
}
