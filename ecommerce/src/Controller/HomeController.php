<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//use App\Entity\Product;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        try{
            return $this->render('home/home.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            return $this->render('error.html.twig', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
