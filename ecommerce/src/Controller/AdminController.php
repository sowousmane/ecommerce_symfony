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
                'current_page' => 'Dashboard',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

    /**
     * @Route("/gestion_admin", name="gestion_admin")
     */
    public function gestion_admin(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        
        return $this->render('admin/gestionAdmin.html.twig', [
            'products' => $products,
            'current_page' => 'Gestions administratives',
        ]);
    }

    /**
     * @Route("/history", name="history")
     */
    public function history(): Response
    {
        
        
        return $this->render('admin/history.html.twig', [
            'current_page' => 'Historique',
        ]);
    }

    /**
     * @Route("/galery", name="galery")
     */
    public function galery(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        
        return $this->render('admin/galery.html.twig', [
            'products' => $products,
            'current_page' => 'Galerie',
        ]);
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

                $this->addFlash('message', 'L\'administrateur a été créé avec succès !');
                return $this->redirectToRoute('admin');
            }
            
            return $this->render('admin/createAdmin.html.twig', [
                'adminForm' => $form->createView(),
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
     * @Route("/delete_product/{id}", name="delete_product")
     */
    public function deleteProduct($id): Response
    {
        try{
            $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $id]);
            
            if(!$product){
                return $this->render('admin/response.html.twig', [
                    'response' => 'Le produit dont l\'id est ' . $id . ' n\'existe pas',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-danger',
                ]);
            }

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->remove($product);
            //$doctrine->flush();

            return $this->render('admin/response.html.twig', [
                'response' => 'Le produit dont l\'id est ' . $id . ' a été supprimé !',
                'current_page' => 'Réponse',
                'class' => 'alert alert-success',
            ]);
        } catch (\Exception $e) {
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }
}
