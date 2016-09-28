<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class DefinitionController extends Controller
{
    /**
     * Get document api configurations
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Definition",
     *      statusCode={
     *          200="Ok",
     *          404="Not found config key"
     *      }
     * )
     *
     * @Rest\QueryParam(name="key", requirements="[\w\.]+", default=NULL, description="Config key")
     */
    public function getDefinitionsAction(ParamFetcher $paramFetcher)
    {
        $configuration = $this->get('app.definition');
        $config        = [];

        if ($key = $paramFetcher->get('key')) {
            if ($configuration->has($key)) {
                $config = $configuration->get($key);
            } else {
                return $this->view(sprintf('Not found config key "%s"', $key), 404);
            }
        } else {
            $config = $configuration->all();
        }

        return $this->view($config);
    }

}
