<?php

namespace App\Entity;

use App\Repository\LinkSessionModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkSessionModuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class LinkSessionModule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $start_date;

    #[ORM\Column(type: 'datetime')]
    private $end_date;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'link_session_module')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\ManyToOne(targetEntity: Session::class, inversedBy: 'link_session_module')]
    #[ORM\JoinColumn(nullable: false)]
    private $session;

    public function __construct()
    {
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

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): self
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): self
    {
        $this->end_date = $end_date;

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
