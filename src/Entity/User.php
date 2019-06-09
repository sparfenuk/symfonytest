<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields="email", message="This e-mail already exists.")
 * @UniqueEntity(fields="username", message="This username already exists.")
 */
class User implements UserInterface, \Serializable
{

    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string",length=64, unique=true)
     */
    private $email;

    /**
     * 0-user without confirmed email
     * 1-user with confirmed email
     * 2-moderator
     * 3-admin
     * -1-banned user
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $auth_key;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo_name;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $browser;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    public function getRoles()
    {
        return [
            'ROLE_USER',
            'ROLE_ADMIN',
            'ROLE_MODERATOR'
        ];
    }

    public function getSalt()
    {
        return 'SaLtsdjgkgkit12863513khdrkSYTAE%^^%^%&^%&^5\"';
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->status,
            $this->auth_key,
            $this->photo_name,
            $this->ip,
            $this->browser,
            $this->created_at,
            $this->updated_at,
        ]);
    }

    public function unserialize($serialized)
    {
        list($this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->status,
            $this->auth_key,
            $this->photo_name,
            $this->ip,
            $this->browser,
            $this->created_at,
            $this->updated_at,) = unserialize($serialized);
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

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }


    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function setAuthKey(string $auth_key): self
    {
        $this->auth_key = $auth_key;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getBrowser(): ?string
    {
        return $this->browser;
    }

    public function setBrowser(string $browser): self
    {
        $this->browser = $browser;

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getPhotoName(): ?string
    {
        return $this->photo_name;
    }

    public function setPhotoName(string $photo_name): self
    {
        $this->photo_name = $photo_name;

        return $this;
    }

}
