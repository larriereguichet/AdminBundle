<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Routing\UrlGenerator;

use LAG\AdminBundle\Metadata\Attribute\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

interface LinkUrlGeneratorInterface
{
    /**
     * Generate a url from a link object. Data can be passed to build route parameters.
     */
    public function generateUrl(Link $link, mixed $data = null, int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string;
}
