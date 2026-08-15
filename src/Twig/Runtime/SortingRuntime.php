<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Twig\Runtime;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

final readonly class SortingRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public function generateSortUrl(
        string $fieldName,
        string $sortParameter,
        string $orderParameter,
        ?string $currentSort,
        ?string $currentOrder,
    ): string {
        $request = $this->requestStack->getCurrentRequest();
        $query = $request !== null ? $request->query->all() : [];

        $nextOrder = ($currentSort === $fieldName && strtolower((string) $currentOrder) === 'asc') ? 'desc' : 'asc';
        $query[$sortParameter] = $fieldName;
        $query[$orderParameter] = $nextOrder;

        return '?'.http_build_query($query);
    }
}
