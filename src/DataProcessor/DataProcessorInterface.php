<?php

namespace LAG\AdminBundle\DataProcessor;

use LAG\AdminBundle\Action\ActionInterface;

interface DataProcessorInterface
{
    public function process(mixed $data, ActionInterface $action): void;
}
