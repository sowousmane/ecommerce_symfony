<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Form\CreateClientFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//use App\Entity\Product;

class HomeController extends AbstractController
{
    /**
     * @Route("/create_client", name="create_client")
     */
    public function create_client(Request $request, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try{

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
            }
            
            return $this->render('home/createClient.html.twig', [
                'clientForm' => $form->createView(),
            ]);
        }
        catch(\Exception $e){
            $this->addFlash('danger', $e->getMessage());
        }
    }
}
