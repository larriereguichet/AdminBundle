<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\Behaviors\EntityLabel;
use LAG\AdminBundle\Form\Type\BatchActionType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;

class BatchFormHandler
{
    use EntityLabel;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $batchRoute;

    public function __construct(FormFactory $formBuilder, $batchRoute)
    {
        $this->formFactory = $formBuilder;
        $this->batchRoute = $batchRoute;
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
        $bacthItems = [];
        $batchEntities = [];

        // find batch items checkbox values
        foreach ($data as $name => $batchItem) {
            if (substr($name, 0, 6) == 'batch_') {
                $bacthItems[$name] = $batchItem;
            }
        }
        // check if they exists in entities displayed and if checkbox is checked
        foreach ($bacthItems as $name => $bacthItem) {
            $batchId = (int)str_replace('batch_', '', $name);

            if (array_key_exists($batchId, $entities) && $bacthItem === true) {
                $batchEntities[$batchId] = $this->getEntityLabel($entities[$batchId]);
            }
        }
        $form = $this
            ->formFactory
            ->create(new BatchActionType(), [
                'entities_ids' => array_keys($batchEntities),
                'entities_labels' => $batchEntities,
                'batch_action' => $data['batch']
            ], [
                'action' => $this->batchRoute
            ]);

        return $form;
    }
}
