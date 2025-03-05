<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<User>
 */
class RegistrationFormType extends AbstractType
{
    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'email',
                options: [
                    'label' => 'email',
                    'translation_domain' => 'registration',
                ]
            )
            ->add('plainPassword', PasswordType::class, [
                'label' => 'password',
                'translation_domain' => 'registration',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Dein Passwort',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Passwort zu kurz',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ->add(
            'captcha',
            TextType::class,
            [
                'mapped' => false,
                'required' => true,
                'translation_domain' => 'registration',
                'label' => 'captcha',
                'help' => 'captcha.help',
                'constraints' => [
                    new EqualTo(value: $this->requestStack->getSession()->get('captcha') ?? '', message: 'captcha.not_matching'),
                ],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
