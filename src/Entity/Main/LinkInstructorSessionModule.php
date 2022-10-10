<?php

namespace App\Entity\Main;

use App\Repository\LinkInstructorSessionModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkInstructorSessionModuleRepository::class)]
class LinkInstructorSessionModule
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Instructor::class, cascade: ["persist"], inversedBy: 'linksInstructorSessionModule')]
    #[ORM\JoinColumn(nullable: false)]
    private $instructor;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Session::class, cascade: ["persist"], inversedBy: 'linksInstructorSessionModule')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Module::class, cascade: ["persist"], inversedBy: 'linksInstructorSessionModule')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;


    public function getInstructor(): ?Instructor
    {
        return $this->instructor;
    }

    public function setInstructor(?Instructor $instructor): self
    {
        $this->instructor = $instructor;

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
