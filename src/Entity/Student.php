<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $id_moodle;

    #[ORM\Column(type: 'string', length: 150)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 150)]
    private $last_name;

    #[ORM\Column(type: 'datetime')]
    private $birth_date;

    #[ORM\Column(type: 'json')]
    private $badges = [];

    #[ORM\Column(type: 'string', length: 45)]
    private $mail_3wa;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updated_at;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: LinkSessionStudent::class)]
    private $link_session_student;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Result::class)]
    private $results;

    #[ORM\ManyToMany(targetEntity: QcmInstance::class, inversedBy: 'students')]
    private $qcmInstances;

    public function __construct()
    {
        $this->link_session_student = new ArrayCollection();
        $this->results = new ArrayCollection();
        $this->qcmInstances = new ArrayCollection();
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

    public function getIdModule(): ?int
    {
        return $this->id_moodle;
    }

    public function setIdModule(int $id_moodle): self
    {
        $this->id_moodle = $id_moodle;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getBadges(): ?array
    {
        return $this->badges;
    }

    public function setBadges(array $badges): self
    {
        $this->badges = $badges;

        return $this;
    }

    public function getMail3wa(): ?string
    {
        return $this->mail_3wa;
    }

    public function setMail3wa(string $mail_3wa): self
    {
        $this->mail_3wa = $mail_3wa;

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
     * @return Collection<int, LinkSessionStudent>
     */
    public function getLinkSessionStudent(): Collection
    {
        return $this->link_session_student;
    }

    public function addLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if (!$this->link_session_student->contains($linkSessionStudent)) {
            $this->link_session_student[] = $linkSessionStudent;
            $linkSessionStudent->setStudent($this);
        }

        return $this;
    }

    public function removeLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if ($this->link_session_student->removeElement($linkSessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionStudent->getStudent() === $this) {
                $linkSessionStudent->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Result>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setStudent($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getStudent() === $this) {
                $result->setStudent(null);
            }
        }

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
        }

        return $this;
    }

    public function removeQcmInstance(QcmInstance $qcmInstance): self
    {
        $this->qcmInstances->removeElement($qcmInstance);

        return $this;
    }
}
