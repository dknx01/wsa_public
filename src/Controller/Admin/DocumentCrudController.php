<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Document;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use App\Wahlkreis\Handler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/verwaltung/')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
#[IsGranted(Roles::ROLE_ADMIN->name)]
class DocumentCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Document::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield ChoiceField::new('state', $this->translator->trans(id: 'document.state', domain: 'admin'))
            ->setChoices(array_merge(['bitte wählen' => ''], $this->handler->getStates($this->security->getUser())))
            ->autocomplete()
            ->setHelp($this->translator->trans(id: 'document.state.help', domain: 'admin'));
        yield TextField::new('type', $this->translator->trans(id: 'document.type', domain: 'admin'))
            ->setDisabled();
        yield AssociationField::new('wahlkreis', $this->translator->trans(id: 'document.dd.area', domain: 'admin'))
            ->autocomplete()
            ->setHelp($this->translator->trans(id: 'document.dd.area.hint', domain: 'admin'))
            ->renderAsHtml();
        yield TextField::new('wkName', $this->translator->trans(id: 'wahlkreis.name', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'wahlkreis.name.help', domain: 'admin'))
            ->setDisabled();
        yield IntegerField::new('wkNr', $this->translator->trans(id: 'wahlkreis.number', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'wahlkreis.number.help', domain: 'admin'))
            ->setDisabled();
        yield TextField::new('name', $this->translator->trans(id: 'document.name', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'document.name.help', domain: 'admin'));
        yield TextEditorField::new('description', $this->translator->trans(id: 'document.hint', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'document.hint.help', domain: 'admin'));
    }
}
