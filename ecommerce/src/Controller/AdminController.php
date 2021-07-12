<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\ClientFormType;

use App\Entity\Client;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Command;
use App\Entity\Admin;
use App\Entity\Payment;
use App\Entity\ProductCommand;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(Request $request): Response
    {
        try{

            $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
            $commands = $this->getDoctrine()->getRepository(Command::class)->findAll();
            $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();
            $productCommands = $this->getDoctrine()->getRepository(ProductCommand::class)->findAll();

            $client = new Client();
            $form = $this->createForm(ClientFormType::class, $client);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($client);
                $doctrine->flush();
            }

            return $this->render('admin/index.html.twig', [
                'clients' => $clients,
                'products' => $products,
                'categories' => $categories,
                'admins' => $admins,
                'commands' => $commands,
                'payments' => $payments,
                'productCommands' => $productCommands,
                'clientForm' => $form->createView(),
            ]);
        }
        catch(\Exception $e){
            return $this->render('error.html.twig', [
                'exception' => $e->getMessage(),
            ]);
        }
        
    }

    /*
    ------------------------------------------------------
    -------------------- Client --------------------------
    ------------------------------------------------------
    */
    
    public function createClient(): Response
    {
        try{
            $entityManager = $this->getDoctrine()->getManager();

            $client = new Client();
            $form = $this->createForm(ClientFormType::class, $client);
            $client->setFirstname('Hassane');
            // les autres set...
            
            /* $entityManager->persist($client);
            $entityManager->flush(); */

            return new Response('Compétence enregistrée ! Son id : ' . $client->getId());
        }
        catch(\Exception $e){
            return $this->render('error.html.twig', [
                'exception' => $e->getMessage(),
            ]);
        }
        
    }
}
