<?php

namespace LAG\AdminBundle;

use LAG\AdminBundle\DependencyInjection\CompilerPass\ActionCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\DataProviderCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\FieldCompilerPass;
use LAG\AdminBundle\DependencyInjection\CompilerPass\ResponderCompilerPass;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LAGAdminBundle extends Bundle implements PrependExtensionInterface
{
    // CRUD Actions
    const SERVICE_ID_LIST_ACTION = 'lag.admin.actions.list';
    const SERVICE_ID_CREATE_ACTION = 'lag.admin.actions.create';
    const SERVICE_ID_EDIT_ACTION = 'lag.admin.actions.edit';
    const SERVICE_ID_DELETE_ACTION = 'lag.admin.actions.delete';
    
    const SERVICE_ID_ACTION_FACTORY = 'lag.admin.action_factory';
    const SERVICE_ID_ACTION_REGISTRY = 'lag.admin.action_registry';
    
    // Responders
    const SERVICE_ID_LIST_RESPONDER = 'lag.admin.action.list_responder';
    
    // Form Handlers
    const SERVICE_ID_EDIT_FORM_HANDLER = 'lag.admin.form.edit_form_handler';
    const SERVICE_ID_LIST_FORM_HANDLER = 'lag.admin.form.list_form_handler';
    
    // Service Tags
    const SERVICE_TAG_ACTION = 'lag.admin.action';
    const SERVICE_TAG_FORM_HANDLER = 'lag.admin.form_handler';
    
    // Request Admin parameters
    const REQUEST_PARAMETER_ADMIN = '_admin';
    const REQUEST_PARAMETER_ACTION = '_action';
    
    /**
     * @return string[]
     */
    public static function getDefaultActionServiceMapping()
    {
        return [
            'list' => LAGAdminBundle::SERVICE_ID_LIST_ACTION,
            'edit' => LAGAdminBundle::SERVICE_ID_EDIT_ACTION,
            'delete' => LAGAdminBundle::SERVICE_ID_DELETE_ACTION,
            'create' => LAGAdminBundle::SERVICE_ID_CREATE_ACTION,
        ];
    }
    
    /**
     * Add the field and the data provider compiler pass
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        // register field compiler pass
        $container->addCompilerPass(new FieldCompilerPass());
        $container->addCompilerPass(new DataProviderCompilerPass());
        $container->addCompilerPass(new ActionCompilerPass());
        $container->addCompilerPass(new ResponderCompilerPass());
    }
    
    /**
     * @return bool|ExtensionInterface
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $class = $this->getContainerExtensionClass();

            if (class_exists($class)) {
                $extension = new $class();

                if (!$extension instanceof ExtensionInterface) {
                    throw new LogicException(sprintf('Extension %s must implement Symfony\Component\DependencyInjection\Extension\ExtensionInterface.', $class));
                }
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }
        if ($this->extension) {
            return $this->extension;
        }
        
        return false;
    }
    
    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('twig', [
            'globals' => [
                'config' => new Expression('service("lag.admin.configuration_storage").getApplicationConfiguration()'),
            ],
        ]);
    }
}
