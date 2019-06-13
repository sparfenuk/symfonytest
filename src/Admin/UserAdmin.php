<?php


namespace App\Admin;

use App\Controller\AppController;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ChoiceFieldMaskType;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Form\Type\Filter\DateTimeType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('promote', $this->getRouterIdParameter().'/promote')
            ->add('demote' ,$this->getRouterIdParameter().'/demote');
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $dt = new \DateTime();
        $formMapper->add('username', TextType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('status', ChoiceFieldMaskType::class, [
                'choices' => [
                    'User (without confirmed email)' => 0,
                    'User (with confirmed email' => 1,
                    'Moderator' => 2,
                    'Admin' => 3,
                ]
            ])
//            ->add('createdAt',DateTimeType::class,['attr' => ['value' => $dt->format('Y-m-d H:i:s')]])
//            ->add('updatedAt',DateTimeType::class,['attr' => ['value' => $dt->format('Y-m-d H:i:s')]])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username')
            ->add('status')
            ->add('email')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username')
            ->add('status')
            ->add('email')
            ->add('createdAt')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                    'promote' => [
                        'template' => 'Admin/promote.html.twig'
                    ],
                    'demote' =>[
                        'template' => 'Admin/demote.html.twig'
                    ]
                ],
            ]);
    }
}

