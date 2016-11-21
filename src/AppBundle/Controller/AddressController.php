<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ugosansh\Component\EntityManager\ManagerInterface;
use EPE\Component\EventEntity\Entity\Address;
use AppBundle\Form\Type\AddressType;

/**
 * Address entity controller
 */
class AddressController extends Controller
{
    /**
     * Get address manager
     *
     * @return ManagerInterface
     */
    protected function getManager(): ManagerInterface
    {
        return $this->get('app.address.manager');
    }

    /**
     * Create form edit
     *
     * @param Address $address
     *
     * @return View
     */
    protected function createEditForm(Address $address)
    {
        $isNew = $address->getId() ? false : true;
        $form  = $this->createRestForm(AddressType::class, $address);

        if ($this->processForm($form)) {
            $this->getManager()->save($address);

            if ($isNew) {
                //$this->get('app.')
            }

            return $this->forward('AppBundle:Address:getAddress', ['id' => $address->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get address
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getAddressAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$address = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['address.not_found'], 404);
        }

        return $this->view($address);
    }

    /**
     * Get address list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|name),(desc|asc)", default="id,desc", description="Order (id|name),(desc|asc) (ex : id,desc)")
     */
    public function getAddressesAction(ParamFetcher $paramFetcher)
    {
        $addresss = $this->getManager()->findBy(
            [],
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        return $this->view($addresss, 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Get address list from person id
     *
     * @param ParamFetcher $paramFetcher Param fetcher
     * @param int          $id           Person id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     */
    public function getEventsAddressesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$event = $this->get('app.event.manager')->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        return $this->view($event->getLocation(), 200);
    }

    /**
     * Create new address
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist address entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\AddressType",
     *          "name"=""
     *      }
     * )
     */
    public function postEventsAddressAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$event = $this->get('app.event.manager')->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        $entity = $this->getManager()->createEntity();
        $entity->setEvent($event);

        return $this->createEditForm($entity);
    }

    /**
     * Update address
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found address",
     *          422="Unprocessable entity",
     *          500="Failed to persist address entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\AddressType",
     *          "name"=""
     *      }
     * )
     */
    public function putAddressAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['address.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update address
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist address entity"
     *      }
     * )
     */
    public function patchAddressAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['address.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Address:getAddress');
    }

    /**
     * Remove address
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Address",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove address entity"
     *      }
     * )
     */
    public function deleteAddressAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['address.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
