<?php

namespace AdminBundle\Field;

use LAG\AdminBundle\Configuration\ApplicationConfiguration;
use LAG\AdminBundle\Field\LinkField;
use LAG\AdminBundle\Tests\TestCase;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkFieldTest extends TestCase
{
    private LinkField $field;

    public function testConfigureOptions(): void
    {
        $appConfig = new ApplicationConfiguration(['resources_path' => 'test']);
        $this->field->setApplicationConfiguration($appConfig);
        $resolver = new OptionsResolver();
        $this->field->configureOptions($resolver);
    }

    protected function setUp(): void
    {
        $this->field = new LinkField('my_field', 'link');
    }
}
