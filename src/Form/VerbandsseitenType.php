<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\Verbandsseiten;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Verbandsseiten>
 */
class VerbandsseitenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'name',
                options: [
                    'translation_domain' => 'admin',
                    'label' => 'verbandsseiten.name',
                    'help' => 'verbandsseiten.name.hint',
                ]
            )
            ->add(
                child: 'link',
                options: [
                    'translation_domain' => 'admin',
                    'label' => 'verbandsseiten.link',
                    'help' => 'verbandsseiten.link.hint',
                ]
            )
            ->add(
                child: 'linkName',
                options: [
                    'translation_domain' => 'admin',
                    'label' => 'verbandsseiten.link.name',
                    'help' => 'verbandsseiten.link.name.hint',
                ]
            )
            ->add('save', SubmitType::class, [
                'label' => 'document.save',
                'translation_domain' => 'admin',
                'attr' => [
                    'class' => 'btn btn-partei',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Verbandsseiten::class,
        ]);
    }
}
