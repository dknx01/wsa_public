<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Wahlkreis;
use App\Security\ActiveUserVoter;
use App\User\Roles;
use App\Wahlkreis\Handler;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
#[IsGranted(Roles::ROLE_ADMIN->name)]
class WahlkreisCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly Handler $handler,
        private readonly Security $security,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Wahlkreis::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('name', $this->translator->trans(id: 'wahlkreis.name', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'wahlkreis.name.help', domain: 'admin'))
            ->setRequired(true);
        yield IntegerField::new('number', $this->translator->trans(id: 'wahlkreis.number', domain: 'admin'))
            ->setHelp($this->translator->trans(id: 'wahlkreis.number.help', domain: 'admin'))
            ->setRequired(false)
            ->setEmptyData(null);
        yield ChoiceField::new('state', $this->translator->trans('wahlkreis.state', domain: 'admin'))
            ->setChoices(array_merge(['bitte wählen' => ''], $this->handler->getStates($this->security->getUser())))
            ->autocomplete()
            ->setHelp($this->translator->trans('wahlkreis.state.help', domain: 'admin'))
            ->setRequired(true);
        yield IntegerField::new('year', $this->translator->trans('wahlkreis.year', domain: 'admin'))
            ->setHelp($this->translator->trans('wahlkreis.year.help', domain: 'admin'))
            ->setRequired(true);
    }
}
