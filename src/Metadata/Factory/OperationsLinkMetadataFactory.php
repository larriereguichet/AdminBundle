<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata\Factory;

use LAG\AdminBundle\Metadata\Attribute\Link;
use LAG\AdminBundle\Metadata\CollectionOperationMetadataInterface;
use LAG\AdminBundle\Metadata\ResourceMetadataInterface;

use function Symfony\Component\String\u;

final readonly class OperationsLinkMetadataFactory implements ResourceMetadataFactoryInterface
{
    public function __construct(
        private ResourceMetadataFactoryInterface $metadataFactory,
    ) {
    }

    public function createMetadata(string $resourceName): ResourceMetadataInterface
    {
        $resource = $this->metadataFactory->createMetadata($resourceName);

        $operations = $resource->getOperations();

        foreach ($resource->getOperations() as $operation) {
            $contextualLinks = [];
            $itemLinks = [];

            if ($operation instanceof CollectionOperationMetadataInterface) {
                if ($operation->getContextualLinks() === null
                    && $resource->hasOperation('create')) {
                    $contextualOperation = $resource->getOperation('create');
                    $contextualLinks[] = new Link(
                        name: 'create',
                        operation: $contextualOperation->getName(),
                        text: 'lag_admin.ui.create',
                        icon: 'bi:circle-plus',
                    );
                }

                if ($operation->getItemLinks() === null) {
                    if ($resource->hasOperation('update')) {
                        $itemOperation = $resource->getOperation('update');
                        $itemLinks[] = new Link(
                            name: 'update',
                            operation: $itemOperation->getName(),
                            text: 'lag_admin.ui.update',
                            icon: 'bi:pencil',
                        );
                    }

                    if ($resource->hasOperation('delete')) {
                        $itemOperation = $resource->getOperation('delete');
                        $itemLinks[] = new Link(
                            name: 'delete',
                            operation: $itemOperation->getName(),
                            text: 'lag_admin.ui.delete',
                            icon: 'bi:trash',
                        );
                    }
                }
            }
            $contextualLinks = $this->initializeLinks(
                $resource,
                $operation->getContextualLinks() ?? $contextualLinks,
            );
            $itemLinks = $this->initializeLinks(
                $resource,
                $operation->getItemLinks() ?? $itemLinks,
            );

            $operations[$operation->getShortName()] = $operation
                ->withContextualLinks($contextualLinks)
                ->withItemLinks($itemLinks)
            ;
        }

        return $resource->withOperations($operations);
    }

    /**
     * @param array<Link|string> $links
     *
     * @return array<Link>
     */
    private function initializeLinks(ResourceMetadataInterface $resource, array $links): array
    {
        foreach ($links as $index => $link) {
            if (is_string($link)) {
                $link = new Link(operation: $link);
            }
            $linkName = $link->getName();
            $linkOperation = $link->getOperation();

            if ($linkOperation !== null) {
                if (!u($linkOperation)->containsAny('.')) {
                    $linkOperation = $resource->getApplication().'.'.$resource->getShortName().'.'.$linkOperation;
                }

                if ($linkName === null) {
                    $linkName = (string) u($linkOperation)->afterLast('.');
                }
            }

            $links[$index] = $link
                ->withName($linkName)
                ->withOperation($linkOperation)
                ->withPropertyPath($link->getPropertyPath() ?? $linkName)
                ->withLabel($link->getLabel() ?? $linkName)
            ;
        }

        return $links;
    }
}
