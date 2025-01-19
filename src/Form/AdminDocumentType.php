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
                    'label' => 'Name',
                    'required' => true,
                    'attr' => [
                        'help' => 'Name des Formulars',
                    ],
                ]
            )
            ->add(
                'state',
                ChoiceType::class,
                [
                    'label' => 'Bundesland',
                    'choices' => array_merge(['bitte wählen' => ''], $this->repository->getStates()),
                    'help' => 'Für welches bundesland ist es die Landesliste?',
                    'required' => true,
                ]
            )
            ->add(
                'type',
                ChoiceType::class,
                [
                    'label' => 'Art des Dokuments',
                    'choices' => ['Landesliste' => 'Landesliste', 'Direktkandidaten' => 'Direktkandidaten'],
                    'required' => true,
                    'help' => 'Art des Dokumentes',
                ]
            )
            ->add(
                'fileName',
                TextType::class,
                [
                    'label' => 'Name des Dokumentes',
                    'required' => true,
                ]
            )
            ->add(
                'wahlkreis',
                EntityType::class,
                [
                    'class' => Wahlkreis::class,
                    'label' => 'Wahlkreis',
                    'required' => false,
                ]
            )
            ->add('file', FileType::class, [
                'label' => 'UU-Datei',
                'attr' => [
                    'accept' => '.pdf',
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'help' => 'Hinweise für die Benutzer z.B. wo die UU hingeschickt werden soll',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Speichern', 'attr' => ['class' => 'btn btn-partei']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
