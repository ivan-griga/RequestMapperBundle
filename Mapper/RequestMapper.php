<?php

namespace Vangrg\RequestMapperBundle\Mapper;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;
use Vangrg\RequestMapperBundle\Event\BeforeNormalizeEvent;

/**
 * Class RequestMapper.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
class RequestMapper implements RequestMapperInterface
{
    /** @var Serializer */
    private $serializer;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(SerializerInterface $serializer, EventDispatcherInterface $dispatcher)
    {
        $this->serializer = $serializer;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Request $request, RequestParamMapper $configuration)
    {
        $data = $this->_extractData($request);

        $event = new BeforeNormalizeEvent($configuration, $request);
        $event->setData($data);

        if ($this->dispatcher instanceof ContractsEventDispatcherInterface) {
            $this->dispatcher->dispatch($event, BeforeNormalizeEvent::NAME);
        } else {
            $this->dispatcher->dispatch(BeforeNormalizeEvent::NAME, $event);
        }

        $data = $event->getData();

        $context = array_merge(['skip_null_values' => true], $configuration->deserializationContext);

        if ($configuration->toExistedObject) {
            $object = $request->attributes->get($configuration->param);

            $context = array_merge(['object_to_populate' => $object], $context);
        }

        try {
            $object = $this->serializer->denormalize($data, $configuration->class, null, $context);
        } catch (\Throwable $e) {
            throw new BadRequestHttpException($e->getMessage(), $e);
        }

        return $object;
    }

    /**
     * Return an array of data from request.
     *
     * @param Request $request
     *
     * @return array
     */
    protected function _extractData(Request $request)
    {
        $method = $request->getMethod();

        if ('GET' === $method) {
            $data = $request->query->all();
        } else {
            $params = $request->request->all();
            $files = $request->files->all();

            if (is_array($params) && is_array($files)) {
                $data = array_replace_recursive($params, $files);
            } else {
                $data = $params ?: $files;
            }
        }

        return $data;
    }
}