<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


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
            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['email' => $this->getUser()->getEmail()]);

            if($request->getMethod() == 'POST')
            {
                $req = $request->request;
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

                $this->addFlash('maessage', 'Votre profil a été mis à jour !');

                return $this->render('client/index.html.twig', [
                    'user' => $client,
                ]);
            }

            return $this->render('client/index.html.twig', [
                'user' => $client,
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
