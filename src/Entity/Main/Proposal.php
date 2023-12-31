<?php

namespace App\Entity\Main;

use App\Repository\ProposalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProposalRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Proposal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['question:read'])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups(['question:read'])]
    /*TODO mettre les constraint de longueur min et max type text*/
    private $wording;

    /*TODO mettre les constraint boolean*/
    #[ORM\Column(type: 'boolean')]
    #[Groups(['question:read'])]
    private $isCorrectAnswer;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
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

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getIsCorrectAnswer(): ?bool
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
