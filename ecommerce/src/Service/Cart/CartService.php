<?php

namespace App\Service\Cart;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add(Product $product) {
        $panier = $this->session->get('panier', []);

        $id = $product->getId();

        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
    
        $this->session->set('panier', $panier);
    }

    public function remove(Product $product) {

        $id = $product->getId();

        $panier = $this->session->get('panier', []);
        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            } else{

                unset($panier[$id]);
            }

        }
        
        $this->session->set('panier', $panier);
    }

    public function delete(Product $product) {


        $panier = $this->session->get('panier', []);
        if(!empty($panier[$product->getId()])){

            unset($panier[$product->getId()]);

        }
        
        $this->session->set('panier', $panier);
    }



    public function deleteAll() {

        $this->session->set('panier', []);
    }


     public function getFullCart() : array {

        $panier = $this->session->get('panier', []);

        $panierWithData = [];

        foreach($panier as $id => $quantity){
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }
        return $panierWithData;
     }

    public function getTotal() : float {

        $total = 0;
        
        foreach($this->getFullCart() as $item){

            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $total;
    } 
}