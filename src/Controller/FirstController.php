<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class FirstController extends AbstractController
{
    /**
     * @Route("/", name="first")
     */
    public function index()
    {
        return $this->render('first/index.html.twig', [
            'controller_name' => 'FirstController',
        ]);
//        return new Response("<html><body>dsadsadsdasd</body></html>",Response::HTTP_OK);
    }

    /**
     * @Route("/welcome", name="first_page")
     */
    public function hello(Request $request){
        $name = $request->query->get('name', 'Anonymous');

        return $this->render('first_page.html.twig',[
            'name' => $name,

        ]);
    }

}
