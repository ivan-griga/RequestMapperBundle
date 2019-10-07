<?php

namespace Vangrg\RequestMapperBundle\Mapper;

use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestMapperInterface.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
interface RequestMapperInterface
{
    /**
     * Map all request properties using RequestParamMapper config.
     * Return an object.
     *
     * @param Request $request
     * @param RequestParamMapper $toClass
     *
     * @return mixed
     */
    public function map(Request $request, RequestParamMapper $configuration);
}