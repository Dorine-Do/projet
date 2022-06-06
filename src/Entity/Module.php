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
    private $link_session_module;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Question::class)]
    private $question;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkModuleQcm::class)]
    private $link_module_qcm;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    public function __construct()
    {
        $this->link_instructor_module = new ArrayCollection();
        $this->link_session_module = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->created_at = new \DateTime();
        $this->link_module_qcm = new ArrayCollection();
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
    public function getLinkSessionModule(): Collection
    {
        return $this->link_session_module;
    }

    public function addLinkSessionModule(LinkSessionModule $linkSessionModule): self
    {
        if (!$this->link_session_module->contains($linkSessionModule)) {
            $this->link_session_module[] = $linkSessionModule;
            $linkSessionModule->setModule($this);
        }

        return $this;
    }

    public function removeLinkSessionModule(LinkSessionModule $linkSessionModule): self
    {
        if ($this->link_session_module->removeElement($linkSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionModule->getModule() === $this) {
                $linkSessionModule->setModule(null);
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

    public function getLinkModuleQcm(): ?LinkModuleQcm
    {
        return $this->linkModuleQcm;
    }

    public function setLinkModuleQcm(?LinkModuleQcm $linkModuleQcm): self
    {
        $this->linkModuleQcm = $linkModuleQcm;

        return $this;
    }

    public function addLinkModuleQcm(LinkModuleQcm $linkModuleQcm): self
    {
        if (!$this->link_module_qcm->contains($linkModuleQcm)) {
            $this->link_module_qcm[] = $linkModuleQcm;
            $linkModuleQcm->setModule($this);
        }

        return $this;
    }

    public function removeLinkModuleQcm(LinkModuleQcm $linkModuleQcm): self
    {
        if ($this->link_module_qcm->removeElement($linkModuleQcm)) {
            // set the owning side to null (unless already changed)
            if ($linkModuleQcm->getModule() === $this) {
                $linkModuleQcm->setModule(null);
            }
        }

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
}
