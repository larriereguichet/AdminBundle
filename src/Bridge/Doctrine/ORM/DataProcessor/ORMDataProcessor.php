<?php

namespace LAG\AdminBundle\Bridge\Doctrine\ORM\DataProcessor;

use Doctrine\Bundle\DoctrineBundle\Registry;
use LAG\AdminBundle\Action\ActionInterface;
use LAG\AdminBundle\Action\Create;
use LAG\AdminBundle\Action\Delete;
use LAG\AdminBundle\Controller\Update;
use LAG\AdminBundle\DataProcessor\DataProcessorInterface;

class ORMDataProcessor implements DataProcessorInterface
{
    public function __construct(
        private Registry $registry,
    ) {
    }

    public function process(mixed $data, ActionInterface $action): void
    {
        $manager = $this->registry->getManagerForClass($action->getAdmin()->getEntityClass());

        if ($action instanceof Create || $action instanceof Update) {
            $manager->persist($data);
            $manager->flush();
        }

        if ($action instanceof Delete) {
            $manager->remove($data);
            $manager->flush();
        }
    }
}
