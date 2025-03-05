<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\Statistic;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Statistic>
 */
class StatisticType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'name',
                options: [
                    'translation_domain' => 'admin',
                    'label' => 'statistic.name',
                ]
            )
            ->add(child: 'bundesland', options: [
                'label' => 'statistic.bundesland',
                'translation_domain' => 'admin',
            ])
            ->add(child: 'type', options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.type',
            ])
            ->add(child: 'approved', options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.approved',
            ])
            ->add(child: 'unapproved', options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.unapproved',
            ])
            ->add('updatedAt', null, [
                'widget' => 'single_text',
                'translation_domain' => 'admin',
                'label' => 'statistic.updatedAt',
            ])
            ->add('submit', SubmitType::class, [
                'translation_domain' => 'admin',
                'label' => 'form.save',
                'attr' => [
                    'class' => 'btn btn-partei',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Statistic::class,
        ]);
    }
}
