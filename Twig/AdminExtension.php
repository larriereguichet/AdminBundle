<?php

namespace BlueBear\AdminBundle\Twig;

use BlueBear\AdminBundle\Admin\Field;
use BlueBear\AdminBundle\Admin\Field\EntityFieldInterface;
use BlueBear\AdminBundle\Admin\FieldInterface;
use BlueBear\AdminBundle\Utils\RecursiveImplode;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Twig_Extension;
use Twig_SimpleFunction;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminExtension
 *
 * Admin utils functions for twig
 */
class AdminExtension extends Twig_Extension
{
    use RecursiveImplode;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * Return sort column url, with existing request parameters, according to field name
     *
     * @param Request $request
     * @param $fieldName
     * @return string
     * TODO rename function
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

    public function getSortColumnIconClass($order = null, $fieldName, $sort)
    {
        // when no order is provided, no icon should be displayed
        $class = '';

        if ($fieldName == $sort) {
            if ($order == 'ASC') {
                $class = 'fa fa-sort-asc';
            } else if ($order = 'DESC') {
                $class = 'fa fa-sort-desc';
            }
        }
        return $class;
    }

    /**
     * Render a field of an entity
     *
     * @param FieldInterface $field
     * @param $entity
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

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('getSortColumnUrl', [$this, 'getSortColumnUrl']),
            new Twig_SimpleFunction('getSortColumnIconClass', [$this, 'getSortColumnIconClass']),
            new Twig_SimpleFunction('field', [$this, 'field']),
            new Twig_SimpleFunction('routeParameters', [$this, 'routeParameters']),
        ];
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bluebear.admin';
    }
}
