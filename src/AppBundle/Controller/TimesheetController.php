<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\TimesheetType;
use EPE\Component\EventEntity\Entity\Timesheet;


/**
 * Timesheet entity controller
 */
class TimesheetController extends Controller
{
    /**
     * Get timesheet entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.timesheet.manager');
    }

    /**
     * Create form edit
     *
     * @param Timesheet $timesheet
     *
     * @return View
     */
    protected function createEditForm(Timesheet $timesheet)
    {
        $isNew = $timesheet->getId() ? false : true;
        $form  = $this->createRestForm(TimesheetType::class, $timesheet);

        if ($this->processForm($form)) {
            if ($isNew) {
                $entity = $this->getManager()->findOneBy([
                    'calendar' => $timesheet->getCalendar(),
                    'day'      => $timesheet->getDay()
                ]);

                if ($entity) {
                    $entity->setTimeStart($timesheet->getTimeStart());
                    $entity->setTimeEnd($timesheet->getTimeEnd());

                    $timesheet = $entity;
                }
            }

            $this->getManager()->save($timesheet);

            return $this->forward('AppBundle:Timesheet:getTimesheet', ['id' => $timesheet->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get timesheet
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getTimesheetAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$timesheet = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['timesheet.not_found'], 404);
        }

        return $this->view($timesheet);
    }

    /**
     * Get timesheet list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     */
    public function getCalendarsTimesheetsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->get('app.calendar.manager')->find($id)) {
            return $this->view(['error' => 'calendar.not_found'], 404);
        }

        $timesheets = $this->getManager()->findBy(['calendar' => $calendar]);
        $length     = $this->getManager()->count([]);

        if (empty($timesheets)) {
            $timesheets = null;
        }

        return $this->view($timesheets, is_null($timesheets) ? 201 : 200);
    }

    /**
     * Create new timesheet
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist timesheet entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\TimesheetType",
     *          "name"=""
     *      }
     * )
     */
    public function postCalendarsTimesheetsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$calendar = $this->get('app.calendar.manager')->find($id)) {
            return $this->view(['error' => 'calendar.not_found'], 404);
        }

        $entity = $this->getManager()->createEntity();
        $entity->setCalendar($calendar);

        return $this->createEditForm($entity);
    }

    /**
     * Update timesheet
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found timesheet",
     *          422="Unprocessable entity",
     *          500="Failed to persist timesheet entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\TimesheetType",
     *          "name"=""
     *      }
     * )
     */
    public function putTimesheetsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['timesheet.not_found'], 404);
        }

        return $this->createEditForm($entity);
    }

    /**
     * Partial update timesheet
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist timesheet entity"
     *      }
     * )
     */
    public function patchTimesheetsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['timesheet.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Timesheet:getTimesheet');
    }

    /**
     * Remove timesheet
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Timesheet",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove timesheet entity"
     *      }
     * )
     */
    public function deleteTimesheetsAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['timesheet.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
