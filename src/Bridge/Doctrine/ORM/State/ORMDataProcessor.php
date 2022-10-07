<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State;

use Doctrine\Bundle\DoctrineBundle\Registry;
use LAG\AdminBundle\Metadata\Create;
use LAG\AdminBundle\Metadata\Delete;
use LAG\AdminBundle\Metadata\OperationInterface;
use LAG\AdminBundle\Metadata\Update;
use LAG\AdminBundle\State\DataProcessorInterface;

class ORMDataProcessor implements DataProcessorInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function process(
        mixed $data,
        OperationInterface $operation,
        array $uriVariables = [],
        array $context = []
    ): void {
        $manager = $this->registry->getManagerForClass($operation->getResource()->getDataClass());

        if ($operation instanceof Create || $operation instanceof Update) {
            $manager->persist($data);
            $manager->flush();
        }

        if ($operation instanceof Delete) {
            $manager->remove($data);
            $manager->flush();
        }
    }
}
