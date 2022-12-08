<?php

namespace App\Entity\Main;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['session:read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['session:read'])]
    private $name;

    #[ORM\Column(type: 'smallint')]
    private $schoolYear;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private $updatedAt;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionStudent::class, cascade: ['persist'])]
    private $linksSessionStudent;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkInstructorSessionModule::class)]
    private $linksInstructorSessionModule;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionModule::class)]
    private $linksSessionModule;

    public function __construct()
    {
        $this->linksSessionStudent = new ArrayCollection();
        $this->linksInstructorSessionModule = new ArrayCollection();
        $this->linksSessionModule = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = strtoupper($name);

        return $this;
    }

    public function getSchoolYear(): ?int
    {
        return $this->schoolYear;
    }

    public function setSchoolYear(int $schoolYear): self
    {
        $this->schoolYear = $schoolYear;

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
            $linkSessionStudent->setSession($this);
        }

        return $this;
    }

    public function removeLinkSessionStudent(LinkSessionStudent $linkSessionStudent): self
    {
        if ($this->linksSessionStudent->removeElement($linkSessionStudent)) {
            // set the owning side to null (unless already changed)
            if ($linkSessionStudent->getSession() === $this) {
                $linkSessionStudent->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkInstructorSessionModule>
     */
    public function getLinksInstructorSessionModule(): Collection
    {
        return $this->linksInstructorSessionModule;
    }

    public function addLinksInstructorSessionModule(LinkInstructorSessionModule $linksInstructorSessionModule): self
    {
        if (!$this->linksInstructorSessionModule->contains($linksInstructorSessionModule)) {
            $this->linksInstructorSessionModule[] = $linksInstructorSessionModule;
            $linksInstructorSessionModule->setSession($this);
        }

        return $this;
    }

    public function removeLinksInstructorSessionModule(LinkInstructorSessionModule $linksInstructorSessionModule): self
    {
        if ($this->linksInstructorSessionModule->removeElement($linksInstructorSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linksInstructorSessionModule->getSession() === $this) {
                $linksInstructorSessionModule->setSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LinkSessionModule>
     */
    public function getLinksSessionModule(): Collection
    {
        return $this->linksSessionModule;
    }

    public function addLinksSessionModule(LinkSessionModule $linksSessionModule): self
    {
        if (!$this->linksSessionModule->contains($linksSessionModule)) {
            $this->linksSessionModule[] = $linksSessionModule;
            $linksSessionModule->setSession($this);
        }

        return $this;
    }

    public function removeLinksSessionModule(LinkSessionModule $linksSessionModule): self
    {
        if ($this->linksSessionModule->removeElement($linksSessionModule)) {
            // set the owning side to null (unless already changed)
            if ($linksSessionModule->getSession() === $this) {
                $linksSessionModule->setSession(null);
            }
        }

        return $this;
    }


}
