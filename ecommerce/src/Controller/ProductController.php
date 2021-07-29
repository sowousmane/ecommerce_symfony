<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\History;
use App\Entity\Product;
use App\Form\CreateProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CreateCategoryFormType;

class ProductController extends AbstractController
{
    /**
     * @Route("/products_by_category/{id}", name="products_by_category")
     */
    public function productsByCategory($id): Response
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
        ]);
    }

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
     * @Route("/create_category", name="create_category")
     */
    public function createCategory(Request $request): Response
    {
        try{

            $category = new Category();
            $history = new History();
            $form = $this->createForm(CreateCategoryFormType::class, $category);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $history->setTitle('Création d\'une catégorie de produit');
                $history->setContent(
                    "Informations de la catégorie créée : 
                    Nom : " . $category->getName() 
                );
                $history->setSentAt(date('l jS \of F Y h:i:s A'));
                $history->setColor('alert alert-success');

                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($category);
                $doctrine->persist($history);
                $doctrine->flush();

                return $this->render('admin/response.html.twig', [
                    'response' => 'La catégorie a été créée avec succès !',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-success',
                ]);
            }

            return $this->render('admin/createCategory.html.twig', [
                'categoryForm' => $form->createView(),
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
     * @Route("/delete_category/{id}", name="delete_category")
     */
    public function deleteCategory($id): Response
    {
       
        try
        {
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $id]);
            
            if(!$category){
                return $this->render('admin/response.html.twig', [
                    'response' => 'La catégorie dont l\'id est ' . $id . ' n\'existe pas',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-danger',
                ]);
            }

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->remove($category);
            $doctrine->flush();

            return $this->render('admin/response.html.twig', [
                'response' => 'La catégorie dont l\'id est ' . $id . ' a été supprimée !',
                'current_page' => 'Réponse',
                'class' => 'alert alert-success',
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