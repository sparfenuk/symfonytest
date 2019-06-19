<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PostController extends AppController
{


    /**
     * @Route("/", name="index")
     * @param AuthorizationCheckerInterface $authChecker
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(AuthorizationCheckerInterface $authChecker)
    {
        return $this->redirectToRoute('show_posts', ['page' => 1]);
    }

    /**
     * @Route("/post/create", name="create_post")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function create(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class,$post);

        $form->handleRequest($request);


        if ($form->isSubmitted()) {


            if ($post->getPhotoName() !== null) {
                $uploader = new FileUploader($this->getParameter('image_dir'));

                $fileName = $uploader->uploadImage($post->getPhotoName());
                if ($fileName === false)
                    $this->addFlash('error', 'Can\'t upload image.');

                $post->setPhotoName($fileName);
            }



            $post->setAuthor($this->get('security.token_storage')->getToken()->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $this->addFlash('success', 'You\'ve just created post, wait until it would be reviewed.');

            return $this->redirectToRoute('user_posts');
        }


        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/post/edit/{id}" , name="post_edit")
     * @param $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function edit($id, Request $request)
    {



        $post = $user = $this->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        if ($this->get('security.token_storage')->getToken()->getUser()->getId() != $post->getAuthor()->getId()) {
            $this->addFlash('error', 'you are\'nt the owner of this post');
            return $this->redirect('error', 400);
        }
        $fileName = $post->getPhotoName();

        $form = $this->createForm(PostType::class,$post);

        $form->handleRequest($request);


        if ($form->isSubmitted()) {

            if ($fileName !== $post->getPhotoName()) {
                $uploader = new FileUploader($this->getParameter('image_dir'));

                $fileName = $uploader->uploadImage($post->getPhotoName());
                if ($fileName === false)
                    $this->addFlash('error', 'Can\'t upload image.');

                $post->setPhotoName($fileName);
            }
            $post->setVerifiedAdminId();// null by default

            $post->setUpdatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'You\'ve just updated your post, wait until it would be reviewed.');
            $this->redirectToRoute('show_posts', ['page' => 1]);
        }
        return $this->render('post/create.html.twig', ['form' => $form->createView(), 'image_name' => $fileName]);

    }

    /**
     * @Route("/posts/{page}", name="show_posts")
     * @param $page
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function posts($page, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Post::class);


        if ($page < 1)
            $page = 1;

//        if ($request->getMethod() == Request::METHOD_POST)
//            self::debug($request->request->get('orderby'));


        $order = $request->query->get('orderby');

        if ($order !== 'email' && $order !== 'username' && $order !== 'date')
            $order = 'date';

        $posts = null;


        switch ($order) {
            case 'email':

                $posts = $repository->createQueryBuilder('p')
                    ->where('p.verifiedAdminId IS NOT NULL')
                    ->leftjoin('p.author', 'u')
                    ->orderBy('u.email', 'ASC')
                    ->setMaxResults(25)
                    ->setFirstResult(25 * ($page - 1))
                    ->getQuery()->execute();
                break;
            case 'username':

                $posts = $repository->createQueryBuilder('p')
                    ->where('p.verifiedAdminId IS NOT NULL')
                    ->leftjoin('p.author', 'u')
                    ->orderBy('u.username', 'ASC')
                    ->setMaxResults(25)
                    ->setFirstResult(25 * ($page - 1))
                    ->getQuery()->execute();
                break;
            //also 'date'
            default:
                $posts = $repository->createQueryBuilder('p')
                    ->where('p.verifiedAdminId IS NOT NULL')
                    ->orderBy('p.updatedAt', 'DESC')
                    ->setMaxResults(25)
                    ->setFirstResult(25 * ($page - 1))
                    ->getQuery()->execute();
                break;

        }


        //$posts = $repository->findBy([],['updatedAt' => 'DESC'],25,25*($page-1));

        //self::debug($posts);

        return $this->render('post/posts.html.twig', ['posts' => $posts, 'orderby' => $order]);
    }




    /**
     * @Route("/userposts", name="user_posts")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function userPosts()
    {

        $posts = $user = $this->getDoctrine()
            ->getRepository(Post::class)
            ->findBy([
                'author' => $this->get('security.token_storage')->getToken()->getUser()
            ],
            [
                'updatedAt' => 'DESC',
            ]
            );


        return $this->render('post/post_list.html.twig', [
            'posts' => $posts,
        ]);
    }
}
