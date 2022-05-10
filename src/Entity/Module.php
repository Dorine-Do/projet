<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $number_of_weeks;

    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $badges;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOfWeeks(): ?bool
    {
        return $this->number_of_weeks;
    }

    public function setNumberOfWeeks(bool $number_of_weeks): self
    {
        $this->number_of_weeks = $number_of_weeks;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getBadges(): ?string
    {
        return $this->badges;
    }

    public function setBadges(?string $badges): self
    {
        $this->badges = $badges;

        return $this;
    }
}
