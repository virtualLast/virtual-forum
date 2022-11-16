<?php

namespace App\Controller\Admin;

use App\Entity\Question;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class QuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Question::class;
    }


    public function configureFields(string $pageName): iterable
    {

        yield IdField::new('id')->hideOnForm()->setFormTypeOption('disabled', true);
        yield TextField::new('title');
        yield TextareaField::new('content')->hideOnIndex();

        $createdAt = DateTimeField::new('createdAt')->setFormTypeOptions([
            'html5' => true,
            'widget' => 'single_text',
        ]);
        $updatedAt = DateTimeField::new('updatedAt')->setFormTypeOptions([
            'html5' => true,
            'widget' => 'single_text',
        ]);

        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            yield AssociationField::new('createdBy')->hideWhenUpdating();
            yield $createdAt->hideOnForm()->setFormTypeOption('disabled', true);
            yield $updatedAt->hideOnForm()->setFormTypeOption('disabled', true);
        }

    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['title'])
            ->setDefaultSort(['createdAt' => 'DESC', 'title' => 'ASC'])
            ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        if(in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
            $filters->add(EntityFilter::new('createdBy'));
        }
        return $filters
            ->add(DateTimeFilter::new('createdAt'))
            ->add(DateTimeFilter::new('updatedAt'))
            ;
    }
}
