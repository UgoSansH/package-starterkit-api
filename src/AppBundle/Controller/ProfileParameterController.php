<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\ProfileParameterType;
use EPE\Component\NotificationEntity\Entity\ProfileParameter;

/**
 * ProfileParameter entity controller
 */
class ProfileParameterController extends Controller
{
    /**
     * Get profile parameter entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.profile_parameter.manager');
    }
    /**
     * Get profile entity manager
     *
     * @return ManagerInterface
     */
    protected function getProfileManager()
    {
        return $this->get('app.profile.manager');
    }

    /**
     * Create form edit
     *
     * @param ProfileParameter $profile
     *
     * @return View
     */
    protected function createEditForm(ProfileParameter $parameter)
    {
        $form = $this->createRestForm(ProfileParameterType::class, $parameter);

        if ($this->processForm($form)) {
            $this->getManager()->save($parameter);

            return $this->forward('AppBundle:ProfileParameter:getProfileParameter', [
                'idProfile' => $parameter->getProfile()->getId(),
                'id'        => $parameter->getId()
            ]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get profile parameter
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     * @Rest\Get("/profiles/parameters/{id}")
     */
    public function getProfileParameterAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$parameter = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile_parameter.not_found'], 404);
        }

        return $this->view($parameter);
    }

    /**
     * Get profile list
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getProfileParametersAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$profile = $this->getProfileManager()->find($id)) {
            return $this->createErrorResponse(['profile.not_found'], 404);
        }

        $criteria = [
            'profile' => $profile
        ];

        $parameters = $this->getManager()->findBy(
            $criteria,
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count($criteria);

        if (empty($parameters)) {
            $parameters = null;
        }

        return $this->view($parameters, is_null($parameters) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new profile
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\ProfileParameterType",
     *          "name"=""
     *      }
     * )
     *
     * @Rest\Post("/profiles/parameters")
     */
    public function postProfileParametersAction(ParamFetcher $paramFetcher)
    {

        $entity = $this->getManager()->createEntity();

        return $this->createEditForm($entity);
    }

    /**
     * Update profile
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found profile",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\ProfileParameterType",
     *          "name"=""
     *      }
     * )
     *
     * @Rest\Put("/profiles/parameters/{id}")
     */
    public function putProfileParametersAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$parameter = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile_parameter.not_found'], 404);
        }

        return $this->createEditForm($parameter);
    }

    /**
     * Partial update profile
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      }
     * )
     *
     * @Rest\Patch("/profiles/parameters/{id}")
     */
    public function patchProfileParametersAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$parameter = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile_parameter.not_found'], 404);
        }

        return $this->partialUpdate($parameter, $this->getManager(), 'AppBundle:ProfileParameter:getProfileParameter');
    }

    /**
     * Remove profile
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="ProfileParameter",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove profile entity"
     *      }
     * )
     *
     * @Rest\Delete("/profiles/parameters/{id}")
     */
    public function deleteProfileParametersAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$parameter = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile_parameter.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
