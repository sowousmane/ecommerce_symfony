<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Comments;
use App\Entity\History;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CommentsFormType;
use App\Form\CommentsType;
use App\Form\CreateClientFormType;
use App\Service\Cart\CartService;
use App\Form\SearchProductFormType;
use App\Repository\CommentsRepository;
use App\Repository\ProductRepository;
use App\Service\AppService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
     /**
     * @Route("/", name="home")
     */
    public function home( CartService $cartService, Request $request, ProductRepository $productRepository)
    {   
        $user = '';
        $client = null;
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            $user = $client->getFirstname() . ' ';
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $search = $request->request->get('search_product');

        if($request->getMethod() == 'POST')
        {
            $products = $productRepository->search($search);
        }
        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'products' => $products,
            'client' => $client,
            'user' => $user,
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
        ]);
       
    }

    /**
     * @Route("/products_by_category/{id}", name="products_by_category")
     */
    public function productsByCategory($id, CartService $cartService): Response
    {
        $user = '';
        $client = null;
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            $user = $client->getFirstname() . ' ';
        }

        $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['category' => $id]);
        
        return $this->render('home/productsByCategory.html.twig', [
            'products' => $products,
            'client' => $client,
            'user' => $user,
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
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
    public function panier(CartService $cartService){
        
        return $this->render('home/panier.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
        ]);
    }

      /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add(Product $product, CartService $cartService){
       
        $cartService->add($product);

        return $this->redirectToRoute("home");

    }

    /**
     * @Route("panier/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, CartService $cartService){
       
        $cartService->remove($product);

        return $this->redirectToRoute("panier");
    }
    /**
     * @Route("panier/delete/{id}", name="cart_delete")
     */
    public function delete(Product $product, CartService $cartService){
       
        $cartService->delete($product);

        return $this->redirectToRoute("panier");
    }

    /**
     * @Route("panier/delete", name="cart_delete_all")
     */
    public function deleteAll( CartService $cartService){
       
        $cartService->deleteAll();

        return $this->redirectToRoute("panier");
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
        $history = new History();
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

            $history->setTitle('Inscription d\'un client');
            $history->setContent(
                "Informations du client inscrit : 
                Prénom : " . $client->getFirstname() . "
                Nom : " . $client->getLastname() . "
                E-mail : " . $client->getEmail()
            );
            $history->setSentAt(date('l jS \of F Y h:i:s A'));
            $history->setColor('alert alert-success');

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($client);
            $doctrine->persist($user);
            $doctrine->persist($history);
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
    public function details(Product $produit, Request $request, $id, CartService $cartService, CommentsRepository $commentsRepository): Response
    {
        $user = '';
        $client = null;

        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            $user = $client->getFirstname() . ' ';
        }

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

            $post = new Comments();
        
            $formulaire = $this->createForm(CommentsFormType::class, $post);
    
            $formulaire->handleRequest($request);
    
            $formulaire->getErrors();
    
            if($formulaire->isSubmitted() && $formulaire->isValid())
            {
                $post->setProduit($product);

                $parentId = $formulaire->get("parent")->getData();
        

                $em = $this->getDoctrine()->getManager();

            
                
                if($parentId != null){

                    $parent = $em->getRepository(Comments::class)->find($parentId);
                }

                // On définit le parent
                $post->setParent($parent ?? null);

                $em->persist($post);
                
                $em->flush();
                $this->addFlash('message', 'Votre commentaire a bien été envoyé');
                return $this->redirectToRoute("details", ['id' =>
                    $product->getId()    
                ]);
            }
    
           
            $comments = $commentsRepository->findBy(['produit' => $produit]);
            
            
    
            //$em->persist($post);
            
            //$em->flush();
    
           
           //dd($comments);
            return $this->render('home/details.html.twig', [
                'product' => $product,
                'products' => $products,
                'client' => $client,
                'user' => $user,
                'total' => $cartService->getTotal(),
                'totalItem' => $cartService->getTotalItem(),
                'comments' => $comments,
                'formulaire' => $formulaire->createView(),
            ]);
        }
        catch(\Exception $e){
            return $this->render('error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

  
    /*======================================================== */
    /**
    * @Route("/forum", name="forum")
    */
    public function forum (CommentsRepository $commentsRepository, CartService $cartService, Request $request, ProductRepository $productRepository)
    {
        $user = '';
        $client = null;
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
            $user = $client->getFirstname() . ' ';
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $search = $request->request->get('search_product');

        if($request->getMethod() == 'POST')
        {
            $products = $productRepository->search($search);
        }

        $posts = $commentsRepository->findAll();

        $post = new Comments();
        
        $formulaire = $this->createForm(CommentsFormType::class, $post);

        $formulaire->handleRequest($request);
        $formulaire->getErrors();
        if($formulaire->isSubmitted() && $formulaire->isValid())
        {   
            $parentId = $formulaire->get("parent")->getData();
        

            $em = $this->getDoctrine()->getManager();

           
            
            if($parentId != null){

                $parent = $em->getRepository(Comments::class)->find($parentId);
            }

            // On définit le parent
            $post->setParent($parent ?? null);

            $em->persist($post);
            
            $em->flush();
            return $this->redirectToRoute("forum");
        }
        return $this->render('home/forum.html.twig', [
            'posts' => $posts,
            'categories' => $categories,
            'products' => $products,
            'client' => $client,
            'user' => $user,
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
            'formulaire' => $formulaire->createView(),
               
        ]);
    }

   
   
        

     /**
    * @Route("/show{id}", name="show_comment")
    */
    public function showComment($id)
    {
        $posts =  $this->getDoctrine()->getRepository(Comments::class)->findOneBy(['id' => $id]);
        $_posts = $this->getDoctrine()->getRepository(Comments::class)->findBy(['parent' => $id]);
      
        return $this->render('home/showComment.html.twig', [
            'posts' => $posts,
            '_posts' => $_posts,
            
               
        ]);
    }

    /**
    * @Route("/contact", name="contact")
    */
    public function contact(Request $request, \Swift_Mailer $mailer): Response
    {
        if($request->getMethod() == 'POST')
        {
            $email = $request->request->get('email');
            $body = $request->request->get('message');
            $_website = $request->request->get('website');
            $message = (new \Swift_Message('Prise de contact'))
            ->setFrom($email)
            ->setTo('sowousmane4811@gmail.com')
            ->setBody($body .  '

Mon website est : ' . $_website . '

Envoyé par : '. $email);
            
    
            $mailer->send($message);
        }

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
