<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
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
        $form = $this->createForm(RegistrationType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
//            self::debug($form);
//            echo $request->request->get('username');
//            echo $request->request->get('registration[username]');
            $data = $request->request->get('registration');

            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPassword($passwordEncoder->encodePassword($user,$data['password']['first']));
            $user->setStatus(0);
            $user->setAuthKey(self::generateRandomString(30));
            $user->setIp($request->server->get('REMOTE_ADDR'));

            $dt = new \DateTime();

            $user->setCreatedAt($dt);
            $user->setUpdatedAt($dt);

//            $browser = get_browser(null,true);
//            $user->setBrowser($browser['parent'].' '.$browser['platform']);

//            $user->setBrowser(implode(" ",self::getBrowser()));
            $user->setBrowser(self::getBrowser()['bname']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $message = (new \Swift_Message('Hello Email'))
                ->setFrom($data['email'])
                ->setTo($data['email'])
                ->setBody(
                    'To confirm your email go this link 127.0.0.1:8000/confirm?key='.$user->getAuthKey()
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
}
