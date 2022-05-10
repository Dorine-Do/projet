<?php

namespace App\Entity;

use App\Repository\QcmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QcmRepository::class)]
class Qcm
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $module_id;

    #[ORM\Column(type: 'json')]
    private $questions_answers = [];

    #[ORM\Column(type: 'boolean')]
    private $enabled;

    #[ORM\Column(type: 'string', length: 75)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $difficulty;

    #[ORM\Column(type: 'boolean')]
    private $is_official;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $author_id;

    #[ORM\Column(type: 'boolean')]
    private $public;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'qcm', targetEntity: LinkQcmQuestion::class)]
    private $link_qcm_question;

    #[ORM\ManyToOne(targetEntity: Module::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $module;

    #[ORM\OneToMany(mappedBy: 'qcm', targetEntity: QcmInstance::class)]
    private $qcm_instance;

    public function __construct()
    {
        $this->link_qcm_question = new ArrayCollection();
        $this->qcm_instance = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleId(): ?int
    {
        return $this->module_id;
    }

    public function setModuleId(int $module_id): self
    {
        $this->module_id = $module_id;

        return $this;
    }

    public function getQuestionsAnswers(): ?array
    {
        return $this->questions_answers;
    }

    public function setQuestionsAnswers(array $questions_answers): self
    {
        $this->questions_answers = $questions_answers;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getIsOfficial(): ?bool
    {
        return $this->is_official;
    }

    public function setIsOfficial(bool $is_official): self
    {
        $this->is_official = $is_official;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    public function setAuthorId(?int $author_id): self
    {
        $this->author_id = $author_id;

        return $this;
    }

    public function getPublic(): ?bool
    {
        return $this->public;
    }

    public function setPublic(bool $public): self
    {
        $this->public = $public;

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
            $linkQcmQuestion->setQcm($this);
        }

        return $this;
    }

    public function removeLinkQcmQuestion(LinkQcmQuestion $linkQcmQuestion): self
    {
        if ($this->link_qcm_question->removeElement($linkQcmQuestion)) {
            // set the owning side to null (unless already changed)
            if ($linkQcmQuestion->getQcm() === $this) {
                $linkQcmQuestion->setQcm(null);
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
     * @return Collection<int, QcmInstance>
     */
    public function getQcmInstance(): Collection
    {
        return $this->qcm_instance;
    }

    public function addQcmInstance(QcmInstance $qcmInstance): self
    {
        if (!$this->qcm_instance->contains($qcmInstance)) {
            $this->qcm_instance[] = $qcmInstance;
            $qcmInstance->setQcm($this);
        }

        return $this;
    }

    public function removeQcmInstance(QcmInstance $qcmInstance): self
    {
        if ($this->qcm_instance->removeElement($qcmInstance)) {
            // set the owning side to null (unless already changed)
            if ($qcmInstance->getQcm() === $this) {
                $qcmInstance->setQcm(null);
            }
        }

        return $this;
    }
}
