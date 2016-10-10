<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\NotificationTypeType;
use EPE\Component\NotificationEntity\Entity\NotificationType;

/**
 * Type entity controller
 */
class TypeController extends Controller
{
    /**
     * Get type type entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.notification_type.manager');
    }

    /**
     * Create form edit
     *
     * @param Type $type
     *
     * @return View
     */
    protected function createEditForm(NotificationType $type)
    {
        $form = $this->createRestForm(NotificationTypeType::class, $type);

        if ($this->processForm($form)) {
            $this->getManager()->save($type);

            return $this->forward('AppBundle:Type:getType', ['id' => $type->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get type
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getTypeAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$type = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['type.not_found'], 404);
        }

        return $this->view($type);
    }

    /**
     * Get type list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|enabled),(desc|asc)", default="id,desc", description="Order (id|enabled),(desc|asc) (ex : id,desc)")
     */
    public function getTypesAction(ParamFetcher $paramFetcher)
    {
        $types = $this->getManager()->findBy(
            [],
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        if (empty($types)) {
            $types = null;
        }

        return $this->view($types, is_null($types) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new type
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist type entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\NotificationTypeType",
     *          "name"=""
     *      }
     * )
     */
    public function postTypesAction(ParamFetcher $paramFetcher)
    {
        $entity = $this->getManager()->createEntity();

        return $this->createEditForm($entity);
    }

    /**
     * Update type
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found type",
     *          422="Unprocessable entity",
     *          500="Failed to persist type entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\NotificationTypeType",
     *          "name"=""
     *      }
     * )
     */
    public function putTypesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['type.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update type
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist type entity"
     *      }
     * )
     */
    public function patchTypesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['type.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Type:getType');
    }

    /**
     * Remove type
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Type",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove type entity"
     *      }
     * )
     */
    public function deleteTypesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['type.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
