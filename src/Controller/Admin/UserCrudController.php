<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['name', 'username'])
            ->setDefaultSort(['createdAt' => 'DESC', 'username' => 'ASC'])
            ;
    }

//    public function configureFields(string $pageName): iterable
//    {
//        yield IdField::new('id')->hideOnForm()->setFormTypeOption('disabled', true);
//        yield TextField::new('username');
//    }
}
