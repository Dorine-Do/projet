<?php

namespace App\Entity;

use App\Repository\QcmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QcmRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Qcm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 75)]
    private $title;

    #[ORM\Column(type: 'smallint')]
    private $difficulty;

    #[ORM\Column(type: 'boolean')]
    private $isOfficial;

    #[ORM\Column(type: 'boolean')]
    private $isEnabled;

    #[ORM\Column(type: 'boolean')]
    private $isPublic;

    #[ORM\Column(type: 'json')]
    private $questionsCache = [];

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'qcm', targetEntity: QcmInstance::class)]
    private $qcmInstances;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'qcms')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'qcms')]
    #[ORM\JoinTable(name: "link_qcm_question")]
    private $questions;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'qcms')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    public function __construct()
    {
        $this->qcmInstances = new ArrayCollection();
        $this->questions = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDifficulty(): ?int
    {
        return $this->difficulty;
    }

    public function setDifficulty(int $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getIsOfficial(): ?bool
    {
        return $this->isOfficial;
    }

    public function setIsOfficial(bool $isOfficial): self
    {
        $this->isOfficial = $isOfficial;

        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): self
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getQuestionsCache(): ?array
    {
        return $this->questionsCache;
    }

    public function setQuestionsCache(array $questionsCache): self
    {
        $this->questionsCache = $questionsCache;

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
            $qcmInstance->setQcm($this);
        }

        return $this;
    }

    public function removeQcmInstance(QcmInstance $qcmInstance): self
    {
        if ($this->qcmInstances->removeElement($qcmInstance)) {
            // set the owning side to null (unless already changed)
            if ($qcmInstance->getQcm() === $this) {
                $qcmInstance->setQcm(null);
            }
        }

        return $this;
    }

    public function getModule(): ?Module
    {
        return $this->module;
    }

    public function setModule(?Module $module): self
    {
        $this->module = $module;

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
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        $this->questions->removeElement($question);

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
