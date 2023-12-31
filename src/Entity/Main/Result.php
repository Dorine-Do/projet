<?php

namespace App\Entity\Main;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user:read'])]
    private $id;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $submittedAt;

    #[ORM\Column(type: 'json')]
    private $answers = [];

    #[ORM\Column(type: 'smallint')]
    #[Groups(['user:read'])]
    private $score;

    #[ORM\Column(type: 'text', nullable: true)]
    private $studentComment;

    #[ORM\Column(type: 'text', nullable: true)]
    private $instructorComment;

    #[ORM\Column(type: 'boolean')]
    private bool $isFirstTry;

    #[ORM\OneToOne(inversedBy: 'result', targetEntity: QcmInstance::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $qcmInstance;

    #[ORM\Column(type: 'smallint', nullable: true)]
    private $level;

    #[ORM\PrePersist]
    public function setSubmitedAtValue():void
    {
        $this->submittedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubmittedAt(): ?\DateTimeInterface
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeInterface $submittedAt): self
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    public function getAnswers(): ?array
    {
        return $this->answers;
    }

    public function setAnswers(array $answers): self
    {
        $this->answers = $answers;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getStudentComment(): ?string
    {
        return $this->studentComment;
    }

    public function setStudentComment(?string $studentComment): self
    {
        $this->studentComment = $studentComment;

        return $this;
    }

    public function getInstructorComment(): ?string
    {
        return $this->instructorComment;
    }

    public function setInstructorComment(?string $instructorComment): self
    {
        $this->instructorComment = $instructorComment;

        return $this;
    }

    public function isFirstTry(): ?bool
    {
        return $this->isFirstTry;
    }

    public function setIsFirstTry(bool $isFirstTry): self
    {
        $this->isFirstTry = $isFirstTry;

        return $this;
    }

    public function getQcmInstance(): ?QcmInstance
    {
        return $this->qcmInstance;
    }

    public function setQcmInstance(QcmInstance $qcmInstance): self
    {
        $this->qcmInstance = $qcmInstance;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): self
    {
        $this->level = $level;

        return $this;
    }
}
