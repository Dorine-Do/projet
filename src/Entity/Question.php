<?php

namespace App\Entity;

use App\Entity\Enum\Difficulty;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $wording;

    #[ORM\Column(type: 'boolean')]
    private $isMandatory;

    #[ORM\Column(type: 'boolean')]
    private $isOfficial;

    #[ORM\Column(type: 'smallint')]
    private $difficulty;

    #[ORM\Column(type: 'boolean')]
    private $isMultiple;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\Column(type: 'boolean')]
    private $isEnabled;

    #[ORM\Column(type: 'text', nullable: true)]
    private $explanation;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Proposal::class, cascade: ['persist', 'remove'])]
    private $proposals;

    #[ORM\ManyToMany(targetEntity: Qcm::class, mappedBy: 'questions')]
    private $qcms;

    #[ORM\ManyToOne(targetEntity: Instructor::class, inversedBy: 'questions')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
        $this->qcms = new ArrayCollection();
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

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): self
    {
        $this->wording = $wording;

        return $this;
    }

    public function getIsMandatory(): ?bool
    {
        return $this->isMandatory;
    }

    public function setIsMandatory(bool $isMandatory): self
    {
        $this->isMandatory = $isMandatory;

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

    public function getDifficulty(): int|Difficulty
    {
        return match($this->difficulty) {
            1 => Difficulty::Easy,
            2 => Difficulty::Medium,
            3 => Difficulty::Difficult,
            default => Difficulty::Medium
        };
    }

    /**
     * Difficulty MUST be of type Entity\Enum\Difficulty because of CreateQuestionType.php
     * @param Difficulty|int $difficulty
     * @return $this
     */
    public function setDifficulty(Difficulty|int $difficulty): self
    {
        if (gettype($difficulty) == 'integer' )
            $this->difficulty = $difficulty;
        else
            $this->difficulty = $difficulty->value;

        return $this;
    }

    public function getIsMultiple(): ?bool
    {
        return $this->isMultiple;
    }

    public function setIsMultiple(bool $isMultiple): self
    {
        $this->isMultiple = $isMultiple;

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

    public function isEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): self
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    public function getExplanation(): ?string
    {
        return $this->explanation;
    }

    public function setExplanation(string $explanation): self
    {
        $this->explanation = $explanation;

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
     * @return Collection<int, Proposal>
     */
    public function getProposals(): Collection
    {
        return $this->proposals;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposals->contains($proposal)) {
            $this->proposals[] = $proposal;
            $proposal->setQuestion($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposals->removeElement($proposal)) {
            // set the owning side to null (unless already changed)
            if ($proposal->getQuestion() === $this) {
                $proposal->setQuestion(null);
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
            $qcm->addQuestion($this);
        }

        return $this;
    }

    public function removeQcm(Qcm $qcm): self
    {
        if ($this->qcms->removeElement($qcm)) {
            $qcm->removeQuestion($this);
        }

        return $this;
    }

    public function getAuthor(): ?Instructor
    {
        return $this->author;
    }

    public function setAuthor(?Instructor $author): self
    {
        $this->author = $author;

        return $this;
    }
}
