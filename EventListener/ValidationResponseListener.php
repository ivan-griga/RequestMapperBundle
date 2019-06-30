<?php

namespace Vangrg\RequestMapperBundle\EventListener;

use Vangrg\RequestMapperBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationResponseListener.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
class ValidationResponseListener
{
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof ValidationException) {
            $result = $this->createErrorValidationResponse($exception->getErrors(), $exception->getMessage());

            $event->setResponse($this->getResponse($result, Response::HTTP_BAD_REQUEST));
        }
    }

    /**
     * @param ConstraintViolationListInterface $violationList
     * @param string                           $message
     * @param string|null                      $property
     *
     * @return array
     */
    private function createErrorValidationResponse(ConstraintViolationListInterface $violationList, string $message, ?string $property = null): array
    {
        $errors = [];
        foreach ($violationList as $violation) {
            $path = $property ?? $violation->getPropertyPath();

            if (array_key_exists($path, $errors)) {
                array_push($errors[$path], $violation->getMessage());
            } else {
                $errors[$path] = [$violation->getMessage()];
            }
        }

        $result = [
            'code' => Response::HTTP_BAD_REQUEST,
            'message' => $message,
            'errors' => $errors,
        ];

        return $result;
    }

    /**
     * Transforms array to response.
     *
     * @param array $response
     * @param int   $statusCode
     *
     * @return JsonResponse
     */
    private function getResponse(array $response, int $statusCode)
    {
        return JsonResponse::fromJsonString($this->serializer->serialize($response, 'json'), $statusCode);
    }
}