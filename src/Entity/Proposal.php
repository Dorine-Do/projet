<?php

namespace App\Entity;

use App\Repository\ProposalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposalRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Proposal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $proposal;

    #[ORM\Column(type: 'boolean')]
    private $isCorrectAnswer;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'proposals')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\PrePersist]
    public function setCreatedAtValue():void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdateAtValue():void
    {
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposal(): ?string
    {
        return $this->proposal;
    }

    public function setProposal(string $proposal): self
    {
        $this->proposal = $proposal;

        return $this;
    }

    public function isIsCorrectAnswer(): ?bool
    {
        return $this->isCorrectAnswer;
    }

    public function setIsCorrectAnswer(bool $isCorrectAnswer): self
    {
        $this->isCorrectAnswer = $isCorrectAnswer;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;

        return $this;
    }
}
