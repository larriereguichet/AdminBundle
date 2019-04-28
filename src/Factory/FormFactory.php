<?php

namespace LAG\AdminBundle\Factory;

use LAG\AdminBundle\Admin\ActionInterface;
use LAG\AdminBundle\Admin\AdminInterface;
use LAG\AdminBundle\Configuration\AdminConfiguration;
use LAG\AdminBundle\DataProvider\DataProviderInterface;
use LAG\AdminBundle\Exception\Exception;
use LAG\AdminBundle\Field\Definition\FieldDefinitionInterface;
use LAG\AdminBundle\Utils\FormUtils;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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

    public function __construct(DataProviderFactory $dataProviderFactory, FormFactoryInterface $formFactory)
    {
        $this->dataProviderFactory = $dataProviderFactory;
        $this->formFactory = $formFactory;
    }

    public function createEntityForm(AdminInterface $admin, $entity = null): FormInterface
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
            $fields = $dataProvider->getFields($admin);

            foreach ($fields as $field => $definition) {
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
                $formOptions = FormUtils::getFormTypeOptions($definition->getType());
                $formOptions = array_merge($formOptions, $definition->getOptions());

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

    public function createDeleteForm(ActionInterface $action, $entity): FormInterface
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
                ->addModelTransformer(new CallbackTransformer(function(?array $value) {
                    if (null === $value) {
                        $value = [];
                    }

                    return Yaml::dump($value);
                }, function($value) {
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
                ->addModelTransformer(new CallbackTransformer(function(?array $value) {
                    if (null === $value) {
                        $value = [];
                    }

                    return implode(',', $value);
                }, function($value) {
                    if (null === $value) {
                        return [];
                    }

                    return explode(',', $value);
                }))
            ;
        }
    }
}
