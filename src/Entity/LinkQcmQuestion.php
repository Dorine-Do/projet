<?php

namespace App\Entity;

use App\Repository\LinkQcmQuestionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LinkQcmQuestionRepository::class)]
class LinkQcmQuestion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Question::class, inversedBy: 'link_qcm_question')]
    #[ORM\JoinColumn(nullable: false)]
    private $question;

    #[ORM\ManyToOne(targetEntity: Qcm::class, inversedBy: 'link_qcm_question')]
    #[ORM\JoinColumn(nullable: false)]
    private $qcm;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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
