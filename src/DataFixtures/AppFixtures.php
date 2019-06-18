<?php

namespace App\DataFixtures;

use App\Controller\AppController;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->loadUsers($manager);
        $this->loadPosts($manager);
        $manager->flush();
    }

    public function loadPosts(ObjectManager $manager)
    {
        for($i = 0; $i < 100;$i++){
            $post = new Post();
            $post->setAuthor($manager->getRepository(User::class)->find(1));
            $post->setCreatedAt(new \DateTime());
            $post->setUpdatedAt(new \DateTime());
            $post->setText(AppController::generateRandomString(100));

            $post->setVerifiedAdminId(3);
            $manager->persist($post);
            $manager->flush();
        }
    }
    public function loadUsers(ObjectManager $manager)
    {

        $user = new User();
        $user->setId(0);
        $user->setUsername('Vasya');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'1234'));
        $user->setEmail('vasya@email.com');
        $user->setAuthKey(AppController::generateRandomString(30));
        $user->setIp('127.0.0.1');
        $user->setBrowser('chrome');
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        //$user->setPhotoName('no_image.png');
        $user->setStatus(1);

        $manager->persist($user);

        $user = new User();
        $user->setId(1);
        $user->setUsername('VasyaModerator');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'1234'));
        $user->setEmail('VasyaModerator@email.com');
        $user->setAuthKey(AppController::generateRandomString(30));
        $user->setIp('127.0.0.1');
        $user->setBrowser('chrome');
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        //$user->setPhotoName('no_image.png');
        $user->setStatus(2);

        $manager->persist($user);

        $user = new User();
        $user->setId(2);
        $user->setUsername('VasyaAdmin');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'1234'));
        $user->setEmail('VasyaAdmin@email.com');
        $user->setAuthKey(AppController::generateRandomString(30));
        $user->setIp('127.0.0.1');
        $user->setBrowser('chrome');
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());
        //$user->setPhotoName('no_image.png');
        $user->setStatus(3);

        $manager->persist($user);

        $manager->flush();
    }
}
