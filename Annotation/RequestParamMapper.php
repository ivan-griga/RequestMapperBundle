<?php

namespace Vangrg\RequestMapperBundle\Annotation;

/**
 * Class RequestParamMapper.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 *
 * @Annotation
 */
final class RequestParamMapper
{
    /** @var string */
    public $param;

    /** @var string */
    public $class;

    /** @var array */
    public $deserializationContext = [];

    /** @var bool */
    public $toExistedObject = false;

    /** @var bool */
    public $validate = true;

    /** @var array */
    public $validationGroups = [];
}