<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form\Admin\Document;

use App\Document\DocumentType;
use App\Dto\Document\FileDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<FileDto>
 */
class DocumentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type',
                ChoiceType::class,
                [
                    'choice_translation_domain' => false,
                    'translation_domain' => 'admin',
                    'label' => 'wahlkreis.type',
                    'choices' => [DocumentType::LISTE->value => DocumentType::LISTE->value, DocumentType::DIREKTKANDIDAT->value => DocumentType::DIREKTKANDIDAT->value],
                ]
            )->add('file', FileType::class, [
                'label' => 'document.file',
                'translation_domain' => 'admin',
                'attr' => [
                    'accept' => '.pdf',
                ],
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'admin',
                'label' => 'document.hint',
                'help' => 'document.hint.help',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FileDto::class,
            'label' => 'navigation.file',
            'translation_domain' => 'admin',
        ]);
    }
}
