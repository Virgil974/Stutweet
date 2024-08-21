<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: "post")]
class Post {
    
    #[ORM\Id()]
    #[ORM\GeneratedValue(strategy: "AUTO")] 
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", nullable: true, length: 150)] 
    private ?string $title = NULL;

    #[ORM\Column(type: "text", length: 320)] 
    private string $content;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $image = NULL;

    #[ORM\ManyToOne(targetEntity: "App\Entity\User", inversedBy: "posts")]
    #[ORM\JoinColumn(name:"user_id", referencedColumnName:"id", onDelete:"CASCADE")]
    private $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self 
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }
}