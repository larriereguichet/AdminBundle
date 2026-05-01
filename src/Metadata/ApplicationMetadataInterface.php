<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface ApplicationMetadataInterface extends ApplicationInterface
{
    public function withName(string $name): self;

    public function withDateFormat(string $dateFormat): self;

    public function withTimeFormat(string $timeFormat): self;

    public function withTranslationDomain(string $translationDomain): self;

    public function withTranslationPattern(string $translationPattern): self;

    public function withRoutePattern(string $routePattern): self;

    public function withBaseTemplate(string $baseTemplate): self;

    /** @param string[] $permissions */
    public function withPermissions(array $permissions): self;
}
