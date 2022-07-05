<?php

namespace App\Entity;

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

    #[ORM\Column(type: 'boolean')]
    private $number_of_weeks;

    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $badges;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: LinkSessionModule::class)]
    private $link_session_module;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: Question::class)]
    private $question;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\ManyToMany(targetEntity: Instructor::class, inversedBy: 'modules')]
    private $instructors;

    #[ORM\ManyToMany(targetEntity: Qcm::class, inversedBy: 'modules')]
    private $qcms;

    public function __construct()
    {
        $this->instructors = new ArrayCollection();
        $this->link_session_module = new ArrayCollection();
        $this->question = new ArrayCollection();
        $this->qcms = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(){
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function setUpdateAtValue(){
        $this->updated_at = new \DateTime();
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

    public function __toString(): string{
        return $this->getTitle();
    }

    /**
     * @return Collection<int, Instructor>
     */
    public function getInstructors(): Collection
    {
        return $this->instructors;
    }

    public function addInstructor(Instructor $instructor): self
    {
        if (!$this->instructors->contains($instructor)) {
            $this->instructors[] = $instructor;
        }

        return $this;
    }

    public function removeInstructor(Instructor $instructor): self
    {
        $this->instructors->removeElement($instructor);

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
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        $this->qcms->removeElement($qcm);

        return $this;
    }

}
