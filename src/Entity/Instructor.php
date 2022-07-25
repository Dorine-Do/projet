<?php

namespace App\Entity;

use App\Repository\InstructorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: InstructorRepository::class)]
final class Instructor extends User
{
    #[ORM\Column(type: 'boolean')]
    private $isReferent;

    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $phone;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Qcm::class)]
    private $qcms;

    #[ORM\OneToMany(mappedBy: 'instructor', targetEntity: LinkInstructorSessionModule::class)]
    private $linksInstructorSessionModule;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Question::class)]
    private $questions;

    public function __construct()
    {
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
     * @return Collection<int, Qcm>
     */
    public function getQcms(): Collection
    {
        return $this->qcms;
    }

    public function addQcm(Qcm $qcm): self
    {
        if (!$this->qcms->contains($qcm)) {
            $this->qcms[] = $qcm;
            $qcm->setAuthor($this);
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        if ($this->qcms->removeElement($qcm)) {
            // set the owning side to null (unless already changed)
            if ($qcm->getAuthor() === $this) {
                $qcm->setAuthor(null);
            }
        }

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
}
