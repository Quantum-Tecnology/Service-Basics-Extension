<?php

declare(strict_types = 1);

return [
    /*
    |--------------------------------------------------------------------------
    | Sensitivity Character
    |--------------------------------------------------------------------------
    |
    | This character is used to indicate that the search should be case-sensitive.
    | To perform a case-sensitive search, wrap your search term with the sensitivity
    | character at the beginning and end. For example, if the sensitivity character
    | is set to '!!', then searching for '!!example!!' will only return results that
    | match 'example' exactly (case-sensitive), while searching for 'example' will
    | return results that match 'example' regardless of case.
    |
    */
    'sensitivity_character' => env('SENSITIVITY_CHARACTER', '!!'),

    /*
    |--------------------------------------------------------------------------
    | Prevent Scopes Duplicated
    |--------------------------------------------------------------------------
    |
    | If true, the scopes will not be applied if they have already been applied.
    | This is useful to avoid applying the same scope multiple times, which can
    | lead to unexpected results or performance issues.
    |
    */
    'prevent_scopes_duplicated' => env('PREVENT_SCOPES_DUPLICATED', true),

    /*
    |--------------------------------------------------------------------------
    | Parameters Default
    |--------------------------------------------------------------------------
    |
    | These parameters are used to define the default parameters for the
    | service. They can be used to customize the behavior of the service.
    | You can change the names of the parameters by setting the environment
    | variables INCLUDES_PARAMETER, SEARCH_PARAMETER, FILTER_PARAMETER,
    | SORT_PARAMETER, SORT_BY_PARAMETER, and TRASH_PARAMETER.
    |
    */
    'parameters_default'        => [
        'includes' => env('INCLUDES_PARAMETER', 'includes'),
        'search'   => env('SEARCH_PARAMETER', 'search'),
        'filter'   => env('FILTER_PARAMETER', 'filter'),
        'sort'     => env('SORT_PARAMETER', 'sort'),
        'sort_by'  => env('SORT_BY_PARAMETER', 'sort_by'),
        'trash'    => env('TRASH_PARAMETER', 'trash'),
    ],
];
