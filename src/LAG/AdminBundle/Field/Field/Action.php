<?php

namespace LAG\AdminBundle\Field\Field;

class Action extends Link
{
    /**
     * Display a link to an Action.
     *
     * @param string $value
     *
     * @return string
     */
    public function render($value)
    {
        $value = $this
            ->options
            ->get('title');

        return parent::render($value);
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getType()
    {
        return 'action';
    }
}
