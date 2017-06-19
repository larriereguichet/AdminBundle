<?php

namespace LAG\AdminBundle\Twig;

use Knp\Menu\Provider\MenuProviderInterface;
use Knp\Menu\Twig\Helper;
use LAG\AdminBundle\Admin\Behaviors\TranslationKeyTrait;
use LAG\AdminBundle\Admin\Request\RequestHandler;
use LAG\AdminBundle\Configuration\Factory\ConfigurationFactory;
use LAG\AdminBundle\Field\EntityAwareInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Routing\RouteNameGenerator;
use LAG\AdminBundle\View\ViewInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminExtension.
 *
 * Admin utils functions for twig
 */
class AdminExtension extends Twig_Extension
{
    use TranslationKeyTrait;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * @var string
     */
    private $translationPattern;
    
    /**
     * @var Helper
     */
    private $menuHelper;
    
    /**
     * @var RequestHandler
     */
    private $requestHandler;
    
    /**
     * @var RequestStack
     */
    private $requestStack;
    
    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;
    
    /**
     * @var MenuProviderInterface
     */
    private $menuProvider;
    
    /**
     * AdminExtension constructor.
     *
     * @param string                $translationPattern
     * @param RouterInterface       $router
     * @param TranslatorInterface   $translator
     * @param RequestHandler        $requestHandler
     * @param RequestStack          $requestStack
     * @param Helper                $menuHelper
     * @param MenuProviderInterface $menuProvider
     * @param ConfigurationFactory  $configurationFactory
     */
    public function __construct(
        $translationPattern,
        MenuProviderInterface $menuProvider,
        RouterInterface $router,
        TranslatorInterface $translator,
        RequestHandler $requestHandler,
        RequestStack $requestStack,
        Helper $menuHelper,
        ConfigurationFactory $configurationFactory
    ) {
        $this->router = $router;
        $this->translator = $translator;
        $this->translationPattern = $translationPattern;
        $this->menuHelper = $menuHelper;
        $this->requestHandler = $requestHandler;
        $this->requestStack = $requestStack;
        $this->configurationFactory = $configurationFactory;
        $this->menuProvider = $menuProvider;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getSortColumnUrl', [$this, 'getSortColumnUrl']),
            new Twig_SimpleFunction('getSortColumnIconClass', [$this, 'getSortColumnIconClass']),
            new Twig_SimpleFunction('field', [$this, 'field']),
            new Twig_SimpleFunction('field_title', [$this, 'fieldTitle']),
            new Twig_SimpleFunction('route_parameters', [$this, 'routeParameters']),
            new Twig_SimpleFunction('admin_menu_render', [$this, 'renderMenu']),
            new Twig_SimpleFunction('admin_config', [$this, 'getConfigParameter']),
            new Twig_SimpleFunction('admin_url', [$this, 'generateUrl']),
        ];
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('camelize', [$this, 'camelize']),
        ];
    }

    /**
     * Return sort column url, with existing request parameters, according to field name.
     *
     * @param Request $request
     * @param $fieldName
     *
     * @return string
     */
    public function getSortColumnUrl(Request $request, $fieldName)
    {
        // get query string to not delete existing parameters
        $queryString = $request->query->all();
        $queryString['sort'] = $fieldName;

        if (array_key_exists('order', $queryString)) {
            // sort by opposite sorting than current
            $sort = $queryString['order'] == 'ASC' ? 'DESC' : 'ASC';
            $queryString['order'] = $sort;
        } else {
            // if no order was provided, it means that list is sorted by default order (ASC), so we must sort by DESC
            $queryString['order'] = 'DESC';
        }

        return $this
            ->router
            ->generate($request->get('_route'), $queryString);
    }

    /**
     * Return an array of query string parameters, updated with sort field name.
     *
     * @param ParameterBagInterface $parameters
     * @param $fieldName
     *
     * @return array
     */
    public function getOrderQueryString(ParameterBagInterface $parameters, $fieldName)
    {
        $parameters->set('sort', $fieldName);

        if ($parameters->has('order')) {
            // sort by opposite order
            $order = $parameters->get('order') == 'ASC' ? 'DESC' : 'ASC';
            $parameters->set('order', $order);
        } else {
            // if no order was provided, it means that list is sorted by default order (ASC), so we must sort by DESC
            $parameters->set('order', 'DESC');
        }
        return $parameters->all();
    }

    /**
     * @param null $order
     * @param $fieldName
     * @param $sort
     *
     * @return string
     */
    public function getSortColumnIconClass($order = null, $fieldName, $sort)
    {
        // when no order is provided, no icon should be displayed
        $class = '';

        if ($fieldName == $sort) {
            if ($order == 'ASC') {
                $class = 'fa fa-sort-asc';
            } elseif ($order == 'DESC') {
                $class = 'fa fa-sort-desc';
            }
        }

        return $class;
    }

    /**
     * Render a field of an entity.
     *
     * @param FieldInterface $field
     * @param $entity
     *
     * @return string
     */
    public function field(FieldInterface $field, $entity)
    {
        $value = null;
        $accessor = PropertyAccess::createPropertyAccessor();

        // if name starts with a underscore, it is a custom field, not mapped to the entity
        if (substr($field->getName(), 0, 1) != '_') {
            // get raw value from object
            $value = $accessor->getValue($entity, $field->getName());
        }
        
        if ($field instanceof EntityAwareInterface) {
            $field->setEntity($entity);
        }
        $render = $field->render($value);

        return $render;
    }

    /**
     * Return a the title of the field, camelized or translated.
     *
     * @param $fieldName
     * @param null $adminName
     *
     * @return string
     */
    public function fieldTitle($fieldName, $adminName = null)
    {
        if (null !== $this->translationPattern) {
            $title = $this
                ->translator
                ->trans($this->getTranslationKey($this->translationPattern, $fieldName, $adminName));
        } else {
            $title = $this->camelize($fieldName);
        }

        return $title;
    }

    /**
     * @param array $parameters
     * @param $entity
     *
     * @return array
     */
    public function routeParameters(array $parameters, $entity)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $routeParameters = [];

        foreach ($parameters as $parameterName => $fieldName) {
            if (is_array($fieldName) && !count($fieldName)) {
                $fieldName = $parameterName;
            }
            $routeParameters[$parameterName] = $accessor->getValue($entity, $fieldName);
        }

        return $routeParameters;
    }

    /**
     * Camelize a string (using Container camelize method)
     *
     * @param $string
     *
     * @return string
     */
    public function camelize($string)
    {
        return Container::camelize($string);
    }
    
    public function renderMenu($menuName, array $options = [])
    {
        if (!$this->menuProvider->has($menuName)) {
            return '';
        }
        
        $request = $this->requestStack->getMasterRequest();
    
        if ($this->requestHandler->supports($request)) {
            $menu = $this->menuHelper->get($menuName, [], $options);
            $content = $this->menuHelper->render($menu);
        } else {
            $content = $this->menuHelper->render($menuName);
        }
        
        return $content;
    }
    
    public function getConfigParameter($parameter)
    {
        return $this
            ->configurationFactory
            ->getApplicationConfiguration()
            ->getParameter($parameter)
        ;
    }
    
    public function generateUrl($actionName, ViewInterface $view, array $parameters = [])
    {
        $generator = new RouteNameGenerator();
        $routeName = $generator->generate($actionName, $view->getName(), $view->getAdminConfiguration());
    
        return $this
            ->router
            ->generate($routeName, $parameters)
        ;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'lag.admin';
    }
}
