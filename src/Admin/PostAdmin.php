<?php


namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PostAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {

        $formMapper->add('text', TextareaType::class)
            ->add('authorId', IntegerType::class )
            ->add('photoName' , FileType::class)
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('text')->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('text')
            ->addIdentifier('createdAt')
            ->add('author.email')
            ->add('author.username')
            ->add('author.status')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }
}