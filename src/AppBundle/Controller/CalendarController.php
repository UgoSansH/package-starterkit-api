<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\CalendarType;
use EPE\Component\EventEntity\Entity\Calendar;

/**
 * Calendar entity controller
 */
class CalendarController extends Controller
{
    /**
     * Get calendar entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.calendar.manager');
    }

    /**
     * Create form edit
     *
     * @param Calendar $calendar
     *
     * @return View
     */
    protected function createEditForm(Calendar $calendar)
    {
        $form = $this->createRestForm(CalendarType::class, $calendar);

        if ($this->processForm($form)) {
            $this->getManager()->save($calendar);

            return $this->forward('AppBundle:Calendar:getCalendar', ['id' => $calendar->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get calendar
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getCalendarAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['calendar.not_found'], 404);
        }

        return $this->view($calendar);
    }

    /**
     * Get calendar list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="funder", requirements="\d+", default=NULL, description="Filter by Profile Id")
     * @Rest\QueryParam(name="external_id", requirements="\d+", default=NULL, description="Filter by external Id")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getCalendarsAction(ParamFetcher $paramFetcher)
    {
        $criteria = [];

        if ($funder = $paramFetcher->get('funder')) {
            if (!$profile = $this->get('app.profile.manager')->find($funder)) {
                return $this->view(['error' => 'profile.not_found'], 404);
            }

            $criteria['funder'] = $profile;
        }

        if ($externalId = $paramFetcher->get('external_id')) {
            $criteria['externalId'] = $externalId;
        }

        $calendars = $this->getManager()->findBy(
            $criteria,
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count($criteria);

        if (empty($calendars)) {
            $calendars = null;
        }

        return $this->view($calendars, is_null($calendars) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Get calendar list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="external_id", requirements="\d+", default=NULL, description="Filter by external Id")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getProfilesCalendarsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$profile = $this->get('app.profile.manager')->find($id)) {
            return $this->view(['error' => 'profile.not_found'], 404);
        }

        $criteria = ['funder' => $profile];

        if ($externalId = $paramFetcher->get('external_id')) {
            $criteria['externalId'] = $externalId;
        }

        $calendars = $this->getManager()->findBy(
            $criteria,
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count($criteria);

        if (empty($calendars)) {
            $calendars = null;
        }

        return $this->view($calendars, is_null($calendars) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new calendar
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist calendar entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\CalendarType",
     *          "name"=""
     *      }
     * )
     */
    public function postCalendarsAction(ParamFetcher $paramFetcher)
    {
        $entity = $this->getManager()->createEntity();

        return $this->createEditForm($entity);
    }

    /**
     * Update calendar
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found calendar",
     *          422="Unprocessable entity",
     *          500="Failed to persist calendar entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\CalendarType",
     *          "name"=""
     *      }
     * )
     */
    public function putCalendarsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['calendar.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update calendar
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist calendar entity"
     *      }
     * )
     */
    public function patchCalendarsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['calendar.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Calendar:getCalendar');
    }

    /**
     * Remove calendar
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Calendar",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove calendar entity"
     *      }
     * )
     */
    public function deleteCalendarsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['calendar.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
