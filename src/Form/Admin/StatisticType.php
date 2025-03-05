<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin;

use App\Entity\SupportNumbersLandesliste;
use App\Entity\Wahlkreis;
use App\SupportNumbers\Type;
use App\Wahlkreis\Handler;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
class StatisticType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly Handler $handler,
        private readonly Security $security,
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
                    'constraints' => [
                        new NotBlank(),
                        new Callback([$this, 'validateName']),
                    ],
                ]
            )
            ->add(child: 'bundesland', type: ChoiceType::class, options: [
                'label' => 'statistic.bundesland',
                'translation_domain' => 'admin',
                'choice_translation_domain' => false,
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'choices' => array_merge(['---' => ''], $this->handler->getStates($this->security->getUser())),
            ])
            ->add(child: 'wahlkreis', type: EntityType::class, options: [
                'class' => Wahlkreis::class,
                'label' => 'statistic.wahlkreis',
                'translation_domain' => 'admin',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'required' => false,
            ]
            )
            ->add(child: 'type', type: ChoiceType::class, options: [
                'translation_domain' => 'admin',
                'label' => 'statistic.type',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'choices' => [
                    'bitte wählen' => '',
                    Type::LL_BTW->value => Type::LL_BTW->name,
                    Type::DK_BTW->value => Type::DK_BTW->name,
                    Type::LL_KW->value => Type::LL_KW->name,
                    Type::DK_KW->value => Type::DK_KW->name,
                    Type::LL_LTW->value => Type::LL_LTW->name,
                    Type::DK_LTW->value => Type::DK_LTW->name,
                ],
                'choice_translation_domain' => false,
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
