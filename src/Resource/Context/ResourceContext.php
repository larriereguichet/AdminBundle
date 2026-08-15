<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Exception\UnsupportedRequestException;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\ResourceInterface;

final class ResourceContext implements ResourceContextInterface
{
    /** @var array<array{resource: ResourceInterface|null, operation: OperationInterface|null}> */
    private array $stack = [];

    public function push(): void
    {
        $this->stack[] = ['resource' => null, 'operation' => null];
    }

    public function pop(): void
    {
        array_pop($this->stack);
    }

    public function getResource(): ResourceInterface
    {
        $current = $this->current();

        if ($current['resource'] === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource');
        }

        return $current['resource'];
    }

    public function setResource(ResourceInterface $resource): void
    {
        $this->stack[array_key_last($this->stack)]['resource'] = $resource;
    }

    public function hasResource(): bool
    {
        return $this->current()['resource'] !== null;
    }

    public function getOperation(): OperationInterface
    {
        $current = $this->current();

        if ($current['operation'] === null) {
            throw new UnsupportedRequestException('The current request is not supported by any resource or operation');
        }

        return $current['operation'];
    }

    public function setOperation(OperationInterface $operation): void
    {
        $current = $this->current();

        if ($current['operation'] !== null) {
            throw new Exception('The request operation is already set.');
        }
        $this->stack[array_key_last($this->stack)]['operation'] = $operation;
    }

    public function hasOperation(): bool
    {
        return $this->current()['operation'] !== null;
    }

    /** @return array{resource: ResourceInterface|null, operation: OperationInterface|null} */
    private function current(): array
    {
        if ($this->stack === []) {
            return ['resource' => null, 'operation' => null];
        }

        return $this->stack[array_key_last($this->stack)];
    }
}
