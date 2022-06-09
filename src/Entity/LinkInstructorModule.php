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

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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
