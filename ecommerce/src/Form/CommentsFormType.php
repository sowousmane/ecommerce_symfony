<?php

namespace App\Form;

use App\Entity\Comments;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre e-mail"
                ]
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre pseudo"
                ]
            ])
            ->add('title', TextType::class, [
                'label' => 'Objet',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "L'objet de votre demande",
                    'value' => "ceci est une réponse"
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Vos coommentaire ici',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre commentaire"
                ]
            ])
           
            ->add('parent', HiddenType::class, [
                'mapped' => false
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-envoyer btn-lg btn'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comments::class,
        ]);
    }
}
