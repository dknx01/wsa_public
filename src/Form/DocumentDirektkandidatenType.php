<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Form;

use App\Entity\DocumentDirektkandidat;
use App\Repository\WahlkreisRepository;
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
 * @extends AbstractType<DocumentDirektkandidat>
 */
class DocumentDirektkandidatenType extends AbstractType
{
    public function __construct(
        private readonly WahlkreisRepository $repository,
        private readonly Security $security,
        private readonly Handler $handler,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('state', ChoiceType::class, [
                'choices' => array_merge(['bitte wählen' => ''], $this->repository->getStatesByUser($this->security->getUser())),
                'label' => 'document.state',
                'help' => 'document.dd.hint',
                'choice_translation_domain' => false,
                'translation_domain' => 'admin',
            ])
            ->add('area', ChoiceType::class, [
                'choices' => $this->handler->getWahlkreiseFormatted($this->security->getUser()),
                'choice_translation_domain' => false,
                'translation_domain' => 'admin',
                'label' => 'document.dd.area',
                'help' => 'document.dd.area.hint',
            ])
            ->add('type', HiddenType::class, [
                'data' => 'Direktkandidaten',
            ])
            ->add('file', FileType::class, [
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
            ->add('submit', SubmitType::class, [
                'label' => 'document.save',
                'translation_domain' => 'admin',
                'attr' => ['class' => 'btn btn-partei']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
