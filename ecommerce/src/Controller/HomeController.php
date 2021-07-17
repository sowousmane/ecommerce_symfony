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
            $this->addFlash('danger', $e->getMessage());
        }
    }
    /**
     * @Route("/homes", name="homes")
     */
    public function homes(): Response
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
     /**
     * @Route("/alimentation", name="alimentation")
     */
    public function Alimentation(): Response
    {
        try{
            return $this->render('home/Alimentation.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

     /**
     * @Route("/Hygiene", name="Hygiene")
     */
    public function Hygiene(): Response
    {
        try{
            return $this->render('home/Hygiene.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

    /**
    * @Route("/Maison", name="Maison")
    */
   public function Maison(): Response
   {
       try{
           return $this->render('home/Maison.html.twig', [
               'controller_name' => 'HomeController',
           ]);
       }
       catch(\Exception $e){
           $this->addFlash('danger', $e->getMessage());
       }
   }

    /**
    * @Route("/Forum", name="Forum")
    */
    public function Forum(): Response
    {
        try{
            return $this->render('home/Forum.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }
    /**
    * @Route("/Contact", name="Contact")
    */
   public function Contact(): Response
   {
       try{
           return $this->render('home/contact.html.twig', [
               'controller_name' => 'HomeController',
           ]);
       }
       catch(\Exception $e){
           $this->addFlash('danger', $e->getMessage());
       }
   }
}
