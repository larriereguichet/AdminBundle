<?php

declare(strict_types=1);

namespace LAG\AdminBundle\Form\Type\AutoComplete;

use Symfony\Component\Form\AbstractType;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

#[AsEntityAutocompleteField]
final class AutoCompleteType extends AbstractType
{
    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
