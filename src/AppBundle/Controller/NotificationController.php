<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\NotificationType;
use EPE\Component\NotificationEntity\Entity\Notification;

/**
 * Notification entity controller
 */
class NotificationController extends Controller
{
    /**
     * Get notification entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.notification.manager');
    }

    /**
     * Create form edit
     *
     * @param Notification $notification
     *
     * @return View
     */
    protected function createEditForm(Notification $notification)
    {
        $form = $this->createRestForm(NotificationType::class, $notification);

        if ($this->processForm($form)) {
            $this->getManager()->save($notification);

            return $this->forward('AppBundle:Notification:getNotification', ['id' => $notification->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get notification
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getNotificationAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$notification = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['notification.not_found'], 404);
        }

        return $this->view($notification);
    }

    /**
     * Get notification list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getNotificationsAction(ParamFetcher $paramFetcher)
    {
        $notifications = $this->getManager()->findBy(
            [],
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        if (empty($notifications)) {
            $notifications = null;
        }

        return $this->view($notifications, is_null($notifications) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new notification
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist notification entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\NotificationType",
     *          "name"=""
     *      }
     * )
     */
    public function postNotificationsAction(ParamFetcher $paramFetcher)
    {
        $entity = $this->getManager()->createEntity();

        return $this->createEditForm($entity);
    }

    /**
     * Update notification
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found notification",
     *          422="Unprocessable entity",
     *          500="Failed to persist notification entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\NotificationType",
     *          "name"=""
     *      }
     * )
     */
    public function putNotificationsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['notification.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update notification
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist notification entity"
     *      }
     * )
     */
    public function patchNotificationsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['notification.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Notification:getNotification');
    }

    /**
     * Remove notification
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Notification",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove notification entity"
     *      }
     * )
     */
    public function deleteNotificationsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['notification.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
