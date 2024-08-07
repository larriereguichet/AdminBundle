<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\State\Processor;

use Doctrine\Bundle\DoctrineBundle\Registry;
use LAG\AdminBundle\Bridge\Doctrine\ORM\Exception\ManagerNotFoundException;
use LAG\AdminBundle\Resource\Metadata\Create;
use LAG\AdminBundle\Resource\Metadata\Delete;
use LAG\AdminBundle\Resource\Metadata\OperationInterface;
use LAG\AdminBundle\Resource\Metadata\Update;
use LAG\AdminBundle\State\Processor\ProcessorInterface;

final readonly class ORMProcessor implements ProcessorInterface
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

        if ($manager === null) {
            throw new ManagerNotFoundException($operation);
        }

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
