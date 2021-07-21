<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateClientFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'input-create-firstname',
                    'placeholder' => "Prenom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'input-create-lastname',
                    'placeholder' => "Nom"
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'input-create-email',
                    'placeholder' => "E-mail"
                ]
            ])
            ->add('address', TextType::class, [
                'attr' => [
                    'class' => 'input-create-adresse',
                    'placeholder' => "Adresse"
                ]
            ])
            ->add('phone', TelType::class, [
                'attr' => [
                    'class' => 'input-create-phone',
                    'placeholder' => "N° de téléphone"
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'input-create-password',
                    'placeholder' => "Mot de passe"
                ]
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-connexion btn-lg btn'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
