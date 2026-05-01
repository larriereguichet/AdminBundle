<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Metadata;

interface ApplicationInterface
{
    public function getName(): string;

    public function getDateFormat(): string;

    public function getTimeFormat(): string;

    public function getTranslationDomain(): string;

    public function getTranslationPattern(): string;

    public function getRoutePattern(): string;

    public function getBaseTemplate(): string;

    /** @return string[] */
    public function getPermissions(): array;
}
