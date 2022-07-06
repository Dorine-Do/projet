<?php

namespace App\Entity;

use App\Entity\Enum\Difficulty;
use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_author;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text')]
    private $wording;

    #[ORM\Column(type: 'boolean')]
    private $is_mandatory;

    #[ORM\Column(type: 'boolean')]
    private $is_official;

    #[ORM\Column(type: "string", enumType: Difficulty::class)]
    private $difficulty;

    #[ORM\Column(type: 'string', length: 255)]
    private $response_type;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'question')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Proposal::class, cascade:["persist", "remove"])]
    private $proposals;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    #[ORM\ManyToMany(targetEntity: Qcm::class, mappedBy: 'questions')]
    private $qcms;

    public function __construct()
    {
        $this->proposals = new ArrayCollection();
        $this->difficulty = Difficulty::Medium;
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

    public function getIdAuthor(): ?int
    {
        return $this->id_author;
    }

    public function setIdAuthor(?int $id_author): self
    {
        $this->id_author = $id_author;

        return $this;
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
        return $this->is_mandatory;
    }

    public function setIsMandatory(bool $is_mandatory): self
    {
        $this->is_mandatory = $is_mandatory;

        return $this;
    }

    public function getIsOfficial(): ?bool
    {
        return $this->is_official;
    }

    public function setIsOfficial(bool $is_official): self
    {
        $this->is_official = $is_official;

        return $this;
    }

    public function getDifficulty(): Difficulty
    {
        return $this->difficulty;
    }

    public function setDifficulty(Difficulty $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getResponseType(): ?string
    {
        return $this->response_type;
    }

    public function setResponseType(string $response_type): self
    {
        $this->response_type = $response_type;

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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

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
}
