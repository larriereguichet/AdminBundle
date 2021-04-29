<?php

namespace LAG\AdminBundle\Field\Render;

use Exception;
use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Exception\View\FieldRenderingException;
use LAG\AdminBundle\Field\View\TextView;
use LAG\AdminBundle\Field\View\View;
use Symfony\Component\PropertyAccess\PropertyAccess;
use function Symfony\Component\String\u;
use Twig\Environment;

class FieldRenderer implements FieldRendererInterface
{
    private Environment $environment;
    private ApplicationConfiguration $appConfig;

    public function __construct(
        Environment $environment,
        ApplicationConfiguration $appConfig
    ) {
        $this->environment = $environment;
        $this->appConfig = $appConfig;
    }

    public function render(View $field, $data): string
    {
        try {
            $originalData = $data;

            if ($field->getOption('property_path') !== null) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $data = $accessor->getValue($data, $field->getOption('property_path'));
            }
            $dataTransformer = $field->getDataTransformer();

            if ($dataTransformer !== null) {
                $data = $dataTransformer($data);
            }
            $context = [
                'data' => $data,
                'name' => $field->getName(),
                'options' => $field->getOptions(),
            ];

            if ($field->getOption('mapped')) {
                $context['object'] = $originalData;
            }

            if ($field instanceof TextView) {
                $render = $data;
            } else {
                $render = $this->environment->render($field->getTemplate(), $context);
            }

            return trim($render);
        } catch (Exception $exception) {
            $message = sprintf(
                'An exception has been thrown when rendering the field "%s" : "%s", template: "%s"',
                $field->getName(),
                $exception->getMessage(),
                $field->getTemplate()
            );
            throw new FieldRenderingException($message, $exception->getCode(), $exception);
        }
    }

    public function renderHeader(View $field): string
    {
        try {
            $text = null;
            $label = $field->getOption('label');

            if ($label === false || u($field->getName())->startsWith('_')) {
                $text = '';
            }

            if ($label !== null) {
                $text = $label;
            }

            if ($label === false && $field->getName() === 'id') {
                $text = '#';
            }

            if ($text === null) {
                $text = ucfirst($field->getName());

                if ($this->appConfig->isTranslationEnabled()) {
                    $text = $field->getName();
                }
            }

            return $this->environment->render('@LAGAdmin/fields/header.html.twig', [
                'data' => $text,
                'name' => $field->getName(),
                'options' => $field->getOptions(),
            ]);
        } catch (Exception $exception) {
            $message = sprintf(
                'An exception has been thrown when rendering the header for the field "%s" : "%s"',
                $field->getName(),
                $exception->getMessage()
            );
            throw new FieldRenderingException($message, $exception->getCode(), $exception);
        }
    }
}
