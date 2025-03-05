<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\DocumentLandesliste;
use App\Wahlkreis\Handler;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<DocumentLandesliste>
 */
class DocumentLandeslisteType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
        private readonly Handler $handler,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => $this->handler->getStates($this->security->getUser()),
                'label' => 'document.state',
                'help' => 'document.state.help',
                'choice_translation_domain' => false,
                'translation_domain' => 'admin',
            ])
            ->add('type', HiddenType::class, [
                'data' => 'Landesliste',
            ])
            ->add('file', FileType::class, [
                'label' => 'document.file',
                'translation_domain' => 'admin',
                'attr' => [
                    'accept' => '.pdf',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'document.hint',
                'help' => 'document.hint.help',
                'translation_domain' => 'admin',
                'required' => false,
            ])
            ->add('submit', SubmitType::class,
                [
                    'label' => 'document.save',
                    'translation_domain' => 'admin',
                    'attr' => ['class' => 'btn btn-partei'],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
