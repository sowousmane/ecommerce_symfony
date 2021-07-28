<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
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
                    ]);
                }
            }

            return $this->render('client/index.html.twig', [
                'user' => $client,
                'profilePictureForm' => $form->createView(),
                '_picture' => $_picture,
            ]);
        }
        catch(\Exception $e)
        {
            return $this->render('error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
        
    }
}
