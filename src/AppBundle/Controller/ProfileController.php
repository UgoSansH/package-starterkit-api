<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\ProfileType;
use EPE\Component\NotificationEntity\Entity\Profile;

/**
 * Profile entity controller
 */
class ProfileController extends Controller
{
    /**
     * Get profile entity manager
     *
     * @return ManagerInterface
     */
    protected function getManager()
    {
        return $this->get('app.profile.manager');
    }

    /**
     * Create form edit
     *
     * @param Profile $profile
     *
     * @return View
     */
    protected function createEditForm(Profile $profile)
    {
        $form = $this->createRestForm(ProfileType::class, $profile);

        if ($this->processForm($form)) {
            $this->getManager()->save($profile);

            return $this->forward('AppBundle:Profile:getProfile', ['id' => $profile->getId()]);
        }

        return $this->createFormErrorResponse($form);
    }

    /**
     * Get profile
     *
     * @param ParamFetcher $paramFetcher
     * @param integer      $id
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Profile",
     *      statusCodes={
     *          200="Ok",
     *          404="Tag not found"
     *      }
     * )
     *
     */
    public function getProfileAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$profile = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile.not_found'], 404);
        }

        return $this->view($profile);
    }

    /**
     * Get profile list
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Profile",
     *      statusCodes={
     *          200="Ok"
     *      }
     * )
     *
     * @Rest\QueryParam(name="external_id", requirements="\d+", default=NULL, description="Limit")
     * @Rest\QueryParam(name="limit", requirements="\d+", default=20, description="Limit")
     * @Rest\QueryParam(name="offset", requirements="\d+", default="0", description="Offset")
     * @Rest\QueryParam(name="order", requirements="(id|type),(desc|asc)", default="id,desc", description="Order (id|type),(desc|asc) (ex : id,desc)")
     */
    public function getProfilesAction(ParamFetcher $paramFetcher)
    {
        $criteria = [];

        if ($externalId = $paramFetcher->get('external_id')) {
            $criteria['externalId'] = $externalId;
        }

        $profiles = $this->getManager()->findBy(
            $criteria,
            $this->filterOrderQueryParam($paramFetcher),
            $paramFetcher->get('limit'),
            $paramFetcher->get('offset')
        );

        $length = $this->getManager()->count($criteria);

        if (empty($profiles)) {
            $profiles = null;
        }

        return $this->view($profiles, is_null($profiles) ? 201 : 200, $this->createPageHeader($paramFetcher, $length));
    }

    /**
     * Create new profile
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return View
     *
     * @ApiDoc(
     *      section="Profile",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\ProfileType",
     *          "name"=""
     *      }
     * )
     */
    public function postProfilesAction(ParamFetcher $paramFetcher)
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
     *      section="Profile",
     *      statusCodes={
     *          200="Ok",
     *          404="Not found profile",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      },
     *      input={
     *          "class"="AppBundle\Form\Type\ProfileType",
     *          "name"=""
     *      }
     * )
     */
    public function putProfilesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile.not_found'], 404);
        }

        return $this->createEditForm($entity);
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
     *      section="Profile",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to persist profile entity"
     *      }
     * )
     */
    public function patchProfilesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile.not_found'], 404);
        }

        return $this->partialUpdate($entity, $this->getManager(), 'AppBundle:Profile:getProfile');
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
     *      section="Profile",
     *      statusCodes={
     *          200="Ok",
     *          422="Unprocessable entity",
     *          500="Failed to remove profile entity"
     *      }
     * )
     */
    public function deleteProfilesAction(ParamFetcher $paramFetcher, $id)
    {
        if (!$entity = $this->getManager()->find($id)) {
            return $this->createErrorResponse(['profile.not_found'], 404);
        }

        if ($this->getManager()->remove($entity)) {
            return $this->view(null, 201);
        }

        return $this->createErrorResponse(['server.error'], 500);
    }

}
