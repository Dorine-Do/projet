<?php

namespace App\Entity\Suivi;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $idMoodle = null;

    #[ORM\Column(nullable: true)]
    private ?int $idHubspot = null;

    #[ORM\Column(nullable: true)]
    private ?int $idCompagny = null;

    #[ORM\Column(length: 63)]
    private ?string $firstname = null;

    #[ORM\Column(length: 63)]
    private ?string $lastname = null;

    #[ORM\Column(length: 127)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(length: 63)]
    private ?string $username = null;

    #[ORM\Column(length: 31, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 7)]
    private ?string $shortcode = null;

    #[ORM\Column(length: 31)]
    private ?string $access = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMoodle(): ?int
    {
        return $this->idMoodle;
    }

    public function setIdMoodle(?int $idMoodle): self
    {
        $this->idMoodle = $idMoodle;

        return $this;
    }

    public function getIdHubspot(): ?int
    {
        return $this->idHubspot;
    }

    public function setIdHubspot(?int $idHubspot): self
    {
        $this->idHubspot = $idHubspot;

        return $this;
    }

    public function getIdCompagny(): ?int
    {
        return $this->idCompagny;
    }

    public function setIdCompagny(?int $idCompagny): self
    {
        $this->idCompagny = $idCompagny;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getShortcode(): ?string
    {
        return $this->shortcode;
    }

    public function setShortcode(string $shortcode): self
    {
        $this->shortcode = $shortcode;

        return $this;
    }

    public function getAccess(): ?string
    {
        return $this->access;
    }

    public function setAccess(string $access): self
    {
        $this->access = $access;

        return $this;
    }
}
