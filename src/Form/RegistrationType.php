<?php

namespace App\Form;

use App\Entity\Utilisateur;
use App\Entity\TypeUtilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('email')
            ->add('password',PasswordType::class)
            ->add('confirmMdp',PasswordType::class)
            ->add('adresse')
            ->add('login')
            ->add('numero')
            ->add('typeUtilisateur', EntityType::class, [
                'class' => TypeUtilisateur::class,
                'choice_label' => 'nom'
            ])
            ->add('description')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
