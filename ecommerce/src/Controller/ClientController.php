<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Command;
use App\Entity\History;
use App\Entity\User;
use App\Form\CreateClientFormType;
use App\Form\ProfilePictureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ClientController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function index(UserPasswordEncoderInterface $passwordEncoder, 
        Request $request): Response
    {
        try
        {
            $client = $this->getDoctrine()->getRepository(Client::class)
                ->findOneBy(['email' => $this->getUser()->getEmail()]);
            
            if($client->getPicture() != null)
            {
                $_picture = 'clients/' . $client->getPicture();
            } 
            else 
            {
                $_picture = '_profile.png';
            }

            $commands = $this->getDoctrine()->getRepository(Command::class)->findBy(['client' => $client]);

            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getEmail()]);
            $form = $this->createForm(ProfilePictureFormType::class);
            $form->handleRequest($request);

            if($request->getMethod() == 'POST')
            {
                $req = $request->request;

                if($form->isSubmitted() && $form->isValid())
                {
                    $picture = $form->get('picture')->getData();

                    if($picture)
                    {
                        $newFilename = 'client' . $client->getId() . '.' . $picture->guessExtension();

                        $picture->move(
                            $this->getParameter('clients'),
                            $newFilename
                        );

                        $client->setPicture($newFilename);
                        $doctrine = $this->getDoctrine()->getManager();
                        $doctrine->persist($client);
                        $doctrine->flush();

                        $_picture = 'client/' . $newFilename;

                        return $this->render('client/index.html.twig', [
                            'user' => $client,
                            'profilePictureForm' => $form->createView(),
                            '_picture' => $_picture,
                            '_count' => count($commands),
                        ]);
                    }
                } 
                else 
                {
                    $client->setFirstname($req->get('firstname'));
                    $client->setLastname($req->get('lastname'));
                    $client->setEmail($req->get('email'));
                    $client->setAddress($req->get('address'));
                    $client->setPhone($req->get('phone'));
                    $client->setPassword($req->get('password'));
                    
                    $user->setEmail($client->getEmail());
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $client->getPassword()
                        )
                    );

                    $doctrine = $this->getDoctrine()->getManager();
                    $doctrine->persist($client);
                    $doctrine->persist($user);
                    $doctrine->flush();

                    return $this->render('client/index.html.twig', [
                        'user' => $client,
                        'profilePictureForm' => $form->createView(),
                        '_picture' => $_picture,
                        '_count' => count($commands),
                    ]);
                }
            }

            return $this->render('client/index.html.twig', [
                'user' => $client,
                'profilePictureForm' => $form->createView(),
                '_picture' => $_picture,
                '_count' => count($commands),
            ]);
        }
        catch(\Exception $e)
        {
            return $this->render('error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
        
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
}

    