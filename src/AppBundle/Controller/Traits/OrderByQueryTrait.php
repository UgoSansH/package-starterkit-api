<?php

namespace AppBundle\Controller\Traits;

use FOS\RestBundle\Request\ParamFetcher;

trait OrderByQueryTrait
{
    /**
     * Filter orderBy QueryParam
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return mixed (array|null)
     */
    protected function filterOrderQueryParam(ParamFetcher $paramFetcher)
    {
        $orderBy = null;

        if ($order = $paramFetcher->get('order')) {
            $order         = explode(',', $order);
            $orderBy[$order[0]] = in_array(strtolower($order[1]), ['asc', 'desc']) ? $order[1] : 'desc';
        }

        return $orderBy;
    }

}
