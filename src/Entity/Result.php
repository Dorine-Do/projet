<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $student_id;

    #[ORM\Column(type: 'integer')]
    private $qcm_instance_id;

    #[ORM\Column(type: 'string', length: 255)]
    private $level;

    #[ORM\Column(type: 'json')]
    private $answers = [];

    #[ORM\Column(type: 'float')]
    private $total_score;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private $instructor_comment;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private $student_comment;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\ManyToOne(targetEntity: QcmInstance::class, inversedBy: 'result')]
    #[ORM\JoinColumn(nullable: false)]
    private $qcmInstance;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'results')]
    #[ORM\JoinColumn(nullable: false)]
    private $student;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudentId(): ?int
    {
        return $this->student_id;
    }

    public function setStudentId(int $student_id): self
    {
        $this->student_id = $student_id;

        return $this;
    }

    public function getQcmInstance(): ?int
    {
        return $this->qcm_instance;
    }

    public function setQcmInstance(int $qcm_instance): self
    {
        $this->qcm_instance = $qcm_instance;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;

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

    public function getTotalScore(): ?float
    {
        return $this->total_score;
    }

    public function setTotalScore(float $total_score): self
    {
        $this->total_score = $total_score;

        return $this;
    }

    public function getInstructorComment(): ?string
    {
        return $this->instructor_comment;
    }

    public function setInstructorComment(?string $instructor_comment): self
    {
        $this->instructor_comment = $instructor_comment;

        return $this;
    }

    public function getStudentComment(): ?string
    {
        return $this->student_comment;
    }

    public function setStudentComment(?string $student_comment): self
    {
        $this->student_comment = $student_comment;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): self
    {
        $this->student = $student;

        return $this;
    }
}
