<?php

namespace Vangrg\RequestMapperBundle\EventListener;

use Vangrg\RequestMapperBundle\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
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

    /** @var string */
    private $responseFormat;

    public function __construct(SerializerInterface $serializer, string $responseFormat)
    {
        $this->serializer = $serializer;
        $this->responseFormat = $responseFormat;
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
     * @return Response
     */
    private function getResponse(array $response, int $statusCode)
    {
        $content = $this->serializer->serialize($response, $this->responseFormat);

        $response = new Response($content, $statusCode);

        $mimeTypes = Request::getMimeTypes($this->responseFormat);

        if (count($mimeTypes) > 0) {
            $response->headers->set('Content-Type', $mimeTypes[0]);
        }

        return $response;
    }
}