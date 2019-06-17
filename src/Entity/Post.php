<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 *
 */
class Post
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $id;

//    /**
//     * @ORM\Column(type="integer")
//     */
//    private $authorId;

    /**
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $verifiedAdminId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     *
     */
    private $photoName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=2048)
     */
    private $text;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     *
     */
    private $author;
    /**
     * @return User
     */
    public function getAuthor()//:User
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }




    public function __construct()
    {
        $dt = new \DateTime();

        $this->setCreatedAt($dt);
        $this->setUpdatedAt($dt);
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): self
    {
        $this->Id = $Id;

        return $this;
    }

//    public function getAuthorId(): ?int
//    {
//        return $this->authorId;
//    }
//
//    public function setAuthorId(int $authorId): self
//    {
//        $this->authorId = $authorId;
//
//        return $this;
//    }

    public function getVerifiedAdminId(): ?int
    {
        return $this->verifiedAdminId;
    }

    public function setVerifiedAdminId(int $verifiedAdminId = null): self
    {
        $this->verifiedAdminId = $verifiedAdminId;

        return $this;
    }

    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    public function setPhotoName(?string $photoName): self
    {
        $this->photoName = $photoName;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }
}
