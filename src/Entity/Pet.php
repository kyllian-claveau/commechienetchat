<?php

namespace App\Entity;

use App\Repository\PetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    public const CAT_TYPE = "CAT_TYPE";
    public const DOG_TYPE = "DOG_TYPE";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;
    #[ORM\Column(type:"datetime")]
    private ?\DateTime $birthdayDate = null;

    #[ORM\Column(length: 255)]
    private ?string $breed = null;

    #[ORM\Column(length: 180)]
    private ?string $avatarPetFilename = null;
    private ?UploadedFile $avatarPetFile = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pets')]
    private $user;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;
        return $this;
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

    public function getAvatarPetFilename(): ?string
    {
        return $this->avatarPetFilename;
    }

    public function setAvatarPetFilename(?string $avatarPetFilename): self
    {
        $this->avatarPetFilename = $avatarPetFilename;
        return $this;
    }

    public function getAvatarPetFile(): ?UploadedFile
    {
        return $this->avatarPetFile;
    }

    public function setAvatarPetFile(?UploadedFile $avatarPetFile): self
    {
        $this->avatarPetFile = $avatarPetFile;
        return $this;
    }
    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getBirthdayDate(): ?\DateTime
    {
        return $this->birthdayDate;
    }

    public function setBirthdayDate(?\DateTime $birthdayDate): self
    {
        $this->birthdayDate = $birthdayDate;
        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): self
    {
        $this->breed = $breed;
        return $this;
    }

}
