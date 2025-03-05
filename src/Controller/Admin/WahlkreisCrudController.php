<?php

/*
 * This file is part of the Weltsozialamt website.
 * (c) dknx01/wsa_public
 */

namespace App\Controller\Admin;

use App\Entity\Wahlkreis;
use App\Security\ActiveUserVoter;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(ActiveUserVoter::ACTIVE_USER)]
class WahlkreisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Wahlkreis::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            IntegerField::new('number'),
            TextField::new('state'),
        ];
    }
}
