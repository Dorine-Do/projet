<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student extends User
{
    #[ORM\Column(type: 'json', nullable: true)]
    private $badges = [];

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: LinkSessionStudent::class)]
    private $linksSessionStudent;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: QcmInstance::class)]
    private $qcmInstances;

    public function __construct()
    {
        $this->linksSessionStudent = new ArrayCollection();
        $this->qcmInstances = new ArrayCollection();
    }

    public function getBadges(): ?array
    {
        return $this->badges;
    }

    public function setBadges(?array $badges): self
    {
        $this->badges = $badges;

        return $this;
    }

    /**
     * @return Collection<int, LinkSessionStudent>
     */
    public function getLinksSessionStudent(): Collection
    {
        return $this->linksSessionStudent;
    }

    public function addLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if (!$this->linksSessionStudent->contains($linkSessionStudent)) {
            $this->linksSessionStudent[] = $linkSessionStudent;
            $linkSessionStudent->setStudent($this);
        }

        return $this;
    }

    public function removeLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if ($this->linksSessionStudent->removeElement($linkSessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionStudent->getStudent() === $this) {
                $linkSessionStudent->setStudent(null);
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
            $qcmInstance->setStudent($this);
        }

        return $this;
    }

    public function removeQcmInstance(QcmInstance $qcmInstance): self
    {
        if ($this->qcmInstances->removeElement($qcmInstance)) {
            // set the owning side to null (unless already changed)
            if ($qcmInstance->getStudent() === $this) {
                $qcmInstance->setStudent(null);
            }
        }

        return $this;
    }
}
