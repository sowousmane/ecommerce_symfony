<?php

namespace App\Controller;

use App\Service\AppService;
use App\Entity\User;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use Monolog\Handler\SwiftMailerHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/reset_password_email", name="reset_password_email")
     */
    public function resetPasswordEmail(Request $request,
        \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $datas = $form->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $datas['email']]);
            
            if(!$user){
                $this->addFlash('danger', 'Cette adresse n\'existe pas');
                return $this->redirectToRoute('reset_password_email');
            }

            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($user);
                $doctrine->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', 'Une erreur est survenue : ' . $e->getMessage());
                return $this->redirectToRoute('reset_password_email');
            }

            $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            
            $message = (new \Swift_Message('Mot de passe oubli??'))
                ->setFrom('ecommerce.htos@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    'Bonjour, 

Vous venez de demander une reinitialisation de votre mot de passe.
Cliquez sur ce lien ' . $url . ' pour continuer.'
                )
            ;

            $mailer->send($message);

            $this->addFlash('message', 'Un mail de reinitialisation de votre mot de passe vous a ??t?? envoy?? !');
            return $this->redirectToRoute('reset_password_email');
        }

        return $this->render('security/resetPasswordEmail.html.twig', [
            'emailForm' => $form->createView(),
        ]);
            
    }

    /**
     * @Route("/reset_password/{token}", name="reset_password")
     */
    public function resetPassword($token, Request $request, 
        UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token' => $token]);
        if(!$user){
            $this->addFlash('danger', 'Votre cl?? n\'existe pas');
            return $this->redirectToRoute('reset_password_email');
        }

        if($request->isMethod('POST')){
            if($request->request->get('password') == $request->request->get('confirmPassword')){
                $user->setResetToken(null);
                $user->setPassword(
                    $passwordEncoder->encodePassword($user,$request->request->get('password'))
                );
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($user);
                $doctrine->flush();

                $this->addFlash('message', 'Votre mot de passe a ??t?? mis ?? jour avec succ??s !');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('warning', 'Les mots de passe ne sont pas identiques');
            }
        }

        return $this->render('security/resetPassword.html.twig', [
            'token' => $token,
        ]);
    }
}
