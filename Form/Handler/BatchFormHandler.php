<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\Behaviors\EntityLabelTrait;
use LAG\AdminBundle\Form\Type\BatchActionType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class BatchFormHandler
{
    use EntityLabelTrait;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * BatchFormHandler constructor.
     *
     * @param FormFactory $formBuilder
     */
    public function __construct(FormFactory $formBuilder)
    {
        $this->formFactory = $formBuilder;
    }

    /**
     * Return entities checked by user
     *
     * @param FormInterface $form
     * @return FormInterface
     */
    public function handle(FormInterface $form)
    {
        $data = $form->getData();
        // TODO sort entities by id ?
        $entities = $data['entities'];
        $batchItems = [];
        $batchEntities = [];

        // find batch items checkbox values
        foreach ($data as $name => $batchItem) {
            if (substr($name, 0, 6) == 'batch_') {
                $batchItems[$name] = $batchItem;
            }
        }
        // check if they exists in entities displayed and if checkbox is checked
        foreach ($batchItems as $name => $bacthItem) {
            $batchId = (int)str_replace('batch_', '', $name);

            if (array_key_exists($batchId, $entities) && $bacthItem === true) {
                $batchEntities[$batchId] = $this->getEntityLabel($entities[$batchId]);
            }
        }
        // TODO get action from configuration
        $action = 'lag.admin.batch.delete';

        $form = $this
            ->formFactory
            ->create(BatchActionType::class, [
                'entities_ids' => array_keys($batchEntities),
                'entities_labels' => $batchEntities,
                'batch_action' => $data['batch']
            ], [
                'action' => $action
            ]);

        return $form;
    }
}
