<?php


namespace App\Admin;



use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_page' => 1,
        '_sort_order' => 'ASC',
        '_sort_by' => 'verifiedAdminId',
    ];


    protected function configureFormFields(FormMapper $formMapper)
    {

        //$container = $this->getConfigurationPool()->getContainer();

        $formMapper->add('text', TextareaType::class)
            ->add('author', EntityType::class,['class' => User::class,])//'attr' => ['value' => $container->get('security.token_storage')->getToken()->getUser()->getId()]] )
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('text')
            ->add('createdAt')
            ->add('verifiedAdminId');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->add('verify', $this->getRouterIdParameter().'/verify');

    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('text')
            ->addIdentifier('createdAt')
            ->add('author.email')
            ->add('author.username')
            ->add('author.status')
            ->addIdentifier('verifiedAdminId')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'verify' => [
                        'template' => 'Admin/verify.html.twig'
                    ]
                ]
            ]);
    }
}