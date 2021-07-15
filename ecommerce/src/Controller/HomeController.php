<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\CreateClientFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//use App\Entity\Product;

class HomeController extends AbstractController
{
    /**
     * @Route("/create_client", name="create_client")
     */
    public function create_client(Request $request, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try{
            return $this->render('home/home.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }
}
