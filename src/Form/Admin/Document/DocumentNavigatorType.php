<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin\Document;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Flow\Type\FinishFlowType;
use Symfony\Component\Form\Flow\Type\NextFlowType;
use Symfony\Component\Form\Flow\Type\PreviousFlowType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<null>
 */
class DocumentNavigatorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('back', PreviousFlowType::class, [
            'label' => 'navigation.back',
            'translation_domain' => 'admin',
            'attr' => ['class' => 'btn btn-partei'],
        ]);
        $builder->add('next', NextFlowType::class, [
            'label' => 'navigation.next',
            'translation_domain' => 'admin',
            'attr' => ['class' => 'btn btn-partei'],
        ]);
        $builder->add('finish', FinishFlowType::class, [
            'label' => 'navigation.save',
            'translation_domain' => 'admin',
            'attr' => ['class' => 'btn btn-partei'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'mapped' => false,
            'priority' => -100,
        ]);
    }
}
