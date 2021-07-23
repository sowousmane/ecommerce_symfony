<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\History;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateAdminFormType;
use App\Form\CreateProductFormType;
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
        $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $commands = $this->getDoctrine()->getRepository(Command::class)->findAll();
        $payments = $this->getDoctrine()->getRepository(Payment::class)->findAll();
        $user = $this->getDoctrine()->getRepository(Admin::class)
            ->findOneBy(['id' => $this->getUser()->getId()]);
        
        return $this->render('admin/gestionAdmin.html.twig', [
            'admins' => $admins,
            'clients' => $clients,
            'categories' => $categories,
            'products' => $products,
            'commands' => $commands,
            'payments' => $payments,
            'user' => $user,
            'current_page' => 'Gestions administratives',
        ]);
    }

    /**
     * @Route("/history", name="history")
     */
    public function history(): Response
    {
        $histories = $this->getDoctrine()->getRepository(History::class)->findAll();
        
        return $this->render('admin/history.html.twig', [
            'current_page' => 'Historique',
            'histories' => $histories,
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
            $history = new History();
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

                $history->setTitle('Création d\'un administrateur');
                $history->setContent(
                    "Informations de l'administrateur créé : 
                    Prénom : " . $admin->getFirstname() . "
                    Nom : " . $admin->getLastname() . "
                    E-mail : " . $admin->getEmail()
                );
                $history->setSentAt(date('l jS \of F Y h:i:s A'));
                $history->setColor('alert alert-success');

                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($admin);
                $doctrine->persist($user);
                $doctrine->persist($history);
                $doctrine->flush();

                return $this->render('admin/response.html.twig', [
                    'response' => 'message', 'L\'administrateur a été créé avec succès !',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-success',
                ]);
            }
            
            $_user = $this->getDoctrine()->getRepository(Admin::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            
            return $this->render('admin/createAdmin.html.twig', [
                'adminForm' => $form->createView(),
                'admin' => $_user,
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
     * @Route("/edit_product", name="edit_product")
     */
    public function editProduct(Request $request): Response
    {
        $id = 1;
        try{
            $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $id]);

            if(!$product){
                return $this->render('admin/response.html.twig', [
                    'response' => 'Le produit dont l\'id est ' . $id . ' n\'existe pas',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-danger',
                ]);
            }

            $form = $this->createForm(CreateProductFormType::class, $product);
            $form->handleRequest($request);

            

            return $this->render('admin/editProduct.html.twig', [
                'editProductForm' => $form->createView(),
                'response' => 'Le produit dont l\'id est ' . $id . ' a été modifié !',
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

    /**
     * @Route("/delete_admin/{id}", name="delete_admin")
     */
    public function deleteAdmin($id): Response
    {
        try{
            $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy(['id' => $id]);
            
            if(!$admin){
                return $this->render('admin/response.html.twig', [
                    'response' => 'L\'administrateur dont l\'id est ' . $id . ' n\'existe pas',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-danger',
                ]);
            }

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->remove($admin);
            $doctrine->flush();

            return $this->render('admin/response.html.twig', [
                'response' => 'L\'administrateur dont l\'id est ' . $id . ' a été supprimé !',
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
