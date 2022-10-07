<?php

namespace App\Entity\Main;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    #[ORM\Column(type: 'string', length: 12, nullable: true)]
    private $phone;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: QcmInstance::class)]
    private $qcmInstances;

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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
