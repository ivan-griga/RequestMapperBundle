<?php

namespace Vangrg\RequestMapperBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationException.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
class ValidationException extends BadRequestHttpException
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $_errors;

    public function __construct($message = null, ConstraintViolationListInterface $errors = null, \Exception $previous = null, $code = 0)
    {
        $this->_errors = $errors;

        parent::__construct($message, $previous, $code);
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->_errors;
    }
}