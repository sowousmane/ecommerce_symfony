<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppService
{
    private Request $request;
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct()
    {
    }

    public function createUser($form, $role, $doctrine, $_user, $flashe)
    {
        $user = new User();
        $form->handleRequest($this->request);
        
        $user->setEmail($_user->getEmail());
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user,
                $_user->getPassword()
            )
        );
        $user->setRoles([$role]);
        $doctrine->persist($_user);
        $doctrine->persist($user);
        $doctrine->flush();

        $flashe;
    }
}