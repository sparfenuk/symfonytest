<?php

namespace App\Entity;

use App\Controller\AppController;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
    private $authKey;


//    /**
//     * @ORM\Column(type="string", length=255, nullable=true)
//     */
//    private $photo_name;

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
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;


    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Post" , mappedBy="author")
     */
    private $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $dt = new \DateTime();

        $this->setStatus(0);
        $this->setAuthKey(AppController::generateRandomString(30));
        $this->setCreatedAt($dt);
        $this->setUpdatedAt($dt);
        $this->setIp('127.0.0.1');
        $this->setBrowser('Chrome');

    }
    public function getRoles()
    {

        switch ($this->status){
            case 0:
            case 1:
              return [
                  'ROLE_USER'
              ];
                break;
            case 2:
               return [
                   //'ROLE_USER',
                   'ROLE_MODERATOR'
               ];
                break;
            case 3:
                return [
                   //'ROLE_USER',
                   // 'ROLE_MODERATOR',
                    'ROLE_ADMIN'
                ];
                break;

        }
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
            $this->authKey,
           // $this->photo_name,
            $this->ip,
            $this->browser,
            $this->createdAt,
            $this->updatedAt,
        ]);
    }

    public function unserialize($serialized)
    {
        list($this->id,
            $this->username,
            $this->password,
            $this->email,
            $this->status,
            $this->authKey,
            //$this->photo_name,
            $this->ip,
            $this->browser,
            $this->createdAt,
            $this->updatedAt,) = unserialize($serialized);
    }


    public function getRole(): ?int
    {
        return $this->status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $Id): self
    {
        $this->id = $Id;

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
        //return $this->status;
        switch ($this->status){
            case 0:
            case 1:
                return 'ROLE_USER';
                break;
            case 2:

                    //'ROLE_USER',
                   return 'ROLE_MODERATOR';

                break;
            case 3:

                    //'ROLE_USER',
                    // 'ROLE_MODERATOR',
                    return 'ROLE_ADMIN';

                break;

        }
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }


    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    public function setAuthKey(string $authKey): self
    {
        $this->authKey = $authKey;

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
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    public function __toString()
    {
        return sprintf('%s : %s : %s',$this->id,$this->username,$this->email);
    }




//    public function getPhotoName(): ?string
//    {
//        return $this->photo_name;
//    }
//
//    public function setPhotoName(string $photo_name): self
//    {
//        $this->photo_name = $photo_name;
//
//        return $this;
//    }

}
