<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $id_author;

    #[ORM\Column(type: 'text')]
    private $wording;

    #[ORM\Column(type: 'boolean')]
    private $is_mandatory;

    #[ORM\Column(type: 'boolean')]
    private $is_official;

    #[ORM\Column(type: "string", enumType: Enum\Difficulty::class)]
    private Enum\Difficulty $difficulty;

    #[ORM\Column(type: 'string', length: 255)]
    private $response_type;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\ManyToOne(targetEntity: Module::class, inversedBy: 'question')]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: LinkQcmQuestion::class)]
    private $link_qcm_question;

    #[ORM\OneToMany(mappedBy: 'question', targetEntity: Proposal::class)]
    private $proposal;

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    public function __construct()
    {
        $this->link_qcm_question = new ArrayCollection();
        $this->proposal = new ArrayCollection();
        $this->difficulty = Enum\Difficulty::Medium;
        $this->created_at = new \DateTime();
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

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): self
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
     * @return Collection<int, LinkQcmQuestion>
     */
    public function getLinkQcmQuestion(): Collection
    {
        return $this->link_qcm_question;
    }

    public function addLinkQcmQuestion(LinkQcmQuestion $linkQcmQuestion): self
    {
        if (!$this->link_qcm_question->contains($linkQcmQuestion)) {
            $this->link_qcm_question[] = $linkQcmQuestion;
            $linkQcmQuestion->setQuestion($this);
        }

        return $this;
    }

    public function removeLinkQcmQuestion(LinkQcmQuestion $linkQcmQuestion): self
    {
        if ($this->link_qcm_question->removeElement($linkQcmQuestion)) {
            // set the owning side to null (unless already changed)
            if ($linkQcmQuestion->getQuestion() === $this) {
                $linkQcmQuestion->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Proposal>
     */
    public function getProposal(): Collection
    {
        return $this->proposal;
    }

    public function addProposal(Proposal $proposal): self
    {
        if (!$this->proposal->contains($proposal)) {
            $this->proposal[] = $proposal;
            $proposal->setQuestion($this);
        }

        return $this;
    }

    public function removeProposal(Proposal $proposal): self
    {
        if ($this->proposal->removeElement($proposal)) {
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
}
