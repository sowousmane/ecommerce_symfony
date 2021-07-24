<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\History;
use App\Entity\Product;
use App\Form\CreateProductFormType;
use App\Service\AppService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/add_product", name="add_product")
     */
    public function add(Request $request): Response
    {
       
        try
        {
            $product = new Product();
            $history = new History();
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            
            if($request->getMethod() == 'POST')
            {
                $product->setName($request->request->get('name'));
                $product->setDescription($request->request->get('description'));
                $product->setPrice($request->request->get('price'));
                $product->setStock($request->request->get('stock'));
                $product->setImage($request->request->get('image'));
                $product->setCategory(
                    $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $request->request->get('category')])
                );

                $history->setTitle('Ajout d\'un produit');
                $history->setContent(
                    "Informations sur le produit ajouté : 
                    Nom : " . $product->getName() . "
                    Description : " . $product->getDescription() . "
                    Prix : " . $product->getPrice()
                );
                $history->setSentAt(date('l jS \of F Y h:i:s A'));
                $history->setColor('alert alert-success');

                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($product);
                $doctrine->persist($history);
                $doctrine->flush();

                return $this->render('admin/response.html.twig', [
                    'response' => 'Le produit a été créé avec succès !',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-success',
                ]);
            }

            return $this->render('admin/addProduct.html.twig', [
                'categories' => $categories,
            ]);
        } 
        catch(\Exception $e) 
        {
            return $this->render('admin/response.html.twig', [
                'response' =>  $e->getMessage(),
                'current_page' => 'Réponse',
                'class' => 'alert alert-danger',
            ]);
        }
    }
}
