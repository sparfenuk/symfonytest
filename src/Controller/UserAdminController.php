<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAdminController extends CRUDController
{



//    * @Rest\Route("/admin/app/user/{id}/promote",name="promote")
    /**

     * @param $id
     * @return RedirectResponse
     */
    public function promoteAction($id){
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the user with id: %s', $id));
        }

        if($object->getStatus() == 'ROLE_USER')
            $object->setStatus(2);
        else if($object->getStatus() == 'ROLE_MODERATOR')
            $object->setStatus(3);
        else throw new NotFoundHttpException(sprintf('unable to promote the user with id: %s', $id));

        $this->admin->update($object);

        $this->addFlash('success','user promoted');
        return new RedirectResponse($this->admin->generateUrl('list'));

//        return new RedirectResponse(
//            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
//        );
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function demoteAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the user with id: %s', $id));
        }

        if($object->getStatus() == 'ROLE_ADMIN')
            $object->setStatus(2);
        else if($object->getStatus() == 'ROLE_MODERATOR')
            $object->setStatus(1);
        else throw new NotFoundHttpException(sprintf('unable to demote the user with id: %s', $id));

        $this->admin->update($object);

        $this->addFlash('success','user demoted');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

}