<?php

namespace App\Entity\Main;

use App\Repository\BadgesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgesRepository::class)]
class Badges
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $moduleGroupName = null;

    #[ORM\Column(length: 255)]
    private ?string $imgFile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleGroupName(): ?string
    {
        return $this->moduleGroupName;
    }

    public function setModuleGroupName(string $moduleGroupName): self
    {
        $this->moduleGroupName = $moduleGroupName;

        return $this;
    }

    public function getImgFile(): ?string
    {
        return $this->imgFile;
    }

    public function setImgFile(string $imgFile): self
    {
        $this->imgFile = $imgFile;

        return $this;
    }
}
