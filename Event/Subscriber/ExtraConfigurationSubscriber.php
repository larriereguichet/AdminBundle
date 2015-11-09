<?php

namespace LAG\AdminBundle\Event\Subscriber;

use Doctrine\ORM\EntityManager;
use LAG\AdminBundle\Event\AdminEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExtraConfigurationSubscriber implements EventSubscriberInterface
{
    /**
     * @var bool
     */
    protected $enableExtraConfiguration;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public static function getSubscribedEvents()
    {
        return [
            AdminEvent::ADMIN_CREATE => 'adminCreate',
            AdminEvent::ACTION_CREATE => 'actionCreate',
        ];
    }

    public function __construct($enableExtraConfiguration = true, EntityManager $entityManager)
    {
        $this->enableExtraConfiguration = $enableExtraConfiguration;
        $this->entityManager = $entityManager;
    }

    public function adminCreate(AdminEvent $event)
    {
        if (!$this->enableExtraConfiguration) {
            return;
        }
        $configuration = $event->getConfiguration();

        // if no actions are defined, we set default CRUD action
        if (!array_key_exists('actions', $configuration) || !count($configuration['actions'])) {
            $configuration['actions'] = [
                'create' => [],
                'list' => [],
                'edit' => [],
                'delete' => []
            ];
        }
        $event->setConfiguration($configuration);
    }

    public function actionCreate(AdminEvent $event)
    {
        if (!$this->enableExtraConfiguration) {
            return;
        }
        $configuration = $event->getConfiguration();

        if (!count($configuration['fields'])) {
            $test = $this->entityManager->getMetadataFactory()->getMetadataFor($event->getAdmin()->getEntityNamespace());

            var_dump($test);
            die;
            die('lol');
        }
    }
}
