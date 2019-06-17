<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostAdminController extends CRUDController
{

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function verifyAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the post with id: %s', $id));
        }

        $object->setVerifiedAdminId($this->get('security.token_storage')->getToken()->getUser()->getId());

        $this->admin->update($object);

        $this->addFlash('success', 'successfully verified');


        return new RedirectResponse(
            $this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()])
        );
    }
}
