<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateClientFormType;
use App\Service\AppService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {   
        $client = null;
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
        }
        
        try{
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            
            return $this->render('home/home.html.twig', [
                'products' => $products,
                'categories' => $categories,
                'client' => $client,
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(): Response
    {
        try{
            $user = $this->getDoctrine()->getRepository(Client::class)
                ->findOneBy(['id' => $this->getUser()->getId()]);

            return $this->render('client/profile.html.twig', [
                'user' => $user,
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }
     /**
     * @Route("/login_test", name="login_test")
     */
    public function login_test(): Response
    {
        return $this->render('admin/login_essai.html.twig');
    }
    /**
     * @Route("/panier", name="panier")
     */
    public function panier(Request $request): Response
    {
        if($request->isMethod('POST')){
            return $this->redirectToRoute('payment');
        }

        return $this->render('home/panier.html.twig', [
            
        ]);
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request): Response
    {
        

        return $this->render('home/payment.html.twig', [
            
        ]);
    }

    /**
     * @Route("/create_client", name="create_client")
     */
    public function create_client(Request $request, 
    UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $client = new Client();
        $user = new User();
        $form = $this->createForm(CreateClientFormType::class, $client);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setEmail($client->getEmail());
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $client->getPassword()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($client);
            $doctrine->persist($user);
            $doctrine->flush();

            $this->addFlash('message', 'Le client a été créé avec succès !');
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('client/createClient.html.twig', [
            'clientForm' => $form->createView(),
        ]);
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
     * @Route("/Details", name="Details")
     */
    public function Details(): Response
    {
        try{
            return $this->render('home/Details.html.twig', [
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
