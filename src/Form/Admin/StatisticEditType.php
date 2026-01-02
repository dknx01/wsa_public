<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin;

use App\Entity\WahlkreisNumber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<WahlkreisNumber>
 */
class StatisticEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                child: 'name',
                options: [
                    'translation_domain' => 'admin',
                    'label' => 'statistic.name',
                    'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                    'attr' => [
                        'readonly' => true,
                    ],
                    'disabled' => true,
                    'constraints' => [
                        new NotBlank(),
                    ],
                ]
            )
            ->add(child: 'approved', options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.approved',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
            ])
            ->add(child: 'unapproved', options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.unapproved',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
            ])
            ->add(child: 'comment', type: TextareaType::class, options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.comment',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'attr' => [
                    'cols' => 20,
                    'rows' => 3,
                ],
                'required' => false,
                'constraints' => [new NoSuspiciousCharacters()],
            ])
            ->add('updatedAt', DateType::class, [
                'widget' => 'single_text',
                'translation_domain' => 'admin',
                'label' => 'statistic.updatedAt',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'attr' => [
                    'readonly' => true,
                ],
                'data' => new \DateTimeImmutable(),
            ])
            ->add('deleted', CheckboxType::class, [
                'translation_domain' => 'admin',
                'label' => 'statistic.deleted',
                'required' => false,
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
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
            'data_class' => WahlkreisNumber::class,
        ]);
    }
}
