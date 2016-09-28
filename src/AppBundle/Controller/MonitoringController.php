<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * Monitorin action
 */
class MonitoringController extends Controller
{
    /**
     * Ping api
     *
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *      section="Monitoring",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\Get("/monitoring/ping")
     */
    public function getPingAction(Request $request)
    {
        return $this->view('pong', 200);
    }

}
