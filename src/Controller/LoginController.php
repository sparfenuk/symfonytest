<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class LoginController extends AppController
{



//    /**
//     * @Route("/logon", name="login")
//     */
//    public function index(Request $request):Response
//    {
//        $form = $this->createForm(LoginType::class);
//        $form->handleRequest($request);
//
//        if($form->isSubmitted() && $form->isValid()){
//
//        }
//
//        return $this->render('login/index.html.twig',['form' => $form->createView()]);
//    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils):Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/login.html.twig',['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
    }
    /**
     * @Route("/registr", name="registration")
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     * @throws \Exception
     */
    public function registration(UserPasswordEncoderInterface $passwordEncoder, Request $request, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
//            self::debug($form);
//            echo $request->request->get('username');
//            echo $request->request->get('registration[username]');


            $user->setPassword($passwordEncoder->encodePassword($user,$user->getUsername()));

            $user->setIp($request->server->get('REMOTE_ADDR'));

//            $browser = get_browser(null,true);
//            $user->setBrowser($browser['parent'].' '.$browser['platform']);

//            $user->setBrowser(implode(" ",self::getBrowser()));
            $user->setBrowser(self::getBrowser()['name']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom($user->getEmail())
                ->setTo($user->getEmail())
                ->setBody(
                    'To confirm your email go this link 127.0.0.1:8000'.$this->generateUrl('confirm',['key' => $user->getAuthKey()])
                )
            ;
            $mailer->send($message);


            $this->addFlash('success', 'You\'ve just registered check email for details.');
            $this->redirect('app_login');
        }
        return $this->render('login/registry.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws \Exception
     */
    public function profile(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($user->getPassword() !== null)
                $user->setPassword($passwordEncoder->encodePassword($user,$user->getPassword()));


            $user->setUpdatedAt( new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            $this->addFlash('success', 'You\'ve just updated your account.');
            $this->redirect('app_login');
        }
        return $this->render('login/registry.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/confirm/{key}",name="confirm")
     * @param $key
     * @return Response
     */
    public function confirm($key,Request $request)
    {

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['authKey' => $key]);

        if($user->getStatus() == 0)
            $user->setStatus(1);
        else {
            $this->addFlash('error', 'You\'ve already confirmed your email.');
            return $this->render('login/registry.html.twig');
        }

        $user->setAuthKey(self::generateRandomString(30));
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'You\'ve just confirmed your email.');

        return $this->redirectToRoute('app_login', $request->query->all());
    }


    /**
     * @Route("/forgot",name="forgot")
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return Response
     */
    public function forgot(Request $request,\Swift_Mailer $mailer)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, ['required' => true])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->findOneBy(['email' => $form->getData()['email']]);

            if($user !== null){
                $message = (new \Swift_Message('Change your password email'))
                    ->setFrom($user->getEmail())
                    ->setTo($user->getEmail())
                    ->setBody(
                        'To confirm your email go this link 127.0.0.1:8000/ua/change/'.$user->getAuthKey()
                    );
                $mailer->send($message);

                $this->addFlash('success', 'Check email for details.');
            }
            else $this->addFlash('error', 'User with this email doesnt exists.');

            return $this->redirectToRoute('app_login');
        }

        //вони однакові todo: make one template for forms
        return $this->render('login/registry.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change/{key}",name="change")
     * @param $key
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function change($key,Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['authKey' => $key]);

        $form = $this->createFormBuilder()
            ->add('password', RepeatedType::class,[
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password'
                ],
                'second_options' => ['label'=>'Repeat password']
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($passwordEncoder->encodePassword($user,$form->getData()['password']));
            $user->setAuthKey(self::generateRandomString(30));

            $this->addFlash('success', 'Password successfully changed.');


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/registry.html.twig',[
            'form' => $form->createView(),
        ]);
    }

//    /**
//     * @Route("/userposts", name="user_posts")
//     * @return Response
//     */
//    public function posts()
//    {
//
//        $posts = $user = $this->getDoctrine()
//            ->getRepository(Post::class)
//            ->findBy(['author' => $this->get('security.token_storage')->getToken()->getUser()]);
//
//
//        return $this->render('login/posts_list.html.twig',[
//            'posts' => $posts,
//        ]);
//    }
}
