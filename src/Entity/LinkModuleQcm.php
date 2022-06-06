<?php

namespace App\Entity;

use App\Repository\LinkModuleQcmRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkModuleQcmRepository::class)]
class LinkModuleQcm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'link_module_qcm')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\ManyToOne(targetEntity: Qcm::class, inversedBy: 'link_module_qcm')]
    #[ORM\JoinColumn(nullable: false)]
    private $qcm;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getQcm(): ?Qcm
    {
        return $this->qcm;
    }

    public function setQcm(?Qcm $qcm): self
    {
        $this->qcm = $qcm;

        return $this;
    }

}
