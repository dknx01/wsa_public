<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Dto\WahlkreisType;
use App\Entity\Wahlkreis;
use App\Wahlkreis\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

/**
 * @extends AbstractType<Wahlkreis>
 */
class WahlkreisFormType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly Handler $handler,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'wahlkreis.number',
                    'help' => 'wahlkreis.number.help',
                    'translation_domain' => 'admin',
                ]
            )
            ->add('name',
                TextType::class,
                [
                    'constraints' => [
                        new NotBlank(),
                    ],
                    'label' => 'wahlkreis.name',
                    'help' => 'wahlkreis.name.help',
                    'translation_domain' => 'admin',
                ]
            )
            ->add('state',
                ChoiceType::class,
                [
                    'choices' => array_merge(['bitte wählen' => ''], $this->handler->getStates($this->security->getUser())),
                    'choice_translation_domain' => false,
                    'translation_domain' => 'admin',
                    'label' => 'wahlkreis.state',
                    'help' => 'wahlkreis.state.help',
                ],
            )
            ->add('type',
                ChoiceType::class,
                [
                    'choices' => [
                        WahlkreisType::BTW->value => WahlkreisType::BTW->value,
                        WahlkreisType::LTW->value => WahlkreisType::LTW->value,
                        WahlkreisType::KW->value => WahlkreisType::KW->value,
                        WahlkreisType::KW_KREIS->value => WahlkreisType::KW_KREIS->value,
                        WahlkreisType::KW_KOMMUNE->value => WahlkreisType::KW_KOMMUNE->value,
                        WahlkreisType::KW_REG_BEZIRK->value => WahlkreisType::KW_REG_BEZIRK->value,
                        WahlkreisType::KW_VERBAND->value => WahlkreisType::KW_VERBAND->value,
                    ],
                    'choice_translation_domain' => false,
                    'translation_domain' => 'admin',
                    'label' => 'wahlkreis.type',
                    'help' => 'wahlkreis.type.help',
                ]
            )
            ->add('threshold',
                IntegerType::class,
                [
                    'required' => false,
                    'label' => 'wahlkreis.threshold',
                    'help' => 'wahlkreis.threshold.help',
                    'translation_domain' => 'admin',
                ]
            )
            ->add('year', TextType::class, [
                'label' => 'wahlkreis.year',
                'help' => 'wahlkreis.year.help',
                'translation_domain' => 'admin',
                'empty_data' => (new \DateTime())->format('Y'),
                'constraints' => [
                    new NotBlank(),
                    new Range(
                        min: 2025,
                        max: (int) (new \DateTime())->modify('+1 year')->format('Y')),
                ],
            ])
            ->add('save',
                SubmitType::class,
                [
                    'label' => 'wahlkreis.save',
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
        ]);
    }
}
