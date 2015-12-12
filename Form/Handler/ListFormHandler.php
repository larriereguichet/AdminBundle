<?php

namespace LAG\AdminBundle\Form\Handler;

use BlueBear\BaseBundle\Entity\Behaviors\Id;
use LAG\AdminBundle\Admin\Behaviors\EntityLabel;
use LAG\AdminBundle\Admin\Behaviors\EntityLabelTrait;
use Symfony\Component\Form\FormInterface;

class ListFormHandler
{
    use EntityLabelTrait;

    /**
     * Return entities ids checked by user
     *
     * @param FormInterface $form
     * @return array
     */
    public function handle(FormInterface $form)
    {
        $data = $form->getData();
        $batchItems = [];
        $cleanData = [
            'ids' => [],
            'batch_action' => $data['batch_action'],
            'labels' => $this->getLabels($data['entities'])
        ];

        // find batch items checkbox values
        foreach ($data as $name => $batchItem) {
            if (substr($name, 0, 6) == 'batch_') {
                $batchItems[$name] = $batchItem;
            }
        }
        // check if they exists in entities displayed and if checkbox is checked
        foreach ($batchItems as $name => $batchItem) {
            $batchId = (int)str_replace('batch_', '', $name);

            if (array_key_exists($batchId, $cleanData['labels']) && $batchItem === true) {
                $cleanData['ids'][] = $batchId;
            }
        }
        return $cleanData;
    }

    protected function getLabels($entities)
    {
        $labels = [];
        /** @var Id $entity */
        foreach ($entities as $entity) {
            $labels[$entity->getId()] = $this->getEntityLabel($entity);
        }
        return $labels;
    }
}
