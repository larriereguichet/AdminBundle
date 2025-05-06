<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Resource\Context;

use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Metadata\Application;
use LAG\AdminBundle\Request\Extractor\ParametersExtractorInterface;
use LAG\AdminBundle\Resource\Factory\ApplicationFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class ApplicationContext implements ApplicationContextInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private ParametersExtractorInterface $parametersExtractor,
        private ApplicationFactoryInterface $applicationFactory,
    ) {
    }

    public function getApplication(): Application
    {
        $request = $this->requestStack->getCurrentRequest();
        $applicationName = $this->parametersExtractor->getApplicationName($request);

        if ($applicationName === null) {
            throw new Exception('The current request is not supported by any application');
        }

        return $this->applicationFactory->create($applicationName);
    }

    public function hasApplication(): bool
    {
        $request = $this->requestStack->getCurrentRequest();

        return $this->parametersExtractor->getApplicationName($request) !== null;
    }
}
