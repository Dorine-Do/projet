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

    #[ORM\Column(type: 'integer')]
    private $question_id;

    #[ORM\Column(type: 'integer')]
    private $qcm_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionId(): ?int
    {
        return $this->question_id;
    }

    public function setQuestionId(int $question_id): self
    {
        $this->question_id = $question_id;

        return $this;
    }

    public function getQcmId(): ?int
    {
        return $this->qcm_id;
    }

    public function setQcmId(int $qcm_id): self
    {
        $this->qcm_id = $qcm_id;

        return $this;
    }
}
