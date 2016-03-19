<?php

namespace LAG\AdminBundle\Twig;

use LAG\AdminBundle\Application\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\Field;
use LAG\AdminBundle\Field\EntityFieldInterface;
use LAG\AdminBundle\Field\FieldInterface;
use LAG\AdminBundle\Utils\TranslationKeyTrait;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @var ApplicationConfiguration
     */
    protected $configuration;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * AdminExtension constructor.
     *
     * @param RouterInterface $router
     * @param TranslatorInterface $translator
     * @param ApplicationConfiguration $configuration
     */
    public function __construct(RouterInterface $router, TranslatorInterface $translator, ApplicationConfiguration $configuration)
    {
        $this->router = $router;
        $this->translator = $translator;
        $this->configuration = $configuration;
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
     *                TODO rename function
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
     * @return mixed
     */
    public function field(FieldInterface $field, $entity)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->getPropertyAccessor();
        $value = null;
        // if name starts with a underscore, it is a custom field, not mapped to the entity
        if (substr($field->getName(), 0, 1) != '_') {
            // get raw value from object
            $value = $accessor->getValue($entity, $field->getName());
        }
        if ($field instanceof EntityFieldInterface) {
            $field->setEntity($entity);
        }
        $render = $field->render($value);

        return $render;
    }

    /**
     * @param $fieldName
     * @param null $adminName
     * @return string
     */
    public function fieldTitle($fieldName, $adminName = null)
    {
        if ($this->configuration->getParameter('translation')['enabled']) {
            $title = $this
                ->translator
                ->trans($this->getTranslationKey($this->configuration, $fieldName, $adminName));
        } else {
            $title = $this->camelize($fieldName);
        }

        return $title;
    }

    /**
     * @param array $parameters
     * @param $entity
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
     * @return string
     */
    public function camelize($string)
    {
        return Container::camelize($string);
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
