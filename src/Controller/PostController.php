<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AppController
{
    /**
     * @Route("/post/create", name="create_post")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request,UserInterface $user )
    {
        $post = new Post();
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if ($data['photo_name'] != null) {
                $uploader = new FileUploader($this->getParameter('image_dir'));
                $fileName = $uploader->uploadImage($data['photo_name']);
                if ($fileName === false)
                    $this->addFlash('error', 'Can\'t upload image.');
                $post->setPhotoName($fileName);

            }
            //$post->setHeader($data['header']);
            $post->setText($data['text']);
            $post->setAuthorId($this->get('security.token_storage')->getToken()->getUser()->getId());
//            $post->setAuthorId($this->getDoctrine()
//                ->getRepository(User::class)
//                ->findOneBy(['username' => $user->getUsername()])->getId());
            $dt = new \DateTime();

            $post->setCreatedAt($dt);
            $post->setUpdatedAt($dt);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'You\'ve just created post, wait until it would be reviewed.');
        }


        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * * @Route("/posts/{page}", name="show_posts")
     */
    public function posts($page)
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $repository->findBy([],['updated_at' => 'DESC'],25,25*($page-1));

        $repository = $this->getDoctrine()->getRepository(User::class);
        $shows = array();

        foreach ($posts as $post){
            $user = $repository->find($post->getAuthorId());
            $p = new \App\Entity\PostShow\Post();
            $p->text = $post->getText();
            $p->updated_at = $post->getUpdatedAt();
            $p->username = $user->getUsername();
            $p->email = $user->getEmail();

            array_push($shows,$p);
        }

        return $this->render('post/posts.html.twig',['posts'=>$shows]);
    }
}
