<?php

namespace App\Form;

use App\Entity\Comments;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Email"
                ]
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Pseudo"
                ]
            ])
            ->add('content', CKEditorType::class, [
                'label' => 'Commentaire',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => "Votre commentaire"
                ]
            ])
            ->add('rgpd', CheckboxType::class)
            ->add('parent', HiddenType::class, [
                'mapped' => false
            ])
            ->add('Envoyer', SubmitType::class, [
                'attr' => [
                    'class' => 'btn-envoiyer btn-lg btn'
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
