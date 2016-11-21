<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\EventType;
use EPE\Component\EventEntity\Entity\Event;
use AppBundle\Calendar\DateTime;

/**
 * Event entity controller
 */
class EventController extends Controller
{
    /**
     * Get event entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.event.manager');
    }

    /**
     * Create form edit
     *
     * @param Event $event
     *
     * @return View
     */
    protected function createEditForm(Event $event)
    {
        $form = $this->createRestForm(EventType::class, $event);

        if ($this->processForm($form)) {
            $this->getManager()->save($event);

            return $this->forward('AppBundle:Event:getEvent', ['id' => $event->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get event
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getEventAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$event = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        return $this->view($event);
    }

    /**
     * Get event list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="start", requirements="[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}\+[\d]{4}", description="Date start event (format: ISO8601)")
     * @Rest\QueryParam(name="end", requirements="[\d]{4}-[\d]{2}-[\d]{2}T[\d]{2}:[\d]{2}:[\d]{2}\+[\d]{4}", description="Date end event (format: ISO8601)")
     * @Rest\QueryParam(name="parent", requirements="true", default=NULL, description="Exclude sub events")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     */
    public function getCalendarsEventsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->get('app.calendar.manager')->find($id)) {
            return $this->view(['error' => 'calendar.not_found'], 404);
        }

        if (!($start = $paramFetcher->get('start')) || !($end = $paramFetcher->get('end'))) {
            return $this->view('event.calendar.date_required', 400);
        }

        $criterias = [
            'calendar'  => $calendar,
            'dateStart' => [$start, $end]
        ];

        if ($parent = $paramFetcher->get('parent')) {
            $criterias['superEvent'] = null;
        }

        try {
            $start = DateTime::createFromString($start);
            $end   = DateTime::createFromString($end);
        } catch (InvalidArgumentException $e) {
            return $this->view('event.calendar.date_required', 400);
        }

        $events = $this->getManager()->findBy(
            $criterias,
            ['dateStart' => 'asc'],
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count([]);

        if (empty($events)) {
            $events = null;
        }

        return $this->view($events, is_null($events) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new event
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist event entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\EventType",
     *          "name"=""
     *      }
     * )
     */
    public function postEventsAction(ParamFetcher $paramFetcher)
    {
        $entity = $this->getManager()->createEntity();

        return $this->createEditForm($entity);
    }

    /**
     * Update event
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found event",
     *          422="Unprocessable entity",
     *          500="Failed to persist event entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\EventType",
     *          "name"=""
     *      }
     * )
     */
    public function putEventsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update event
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist event entity"
     *      }
     * )
     */
    public function patchEventsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Event:getEvent');
    }

    /**
     * Remove event
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Event",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove event entity"
     *      }
     * )
     */
    public function deleteEventsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['event.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
