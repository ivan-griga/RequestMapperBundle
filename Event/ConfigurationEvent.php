<?php

namespace Vangrg\RequestMapperBundle\Event;

use Symfony\Component\HttpFoundation\Request;
use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;

/**
 * Class ConfigurationEvent.
 *
 * @author Ivan Griga <grigaivan2@gmail.com>
 */
final class ConfigurationEvent extends Event
{
    const NAME = 'vangrg_request_mapper.configuration';

    /**
     * @var RequestParamMapper
     */
    private $configuration;

    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestParamMapper $configuration, Request $request)
    {
        $this->configuration = $configuration;
        $this->request = $request;
    }

    /**
     * @param RequestParamMapper $configuration
     *
     * @return ConfigurationEvent
     */
    public function setConfiguration(RequestParamMapper $configuration): self
    {
        $this->configuration = $configuration;

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