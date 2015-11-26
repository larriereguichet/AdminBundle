<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::NONE_LEVEL)
    ->fixers([
        'psr0',
        'encoding',
        'short_tag',
        'braces',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'array_element_no_space_before_comma',
        'array_element_white_space_after_comma',
        'blankline_after_open_tag',
        'function_typehint_space',
        'include',
        'join_function',
        'list_commas',
        'multiline_array_trailing_comma',
        'namespace_no_leading_whitespace',
    ])
    ->finder($finder)
    ;
