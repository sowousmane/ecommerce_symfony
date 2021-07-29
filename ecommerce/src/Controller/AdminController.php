<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\History;
use App\Entity\User;
use App\Form\CreateAdminFormType;
use App\Form\ProfilePictureFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    /**
     * @Route("/create_admin", name="create_admin")
     */
    public function createAdmin(Request $request, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try
        {
            $admin = new Admin();
            $user = new User();
            $history = new History();
            $form = $this->createForm(CreateAdminFormType::class, $admin);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $user->setEmail($admin->getEmail());
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $admin->getPassword()
                    )
                );
                $user->setRoles(['ROLE_ADMIN']);

                $history->setTitle('Création d\'un administrateur');
                $history->setContent(
                    "Informations de l'administrateur créé : 
                    Prénom : " . $admin->getFirstname() . "
                    Nom : " . $admin->getLastname() . "
                    E-mail : " . $admin->getEmail()
                );
                $history->setSentAt(date('l jS \of F Y h:i:s A'));
                $history->setColor('alert alert-success');

                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($admin);
                $doctrine->persist($user);
                $doctrine->persist($history);
                $doctrine->flush();

                return $this->render('admin/response.html.twig', [
                    'response' => 'L\'administrateur a été créé avec succès !',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-success',
                ]);
            }
            return $this->render('admin/createAdmin.html.twig', [
                'adminForm' => $form->createView(),
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
     * @Route("/edit_admin/{id}", name="edit_admin")
     */
    public function editAdmin(Request $request, $id, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        try
        {
            $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy(['id' => $id]);
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $admin->getEmail()]);
            $form = $this->createForm(CreateAdminFormType::class, $admin);
            $form->handleRequest($request);
            $_form = $this->createForm(ProfilePictureFormType::class);
            $_form->handleRequest($request);

            if($_form->isSubmitted() && $_form->isValid())
            {
                $picture = $_form->get('picture')->getData();

                if($picture)
                {
                    $newFilename = 'admin' . $id . '.' . $picture->guessExtension();

                    $picture->move(
                        $this->getParameter('admins'),
                        $newFilename
                    );

                    $admin->setPicture($newFilename);
                    $doctrine = $this->getDoctrine()->getManager();
                    $doctrine->persist($admin);
                    $doctrine->flush();

                    return $this->render('admin/editAdmin.html.twig', [
                        'editAdminForm' => $form->createView(),
                        'editPictureForm' => $_form->createView(),
                    ]);
                }
            }

            if($form->isSubmitted() && $form->isValid())
            {
                $user->setEmail($admin->getEmail());
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $admin->getPassword()
                    )
                );

                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($admin);
                $doctrine->persist($user);
                $doctrine->flush();

                return $this->render('admin/response.html.twig', [
                    'response' => 'Votre profil a été mis à jour !',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-success',
                ]);
            }

            return $this->render('admin/editAdmin.html.twig', [
                'editAdminForm' => $form->createView(),
                'editPictureForm' => $_form->createView(),
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
     * @Route("/delete_admin/{id}", name="delete_admin")
     */
    public function deleteAdmin($id): Response
    {
        try{
            $admin = $this->getDoctrine()->getRepository(Admin::class)->findOneBy(['id' => $id]);
            
            if(!$admin){
                return $this->render('admin/response.html.twig', [
                    'response' => 'L\'administrateur dont l\'id est ' . $id . ' n\'existe pas',
                    'current_page' => 'Réponse',
                    'class' => 'alert alert-danger',
                ]);
            }

            $doctrine = $this->getDoctrine()->getManager();
            $doctrine->remove($admin);
            $doctrine->flush();

            return $this->render('admin/response.html.twig', [
                'response' => 'L\'administrateur dont l\'id est ' . $id . ' a été supprimé !',
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
}