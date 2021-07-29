<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Command;
use App\Entity\CommandProduct;
use App\Entity\Payment;
use App\Entity\Product;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function cart(CartService $cartService):Response
    {
        return $this->render('home/cart.html.twig', [
            'items' => $cartService->getFullCart(),
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
        ]);
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add(Product $product, CartService $cartService)
    {
        $cartService->add($product);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("cart/remove/{id}", name="cart_remove")
     */
    public function remove(Product $product, CartService $cartService)
    {
        $cartService->remove($product);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("cart/delete/{id}", name="cart_delete")
     */
    public function delete(Product $product, CartService $cartService)
    {
        $cartService->delete($product);
        return $this->redirectToRoute("cart");
    }

    /**
     * @Route("cart/delete", name="cart_delete_all")
     */
    public function deleteAll( CartService $cartService)
    {
        $cartService->deleteAll();
        return $this->redirectToRoute("cart");
    }
    
    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request,  CartService $cartService,
        \Swift_Mailer $mailer): Response
    {   
        $_client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
        $client = null;

        if($this->getUser() && $this->getUser()->getRoles()[0] == "ROLE_USER") {
            $client = $this->getDoctrine()->getRepository(Client::class)->findOneBy(['email' => $this->getUser()->getEmail()]);
        }
        
        if($request->getMethod() == 'POST')
        {
            //insertion de la commande
            $command = new Command();
            $num_com = 'OSHT' . $_client->getId() . date('Ymdhms');
            $command->setNumberCommand($num_com);
            $command->setClient($_client);

            //insertion de l'objet paiement
            $_payment = new Payment();
            $_payment->setCommand($command);
            $_payment->setDelivryMethod($request->request->get('shipping'));

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->persist($command);
            $doctrine->persist($_payment);

            //insertion de command_product            
            foreach($cartService->getFullCart() as $line_command)
            {
                $command_product = new CommandProduct();
                $_product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(['id' => $line_command['product']]);
                $command_product->setCommand($command);
                $command_product->setProduct($_product);
                $command_product->setQuantity($line_command['quantity']);
                
                $doctrine->persist($command_product);
                $_product->setStock($_product->getStock() - $line_command['quantity']);
            }

            $doctrine->flush(); 

            $message = (new \Swift_Message('Votre facture'))
                ->setFrom('ecommerce.htos@gmail.com')
                ->setTo('toiwilouhassane@gmail.com')
                ->setBody(
                    'Bonjour, 

Vous venez de passer une commande sur notre site, 
vous trouvez ci-dessous toutes les informations relatives :

Mode de livraision : ' . $request->request->get('shipping') . 
'Adresse de livraison : ' . $_client->getAddress() . 
'Prix total : ' . $cartService->getTotal() . ' €'
                )
            ;

            $mailer->send($message);
            
            $this->addFlash('message', 'Votre paiement a été accepté ! vous avez reçu votre facture par mail.');
            return new RedirectResponse('/');
        }

        return $this->render('home/payment.html.twig', [
            'total' => $cartService->getTotal(),
            'totalItem' => $cartService->getTotalItem(),
            'items' => $cartService->getFullCart(),
            'client' => $client,

        ]);
    } 
}
