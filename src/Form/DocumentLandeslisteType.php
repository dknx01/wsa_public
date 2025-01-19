<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Repository\WahlkreisRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentLandeslisteType extends AbstractType
{
    public function __construct(private readonly WahlkreisRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => $this->repository->getStates(),
                'label' => 'Bundesland',
                'help' => 'Für welches bundesland ist es die landesliste?',
            ])
            ->add('type', HiddenType::class, [
                'data' => 'Landesliste',
            ])
            ->add('file', FileType::class, [
                'label' => 'UU-Datei',
                'attr' => [
                    'accept' => '.pdf',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Beschreibung',
                'help' => 'Hinweise für die Benutzer z.B. wo die UU hingeschickt werden soll',
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Speichern', 'attr' => ['class' => 'btn btn-partei']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
