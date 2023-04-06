<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    public CONST SENDER_TYPE_VENDOR = 'VENDOR';
    public CONST SENDER_TYPE_USER = 'USER';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;
    
    #[ORM\ManyToOne(targetEntity: Vendor::class)]
    private ?Vendor $vendor = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;
    
    #[ORM\Column(type: 'string')]
    private ?string $message = null;
    
    #[ORM\Column(type: 'datetime')]
    private ?\DateTime $createdAt;

    #[ORM\Column]
    private ?string $senderType = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }
    
    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }
    
    public function setVendor(?Vendor $vendor): self
    {
        $this->vendor = $vendor;
        
        return $this;
    }
    
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;
        
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    public function getSenderType(): ?string
    {
        return $this->senderType;
    }

    public function setSenderType(?string $senderType): self
    {
        $this->senderType = $senderType;

        return $this;
    }
}