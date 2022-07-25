<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    #[ORM\Column(type: 'smallint')]
    private $schoolYear;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    private $updatedAt;

    #[ORM\ManyToMany(targetEntity: Module::class, inversedBy: 'sessions')]
    private $modules;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkSessionStudent::class)]
    private $linksSessionStudent;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: LinkInstructorSessionModule::class)]
    private $linksInstructorSessionModule;

    public function __construct()
    {
        $this->modules = new ArrayCollection();
        $this->linksSessionStudent = new ArrayCollection();
        $this->linksInstructorSessionModule = new ArrayCollection();
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
        $this->name = $name;

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
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules[] = $module;
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        $this->modules->removeElement($module);

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


}
