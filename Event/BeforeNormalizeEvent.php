<?php

namespace Vangrg\RequestMapperBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;

/**
 * Class BeforeNormalizeEvent.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
final class BeforeNormalizeEvent extends Event
{
    const NAME = 'vangrg_request_mapper.before_normalize';

    /** @var RequestParamMapper */
    protected $configuration;

    /** @var Request */
    protected $request;

    /** @var array */
    private $data;

    public function __construct(RequestParamMapper $configuration, Request $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return BeforeNormalizeEvent
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return RequestParamMapper
     */
    public function getConfiguration(): RequestParamMapper
    {
        return $this->configuration;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }
}