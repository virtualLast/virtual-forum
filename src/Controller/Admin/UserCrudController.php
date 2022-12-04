<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

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
            ->setDefaultSort(['createdAt' => 'DESC', 'username' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm()->setFormTypeOption('disabled', true);
        yield TextField::new('username')->setFormTypeOption('disabled', true);
        yield TextField::new('name')->hideOnIndex();
        yield EmailField::new('email');
        yield HiddenField::new('password')->hideOnIndex()->setDisabled();
        yield TextField::new('plainPassword')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password',
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                ]
            ])
            ->setRequired($pageName === Crud::PAGE_NEW)
            ->onlyOnForms();
        yield ChoiceField::new('roles')->hideOnIndex()->setChoices([
            'User' => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN'
        ])->allowMultipleChoices();
        yield DateField::new('createdAt')->hideOnIndex()->hideWhenCreating()->setDisabled();
        yield DateField::new('updatedAt')->hideOnIndex()->hideWhenCreating()->setDisabled();
    }
}
