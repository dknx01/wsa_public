<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\Wahlkreistyp;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Wahlkreistyp>
 */
class WahlkreistypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [new NotBlank()],
                'label' => 'menu.wahlkreistype.name',
                'help' => 'wahlkreistype.name.help',
                'translation_domain' => 'admin',
            ])
            ->add('abbreviation', TextType::class, [
                'required' => false,
                'label' => 'wahlkreistype.abbr',
                'help' => 'wahlkreistype.abbr.help',
                'translation_domain' => 'admin',
            ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'wahlkreistype.save',
                    'translation_domain' => 'admin',
                    'attr' => [
                        'class' => 'btn btn-partei',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Wahlkreistyp::class,
        ]);
    }
}
