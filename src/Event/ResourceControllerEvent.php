<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Event;

use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Resource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

class ResourceControllerEvent extends Event implements ResourceEventInterface
{
    private ?Response $response = null;

    public function __construct(
        private readonly OperationInterface $operation,
        private readonly Request $request,
        private readonly mixed $data,
    ) {
    }

    public function getResource(): Resource
    {
        return $this->operation->getResource();
    }

    public function getOperation(): OperationInterface
    {
        return $this->operation;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }
}
