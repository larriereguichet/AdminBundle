<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminAwareInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Event\Events;
use LAG\AdminBundle\Event\Events\FormEvent;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Yaml;

class FormFactory implements \LAG\AdminBundle\Factory\FormFactoryInterface
{
    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        DataProviderFactory $dataProviderFactory,
        FormFactoryInterface $formFactory,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->dataProviderFactory = $dataProviderFactory;
        $this->formFactory = $formFactory;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createEntityForm(AdminInterface $admin, Request $request, $entity = null): FormInterface
    {
        $dataProvider = $this->getDataProvider($admin->getConfiguration());

        if (null === $entity) {
            $entity = $dataProvider->create($admin);
        }
        $formType = $admin->getConfiguration()->get('form');

        if (null === $formType) {
            $builder = $this
                ->formFactory
                ->createBuilder(FormType::class, $entity, [
                    'label' => false,
                ])
            ;
            $event = new FormEvent($admin, $request);
            $this->eventDispatcher->dispatch(Events::FORM_PRE_CREATE_ENTITY_FORM, $event);

            foreach ($event->getFieldDefinitions() as $field => $definition) {
                if (!$definition instanceof FieldDefinitionInterface) {
                    throw new Exception(
                        'The field definition should implements "'.FieldDefinitionInterface::class.'", got "'.gettype($definition)
                    );
                }
                // Usually we do not want to edit those values in a Form
                if (in_array($field, [
                        'createdAt',
                        'updatedAt',
                    ]) && 'datetime' === $definition->getType()) {
                    continue;
                }
                $formType = FormUtils::convertShortFormType($definition->getType());
                $formOptions = array_merge(
                    FormUtils::getFormTypeOptions($definition->getType()),
                    $definition->getFormOptions()
                );

                $builder->add($field, $formType, $formOptions);
                $this->addFormTransformers($builder, $field, $definition->getType());
            }
            $form = $builder->getForm();
        } else {
            $form = $this
                ->formFactory
                ->create($formType, $entity)
            ;
        }

        return $form;
    }

    public function createDeleteForm(ActionInterface $action, Request $request, $entity): FormInterface
    {
        $form = $this
            ->formFactory
            ->create($action->getConfiguration()->get('form'), $entity)
        ;

        return $form;
    }

    private function getDataProvider(AdminConfiguration $configuration): DataProviderInterface
    {
        return $this
            ->dataProviderFactory
            ->get($configuration->get('data_provider'))
        ;
    }

    private function addFormTransformers(FormBuilderInterface $builder, string $field, ?string $type): void
    {
        if ('array' === $type) {
            $builder
                ->get($field)
                ->addModelTransformer(new CallbackTransformer(function (?array $value = null) {
                    if (null === $value) {
                        $value = [];
                    }

                    return Yaml::dump($value);
                }, function ($value) {
                    if (null === $value) {
                        return [];
                    }

                    return Yaml::parse($value);
                }))
            ;
        }

        if ('simple_array' === $type) {
            $builder
                ->get($field)
                ->addModelTransformer(new CallbackTransformer(function (?array $value = null) {
                    if (null === $value) {
                        $value = [];
                    }

                    return implode(',', $value);
                }, function ($value) {
                    if (null === $value) {
                        return [];
                    }

                    return explode(',', $value);
                }))
            ;
        }
    }
}
