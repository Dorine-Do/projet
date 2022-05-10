<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $id_module;

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

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: LinkClassStudent::class)]
    private $link_class_student;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Result::class)]
    private $results;

    public function __construct()
    {
        $this->link_class_student = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdModule(): ?int
    {
        return $this->id_module;
    }

    public function setIdModule(int $id_module): self
    {
        $this->id_module = $id_module;

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

    public function getUpdatedTime(): ?\DateTimeInterface
    {
        return $this->updated_time;
    }

    public function setUpdatedTime(?\DateTimeInterface $updated_time): self
    {
        $this->updated_time = $updated_time;

        return $this;
    }

    /**
     * @return Collection<int, LinkClassStudent>
     */
    public function getLinkClassStudent(): Collection
    {
        return $this->link_class_student;
    }

    public function addLinkClassStudent(LinkClassStudent $linkClassStudent): self
    {
        if (!$this->link_class_student->contains($linkClassStudent)) {
            $this->link_class_student[] = $linkClassStudent;
            $linkClassStudent->setStudent($this);
        }

        return $this;
    }

    public function removeLinkClassStudent(LinkClassStudent $linkClassStudent): self
    {
        if ($this->link_class_student->removeElement($linkClassStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkClassStudent->getStudent() === $this) {
                $linkClassStudent->setStudent(null);
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
}
