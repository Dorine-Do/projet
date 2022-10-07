<?php

namespace App\Entity\Main;

use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstructorRepository::class)]
class Instructor extends User
{
    #[ORM\Column(type: 'boolean')]
    #[Groups(['user:read'])]
    private $isReferent;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $phone;

    #[ORM\OneToMany(mappedBy: 'instructor', targetEntity: LinkInstructorSessionModule::class)]
    private $linksInstructorSessionModule;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Question::class)]
    private $questions;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: QcmInstance::class)]
    private $qcmInstances;

    public function __construct()
    {
        parent::__construct();
        $this->qcms = new ArrayCollection();
        $this->linksInstructorSessionModule = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function isReferent(): ?bool
    {
        return $this->isReferent;
    }

    public function setIsReferent(bool $isReferent): self
    {
        $this->isReferent = $isReferent;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection<int, LinkInstructorSessionModule>
     */
    public function getLinksInstructorSessionModule(): Collection
    {
        return $this->linksInstructorSessionModule;
    }

    public function addLinksInstructorSessionModule(LinkInstructorSessionModule $linksInstructorSessionModule): self
    {
        if (!$this->linksInstructorSessionModule->contains($linksInstructorSessionModule)) {
            $this->linksInstructorSessionModule[] = $linksInstructorSessionModule;
            $linksInstructorSessionModule->setInstructor($this);
        }

        return $this;
    }

    public function removeLinksInstructorSessionModule(LinkInstructorSessionModule $linksInstructorSessionModule): self
    {
        if ($this->linksInstructorSessionModule->removeElement($linksInstructorSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linksInstructorSessionModule->getInstructor() === $this) {
                $linksInstructorSessionModule->setInstructor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setAuthor($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getAuthor() === $this) {
                $question->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QcmInstance>
     */
    public function getQcmInstances(): Collection
    {
        return $this->qcmInstances;
    }

    public function addQcmInstance(QcmInstance $qcmInstance): self
    {
        if (!$this->qcmInstances->contains($qcmInstance)) {
            $this->qcmInstances[] = $qcmInstance;
            $qcmInstance->setStudent($this);
        }

        return $this;
    }

    public function removeQcmInstance(QcmInstance $qcmInstance): self
    {
        if ($this->qcmInstances->removeElement($qcmInstance)) {
            // set the owning side to null (unless already changed)
            if ($qcmInstance->getStudent() === $this) {
                $qcmInstance->setStudent(null);
            }
        }

        return $this;
    }
}
