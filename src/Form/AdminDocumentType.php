<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\Document;
use App\Entity\Wahlkreis;
use App\Repository\WahlkreisRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Document>
 */
class AdminDocumentType extends AbstractType
{
    public function __construct(private readonly WahlkreisRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'document.name',
                    'translation_domain' => 'admin',
                    'required' => true,
                    'help' => 'document.name.help',
                ]
            )
            ->add(
                'state',
                ChoiceType::class,
                [
                    'label' => 'document.state',
                    'translation_domain' => 'admin',
                    'choices' => array_merge(['bitte wählen' => ''], $this->repository->getStates()),
                    'choice_translation_domain' => false,
                    'help' => 'document.state.help',
                    'required' => true,
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'document.type',
                    'translation_domain' => 'admin',
                    'choices' => ['Landesliste' => 'Landesliste', 'Direktkandidaten' => 'Direktkandidaten'],
                    'choice_translation_domain' => false,
                    'required' => true,
                    'help' => 'document.type',
                ]
            )
            ->add(
                'fileName',
                TextType::class,
                [
                    'label' => 'document.document_name',
                    'translation_domain' => 'admin',
                    'required' => true,
                ]
            )
            ->add(
                'wahlkreis',
                EntityType::class,
                [
                    'class' => Wahlkreis::class,
                    'label' => 'document.dd.area',
                    'translation_domain' => 'admin',
                    'required' => false,
                ]
            )
            ->add('file', FileType::class, [
                'label' => 'document.file',
                'translation_domain' => 'admin',
                'attr' => [
                    'accept' => '.pdf',
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'document.hint',
                'help' => 'document.hint.help',
                'translation_domain' => 'admin',
                'required' => false,
            ])
            ->add('submit',
                SubmitType::class,
                [
                    'label' => 'document.save',
                    'translation_domain' => 'admin',
                    'attr' => ['class' => 'btn btn-partei'],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
