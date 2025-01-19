<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin;

use App\Entity\SupportNumbersDirektkandidat;
use App\Entity\SupportNumbersLandesliste;
use App\Entity\Wahlkreis;
use App\Repository\SupportNumbersRepository;
use App\Repository\WahlkreisRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NoSuspiciousCharacters;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class StatisticType extends AbstractType implements DataMapperInterface
{
    public function __construct(
        private readonly WahlkreisRepository $wahlkreisRepo,
        private readonly SupportNumbersRepository $supportNumbersRepo,
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
                    'constraints' => [
                        new NotBlank(),
                        new Callback([$this, 'validateName']),
                    ],
                ]
            )
            ->add(child: 'bundesland', type: ChoiceType::class, options: [
                'mapped' => false,
                'label' => 'statistic.bundesland',
                'translation_domain' => 'admin',
                'label_attr' => ['class' => 'col-sm-2 col-form-label'],
                'choices' => array_merge(['---' => ''], $this->wahlkreisRepo->getStates()),
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
                'choices' => ['Landesliste' => 'Landesliste', 'Direktkandidat' => 'Direktkandidat'],
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
            ->add('updatedAt', null, [
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
            ->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SupportNumbersLandesliste::class,
        ]);
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        // there is no data yet, so nothing to prepopulate
        if (null === $viewData) {
            return;
        }

        // invalid data type
        if (!$viewData instanceof SupportNumbersLandesliste) {
            throw new UnexpectedTypeException($viewData, SupportNumbersLandesliste::class);
        }

        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);

        // initialize form field values
        $forms['name']->setData($viewData->getName());
        $forms['wahlkreis']->setData($viewData->getWahlkreis());
        $forms['type']->setData($viewData->getType());
        $forms['approved']->setData($viewData->getApproved());
        $forms['unapproved']->setData($viewData->getUnapproved());
        $forms['comment']->setData($viewData->getComment());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        /** @var FormInterface[] $forms */
        $forms = iterator_to_array($forms);
        $name = $forms['name']->getData();
        $viewData = $this->supportNumbersRepo->findOneBy(['name' => $name]);
        if (null === $viewData) {
            $viewData = match ($forms['type']->getData()) {
                'Landesliste' => new SupportNumbersLandesliste(),
                'Direktkandidat' => new SupportNumbersDirektkandidat(),
                default => throw new UnexpectedTypeException($forms['type']->getData(), 'string'),
            };
        }

        $viewData->setApproved($forms['approved']->getData());
        $viewData->setUnapproved($forms['unapproved']->getData());
        $viewData->setComment($forms['comment']->getData());
        $viewData->setName($forms['name']->getData());
        if ($viewData instanceof SupportNumbersDirektkandidat) {
            $viewData->setWahlkreis($this->wahlkreisRepo->findOneBy(['id' => $forms['wahlkreis']->getData()]));
        }
    }

    public function validateName(mixed $payload, ExecutionContextInterface $context): void
    {
        $payload = trim($payload);
        dump($payload);
        if (str_contains($payload, '---') || \in_array($payload, ['Landesliste', 'Direktkandidat'])) {
            $context->buildViolation($this->translator->trans(id: 'statistic.invalid.name', domain: 'admin'))
            ->atPath('name')
            ->addViolation();
        }
    }
}
