<?php

namespace LAG\AdminBundle\Application\Configuration;

use JK\Configuration\Configuration;
use LAG\AdminBundle\Exception\Exception;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Symfony\Component\String\u;

class ActionLinkConfiguration extends Configuration
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('route', null)
            ->setAllowedTypes('route', ['string', 'null'])

            ->setDefault('route_parameters', [])
            ->setAllowedTypes('route_parameters', 'array')

            ->setDefault('url', null)
            ->setAllowedTypes('url', ['string', 'null'])
            ->setNormalizer('url', function (Options $options, $value): ?string {
                if (
                    $value === null
                    && $options->offsetGet('route') === null
                    && $options->offsetGet('admin') === null
                    && $options->offsetGet('action') === null
                ) {
                    dump($options);
                    throw new Exception('The link action should contains an url, a route, or an admin and action');
                }

                return $value;
            })

            ->setDefault('admin', null)
            ->setAllowedTypes('admin', ['string', 'null'])

            ->setDefault('action', null)
            ->setAllowedTypes('action', ['string', 'null'])


            ->setDefault('text', null)
            ->setAllowedTypes('text', ['string', 'null'])
            ->setNormalizer('text', function (Options $options, $value): string {
                if ($value === null) {
                    return 'lag_admin.actions.'.$options->offsetGet('action');
                }

                return $value;
            })

            ->setDefault('attr', [])
            ->setAllowedTypes('attr', 'array')
        ;
    }
}
