<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateAdminFormType;
use App\Service\AppService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        try{

            $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
            $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $commands = $this->getDoctrine()->getRepository(Command::class)->findAll();
            $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();
            $user = $this->getDoctrine()->getRepository(Admin::class)
                ->findOneBy(['id' => $this->getUser()->getId()]);
            //$commands = $this->getDoctrine()->getRepository(Command::class)->findAll();
            
            return $this->render('admin/index.html.twig', [
                'admins' => $admins,
                'clients' => $clients,
                'categories' => $categories,
                'products' => $products,
                'commands' => $commands,
                'payments' => $payments,
                'user' => $user,
                //'admins' => $admins,
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
        
    }

    /**
     * @Route("/create_admin", name="create_admin")
     */
    public function createAdmin(Request $request, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try{

            $admin = new Admin();
            $user = new User();
            $form = $this->createForm(CreateAdminFormType::class, $admin);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setEmail($admin->getEmail());
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $admin->getPassword()
                    )
                );
                $user->setRoles(['ROLE_ADMIN']);
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($admin);
                $doctrine->persist($user);
                $doctrine->flush();
            }
            
            return $this->render('admin/createAdmin.html.twig', [
                'adminForm' => $form->createView(),
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
        
    }
}
