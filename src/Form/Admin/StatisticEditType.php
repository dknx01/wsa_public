<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin;

use App\Entity\SupportNumbersLandesliste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends AbstractType<SupportNumbersLandesliste>
 */
class StatisticEditType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

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
                        new Callback([$this, 'validateName']),
                    ],
                ]
            )
            ->add(child: 'bundesland', type: TextType::class, options: [
                'label' => 'statistic.bundesland',
                'translation_domain' => 'admin',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'disabled' => true,
            ])
            ->add(child: 'wahlkreis', type: TextType::class, options: [
                'label' => 'statistic.wahlkreis',
                'translation_domain' => 'admin',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'required' => false,
                'disabled' => true,
                'getter' => fn (?SupportNumbersLandesliste $value) => $value?->getWahlkreis()?->getName(),
            ]
            )
            ->add(child: 'type', type: TextType::class, options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.type',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'disabled' => true,
            ])
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
            'data_class' => SupportNumbersLandesliste::class,
        ]);
    }

    public function validateName(mixed $payload, ExecutionContextInterface $context): void
    {
        $payload = trim($payload);
        if (str_contains($payload, '---') || \in_array($payload, ['Landesliste', 'Direktkandidat'])) {
            $context->buildViolation($this->translator->trans(id: 'statistic.invalid.name', domain: 'admin'))
            ->atPath('name')
            ->addViolation();
        }
    }
}
