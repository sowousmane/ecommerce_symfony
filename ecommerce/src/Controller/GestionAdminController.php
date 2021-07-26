<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\History;
use App\Entity\Payment;
use App\Entity\Product;
use App\Form\ProfilePictureFormType;
use Symfony\Component\HttpFoundation\Request;

class GestionAdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        try
        {
            $user = $this->getDoctrine()->getRepository(Admin::class)
            ->findOneBy(['id' => $this->getUser()->getId()]);

            if($user->getPicture() != null)
            {
                $_picture = 'admins/' . $user->getPicture();
            } 
            else 
            {
                $_picture = '_profile.png';
            }
        
            return $this->render('admin/index.html.twig', [
                'user' => $user,
                'current_page' => 'Dashboard',
                '_picture' => $_picture,
            ]);
        }
        catch(\Exception $e){
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }

    /**
     * @Route("/gestion_admin", name="gestion_admin")
     */
    public function gestion_admin(): Response
    {
        try
        {
            $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
            $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $commands = $this->getDoctrine()->getRepository(Command::class)->findAll();
            $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();
            $user = $this->getDoctrine()->getRepository(Admin::class)
                ->findOneBy(['id' => $this->getUser()->getId()]);

            if($user->getPicture() != null)
            {
                $_picture = 'admins/' . $user->getPicture();
            } 
            else 
            {
                $_picture = '_profile.png';
            }
            
            return $this->render('admin/gestionAdmin.html.twig', [
                'admins' => $admins,
                'clients' => $clients,
                'categories' => $categories,
                'products' => $products,
                'commands' => $commands,
                'payments' => $payments,
                'user' => $user,
                'current_page' => 'Gestions administratives',
                '_picture' => $_picture,
            ]);
        }
        catch(\Exception $e){
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }

    /**
     * @Route("/history", name="history")
     */
    public function history(): Response
    {
        try
        {
            $histories = $this->getDoctrine()->getRepository(History::class)->findAll();
            $user = $this->getDoctrine()->getRepository(Admin::class)
                ->findOneBy(['id' => $this->getUser()->getId()]);

            if($user->getPicture() != null)
            {
                $_picture = 'admins/' . $user->getPicture();
            } 
            else 
            {
                $_picture = '_profile.png';
            }
            
            return $this->render('admin/history.html.twig', [
                'current_page' => 'Historique',
                'histories' => $histories,
                'user' => $user,
                '_picture' => $_picture,
            ]);
        }
        catch(\Exception $e){
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }

    /**
     * @Route("/galery", name="galery")
     */
    public function galery(): Response
    {
        try
        {
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $user = $this->getDoctrine()->getRepository(Admin::class)
                ->findOneBy(['id' => $this->getUser()->getId()]);

            if($user->getPicture() != null)
            {
                $_picture = 'admins/' . $user->getPicture();
            } 
            else 
            {
                $_picture = '_profile.png';
            }
            
            return $this->render('admin/galery.html.twig', [
                'products' => $products,
                'current_page' => 'Galerie',
                'user' => $user,
                '_picture' => $_picture,
            ]);
        }
        catch(\Exception $e){
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }
}
