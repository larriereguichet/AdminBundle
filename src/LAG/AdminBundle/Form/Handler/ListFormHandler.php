<?php

namespace LAG\AdminBundle\Form\Handler;

use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Form\Type\MassActionType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Environment;

class ListFormHandler
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var Twig_Environment
     */
    private $twig;
    
    public function __construct(FormFactoryInterface $formFactory, Twig_Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }
    
    public function handle(FormInterface $form, AdminInterface $admin)
    {
        if (!$form->isValid()) {
            return;
        }
        $data = $form->getData();
    
        if (!array_key_exists('id', $data)) {
            return;
        }
        $entityClass = $admin
            ->getConfiguration()
            ->getParameter('entity')
        ;
        $entityProperty = $form
            ->getConfig()
            ->getOption('entity_property')
        ;
        $entityValues = [];
        $accessor = PropertyAccess::createPropertyAccessor();
    
        foreach ($data as $entity) {
            $entityValues[] = $accessor->getValue($entity, $entityProperty);
        }
        
        $massDeleteForm = $this
            ->formFactory
            ->create(MassActionType::class, [
                'entity_class' => $entityClass,
                'entity_property' => $entityProperty,
                'entity_values' => $entityValues,
            ])
        ;
    
        return $this
            ->twig
            ->render('@LAGAdmin/List/delete.html.twig', [
                'form' => $massDeleteForm->createView(),
        
            ])
        ;
        
        
        var_dump($data);
        die;
    
    
    }
    
    
    
//    use EntityLabelTrait;
//
//    /**
//     * Return entities ids checked by user
//     *
//     * @param FormInterface $form
//     * @return array
//     */
//    public function handle(FormInterface $form)
//    {
//        $data = $form->getData();
//        $batchItems = [];
//        $cleanData = [
//            'ids' => [],
//            'batch_action' => $data['batch_action'],
//            'labels' => $this->getLabels($data['entities'])
//        ];
//
//        // find batch items checkbox values
//        foreach ($data as $name => $batchItem) {
//            if (substr($name, 0, 6) == 'batch_') {
//                $batchItems[$name] = $batchItem;
//            }
//        }
//        // check if they exists in entities displayed and if checkbox is checked
//        foreach ($batchItems as $name => $batchItem) {
//            $batchId = (int) str_replace('batch_', '', $name);
//
//            if (array_key_exists($batchId, $cleanData['labels']) && $batchItem === true) {
//                $cleanData['ids'][] = $batchId;
//            }
//        }
//        return $cleanData;
//    }
//
//    /**
//     * @param $entities
//     * @return array
//     */
//    protected function getLabels($entities)
//    {
//        $labels = [];
//
//        foreach ($entities as $entity) {
//            $labels[$entity->getId()] = $this->getEntityLabel($entity);
//        }
//        return $labels;
//    }
}
