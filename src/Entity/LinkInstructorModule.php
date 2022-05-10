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
}
