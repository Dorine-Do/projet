<?php

namespace App\Entity\Main;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'smallint')]
    private $weeks;

    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Question::class)]
    private $questions;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Qcm::class)]
    private $qcms;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkInstructorSessionModule::class)]
    private $linksInstructorSessionModule;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkSessionModule::class)]
    private $linksSessionModule;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->qcms = new ArrayCollection();
        $this->linksInstructorSessionModule = new ArrayCollection();
        $this->linksSessionModule = new ArrayCollection();
    }

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

    public function getWeeks(): ?int
    {
        return $this->weeks;
    }

    public function setWeeks(int $weeks): self
    {
        $this->weeks = $weeks;

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
            $question->setModule($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getModule() === $this) {
                $question->setModule(null);
            }
        }

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
            $qcm->setModule($this);
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        if ($this->qcms->removeElement($qcm)) {
            // set the owning side to null (unless already changed)
            if ($qcm->getModule() === $this) {
                $qcm->setModule(null);
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
            $linksInstructorSessionModule->setModule($this);
        }

        return $this;
    }

    public function removeLinksInstructorSessionModule(LinkInstructorSessionModule $linksInstructorSessionModule): self
    {
        if ($this->linksInstructorSessionModule->removeElement($linksInstructorSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linksInstructorSessionModule->getModule() === $this) {
                $linksInstructorSessionModule->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkSessionModule>
     */
    public function getLinksSessionModule(): Collection
    {
        return $this->linksSessionModule;
    }

    public function addLinksSessionModule(LinkSessionModule $linksSessionModule): self
    {
        if (!$this->linksSessionModule->contains($linksSessionModule)) {
            $this->linksSessionModule[] = $linksSessionModule;
            $linksSessionModule->setModule($this);
        }

        return $this;
    }

    public function removeLinksSessionModule(LinkSessionModule $linksSessionModule): self
    {
        if ($this->linksSessionModule->removeElement($linksSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linksSessionModule->getModule() === $this) {
                $linksSessionModule->setModule(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle();
    }
}
