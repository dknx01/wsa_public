<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin\Document;

use App\Dto\Document\WahlkreisDto;
use App\Wahlkreis\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<WahlkreisDto>
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
            ->add('wahlkreis',
                ChoiceType::class,
                [
                    'choice_translation_domain' => false,
                    'translation_domain' => 'admin',
                    'label' => 'wahlkreis.name',
                    'choice_value' => 'id',
                    'choice_label' => 'name',
                    'choices' => $this->handler->findAllByUser($this->security->getUser()),
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => WahlkreisDto::class,
            'label' => 'navigation.wahlkreis',
            'translation_domain' => 'admin',
        ]);
    }
}
