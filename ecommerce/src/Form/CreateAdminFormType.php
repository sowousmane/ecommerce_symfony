<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'attr' => [ 'placeholder' => "Prénom",
                ]
            ])
            ->add('lastname', TextType::class, [
                'attr' => [ 'placeholder' => "Nom",
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => "E-mail",
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => "Mot de passe",
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
            'data_class' => Admin::class,
        ]);
    }
}
