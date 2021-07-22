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
use Symfony\Component\Validator\Constraints\Length;

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
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

            return $this->render('home/home.html.twig', [
                'categories' => $categories,
                'products' => $products,
                'client' => $client,
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

    /**
     * @Route("/products_by_category/{id}", name="products_by_category")
     */
    public function productsByCategory($id): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['category' => $id]);
        
        return $this->render('home/productsByCategory.html.twig', [
            'products' => $products,
        ]);
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
     * @Route("/login_test", name="login_test")
     */
    public function login_test():Response
    {
        return $this->render('home/login_essai.html.twig');
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
     * @Route("/details/{id}", name="details")
     */
    public function details($id): Response
    {
        try{
            $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $id]);
            $_products = $this->getDoctrine()->getRepository(Product::class)->findBy(['category' => $product->getCategory()]);
            $__products = [];
            $products = [];

            foreach($_products as $_product)
            {
                if($_product->getId() != $id)
                {
                    array_push($__products, $_product);
                }
            }

            $index = array_rand($__products, 3);

            foreach($index as $i)
            {
                array_push($products, $__products[$i]);
            }

            return $this->render('home/details.html.twig', [
                'controller_name' => 'HomeController',
                'product' => $product,
                'products' => $products,
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }

  
    /**
    * @Route("/forum", name="forum")
    */
    public function forum(): Response
    {
        try{
            return $this->render('home/forum.html.twig', [
                'controller_name' => 'HomeController',
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }
    /**
    * @Route("/contact", name="contact")
    */
   public function contact(): Response
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
