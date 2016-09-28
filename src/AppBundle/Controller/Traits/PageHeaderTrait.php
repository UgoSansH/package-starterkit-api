<?php

namespace AppBundle\Controller\Traits;

use FOS\RestBundle\Request\ParamFetcher;

trait PageHeaderTrait
{
    /**
     * Create header from manage pagination collection
     *
     * @param ParamFetcher $paramFetcher
     * @param int          $length
     *
     * @return array
     */
    protected function createPageHeader(ParamFetcher $paramFetcher, int $length): array
    {
        return [
            'X-Page-Length'  => ceil($length / $paramFetcher->get('limit')),
            'X-Page-Current' => ceil($paramFetcher->get('offset') / $paramFetcher->get('limit') + 1),
            'X-Page-Items'   => $length,
            'X-Page-Offset'  => $paramFetcher->get('offset'),
            'X-Page-Limit'   => $paramFetcher->get('limit')
        ];
    }

}
