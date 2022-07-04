<?php

namespace App\Entity;

use App\Repository\QcmInstanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QcmInstanceRepository::class)]
class QcmInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'json')]
    private $questions_answers = [];

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    #[ORM\Column(type: 'string', length: 75)]
    private $name;

    #[ORM\Column(type: 'datetime')]
    private $release_date;

    #[ORM\Column(type: 'datetime')]
    private $end_date;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\ManyToOne(targetEntity: Qcm::class, inversedBy: 'qcm_instance')]
    #[ORM\JoinColumn(nullable: false)]
    private $qcm;

    #[ORM\OneToMany(mappedBy: 'qcmInstance', targetEntity: Result::class)]
    private $result;

    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'qcmInstances')]
    private $students;

    public function __construct()
    {
        $this->result = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->students = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionAnswers(): ?array
    {
        return $this->question_answers;
    }

    public function setQuestionAnswers(array $question_answers): self
    {
        $this->question_answers = $question_answers;

        return $this;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->release_date;
    }

    public function setReleaseDate(\DateTimeInterface $release_date): self
    {
        $this->release_date = $release_date;

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

    public function getQcm(): ?Qcm
    {
        return $this->qcm;
    }

    public function setQcm(?Qcm $qcm): self
    {
        $this->qcm = $qcm;

        return $this;
    }

    /**
     * @return Collection<int, Result>
     */
    public function getResult(): Collection
    {
        return $this->result;
    }

    public function addResult(Result $result): self
    {
        if (!$this->result->contains($result)) {
            $this->result[] = $result;
            $result->setQcmInstance($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->result->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getQcmInstance() === $this) {
                $result->setQcmInstance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Student>
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->addQcmInstance($this);
        }

        return $this;
    }

    public function removeStudent(Student $student): self
    {
        if ($this->students->removeElement($student)) {
            $student->removeQcmInstance($this);
        }

        return $this;
    }
}
