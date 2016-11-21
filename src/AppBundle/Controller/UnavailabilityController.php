<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\UnavailabilityType;
use EPE\Component\EventEntity\Entity\Unavailability;

/**
 * Unavailability entity controller
 */
class UnavailabilityController extends Controller
{
    /**
     * Get unavailability entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.unavailability.manager');
    }

    /**
     * Create form edit
     *
     * @param Unavailability $unavailability
     *
     * @return View
     */
    protected function createEditForm(Unavailability $unavailability)
    {
        $form = $this->createRestForm(UnavailabilityType::class, $unavailability);

        if ($this->processForm($form)) {
            $this->getManager()->save($unavailability);

            return $this->forward('AppBundle:Unavailability:getUnavailability', ['id' => $unavailability->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getUnavailabilityAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$unavailability = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['unavailability.not_found'], 404);
        }

        return $this->view($unavailability);
    }

    /**
     * Get unavailability list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getCalendarsUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->get('app.calendar.manager')->find($id)) {
            return $this->view(['error' => 'calendar.not_found'], 404);
        }

        $unavailabilities = $this->getManager()->findBy(
            ['calendar' => $calendar],
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        if (empty($unavailabilities)) {
            $unavailabilities = null;
        }

        return $this->view($unavailabilities, is_null($unavailabilities) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Get unavailability list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getProfilesUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$profile = $this->get('app.profile.manager')->find($id)) {
            return $this->view(['error' => 'profile.not_found'], 404);
        }

        $unavailabilities = $this->getManager()->findBy(
            ['profile' => $profile],
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        if (empty($unavailabilities)) {
            $unavailabilities = null;
        }

        return $this->view($unavailabilities, is_null($unavailabilities) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param int          $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist unavailability entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\UnavailabilityType",
     *          "name"=""
     *      }
     * )
     */
    public function postCalendarsUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->get('app.calendar.manager')->find($id)) {
            return $this->view(['error' => 'calendar.not_found'], 404);
        }

        $entity = $this->getManager()->createEntity();
        $entity->setCalendar($calendar);

        return $this->createEditForm($entity);
    }

    /**
     * Create new unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param int          $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist unavailability entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\UnavailabilityType",
     *          "name"=""
     *      }
     * )
     */
    public function postProfilesUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$profile = $this->get('app.profile.manager')->find($id)) {
            return $this->view(['error' => 'profile.not_found'], 404);
        }

        $entity = $this->getManager()->createEntity();
        $entity->setProfile($profile);

        return $this->createEditForm($entity);
    }

    /**
     * Update unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found unavailability",
     *          422="Unprocessable entity",
     *          500="Failed to persist unavailability entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\UnavailabilityType",
     *          "name"=""
     *      }
     * )
     */
    public function putUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['unavailability.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist unavailability entity"
     *      }
     * )
     */
    public function patchUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['unavailability.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Unavailability:getUnavailability');
    }

    /**
     * Remove unavailability
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Unavailability",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove unavailability entity"
     *      }
     * )
     */
    public function deleteUnavailabilitiesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['unavailability.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
