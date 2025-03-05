<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Statistic;
use App\Security\ActiveUserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/Verwaltung')]
#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class StatisticCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Statistic::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setDisabled(),
            IntegerField::new('approved', 'Bestätigte'),
            IntegerField::new('unapproved', 'Unbestätigte'),
            TextField::new('bundesland')->setDisabled(),
            TextField::new('type')->setDisabled(),
        ];
    }
}
