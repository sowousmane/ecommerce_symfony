<?php

namespace App\Controller;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CreateClientFormType;
use App\Service\Cart\CartService;
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
    public function home( CartService $cartService)
    {   
        $client = null;
        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
        }
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('home/home.html.twig', [
            'categories' => $categories,
            'products' => $products,
            'client' => $client,
            'items' => $cartService->getFullCart(),
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

        return $this->redirectToRoute("panier");

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
            $products = $this->getDoctrine()->getRepository(Product::class)->findBy(['category' => $product->getCategory()]);
            $_products = [];
            for($i = 0; $i< count($products); $i++)
            {
                if($product->getId() != $products[$i]->getId())
                {
                    $_products[$i] = $products[$i];
                }
            }
            return $this->render('home/details.html.twig', [
                'controller_name' => 'HomeController',
                'product' => $product,
                'products' => $_products,
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
