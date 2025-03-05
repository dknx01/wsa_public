<?php

declare(strict_types=1);

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<User>
 */
class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('password', PasswordType::class, [
            'label' => 'password.old',
            'mapped' => false,
            'translation_domain' => 'admin',
        ])
            ->add('passwordNew', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'password.password', 'hash_property_path' => 'password'],
                'second_options' => ['label' => 'password.repeat'],
                'mapped' => false,
                'translation_domain' => 'admin',
            ])
        ->add('submit', SubmitType::class, [
            'label' => 'password.save',
            'translation_domain' => 'admin',
            'attr' => [
                'class' => 'btn btn-partei',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
