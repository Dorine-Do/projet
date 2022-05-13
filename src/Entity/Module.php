<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $number_of_weeks;

    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $badges;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkInstructorModule::class)]
    private $link_instructor_module;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkSessionModule::class)]
    private $link_class_module;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Question::class)]
    private $question;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Qcm::class)]
    private $qcm;

    public function __construct()
    {
        $this->link_instructor_module = new ArrayCollection();
        $this->link_class_module = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->qcm = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberOfWeeks(): ?bool
    {
        return $this->number_of_weeks;
    }

    public function setNumberOfWeeks(bool $number_of_weeks): self
    {
        $this->number_of_weeks = $number_of_weeks;

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

    public function getBadges(): ?string
    {
        return $this->badges;
    }

    public function setBadges(?string $badges): self
    {
        $this->badges = $badges;

        return $this;
    }

    /**
     * @return Collection<int, LinkInstructorModule>
     */
    public function getLinkInstructorModule(): Collection
    {
        return $this->link_instructor_module;
    }

    public function addLinkInstructorModule(LinkInstructorModule $linkInstructorModule): self
    {
        if (!$this->link_instructor_module->contains($linkInstructorModule)) {
            $this->link_instructor_module[] = $linkInstructorModule;
            $linkInstructorModule->setModule($this);
        }

        return $this;
    }

    public function removeLinkInstructorModule(LinkInstructorModule $linkInstructorModule): self
    {
        if ($this->link_instructor_module->removeElement($linkInstructorModule)) {
            // set the owning side to null (unless already changed)
            if ($linkInstructorModule->getModule() === $this) {
                $linkInstructorModule->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkSessionModule>
     */
    public function getLinkClassModule(): Collection
    {
        return $this->link_class_module;
    }

    public function addLinkClassModule(LinkSessionModule $linkClassModule): self
    {
        if (!$this->link_class_module->contains($linkClassModule)) {
            $this->link_class_module[] = $linkClassModule;
            $linkClassModule->setModule($this);
        }

        return $this;
    }

    public function removeLinkClassModule(LinkSessionModule $linkClassModule): self
    {
        if ($this->link_class_module->removeElement($linkClassModule)) {
            // set the owning side to null (unless already changed)
            if ($linkClassModule->getModule() === $this) {
                $linkClassModule->setModule(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestion(): Collection
    {
        return $this->question;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->question->contains($question)) {
            $this->question[] = $question;
            $question->setModule($this);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->question->removeElement($question)) {
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
    public function getQcm(): Collection
    {
        return $this->qcm;
    }

    public function addQcm(Qcm $qcm): self
    {
        if (!$this->qcm->contains($qcm)) {
            $this->qcm[] = $qcm;
            $qcm->setModule($this);
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        if ($this->qcm->removeElement($qcm)) {
            // set the owning side to null (unless already changed)
            if ($qcm->getModule() === $this) {
                $qcm->setModule(null);
            }
        }

        return $this;
    }
}
