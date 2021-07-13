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

            //$products = $this->getDoctrine()->getRepository(Product::class)->findAll();

            return $this->render('home/index.html.twig', [
                'controller_name' => 'HomeController',
                //'products' => $products
            ]);
        }
        catch(\Exception $e){
            return $this->render('error.html.twig', [
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
