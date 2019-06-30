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
     * Map all the properties from $request to $toClass. Return a new object.
     *
     * @param Request $request
     * @param string $toClass
     *
     * @return mixed
     */
    public function map(Request $request, RequestParamMapper $configuration);
}