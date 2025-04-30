<?php

return [
    'parameters_default' => [
        'includes' => env('INCLUDES_PARAMETER', 'includes'),
        'search'   => env('SEARCH_PARAMETER', 'search'),
        'filter'   => env('FILTER_PARAMETER', 'filter'),
        'sort'     => env('SORT_PARAMETER', 'sort'),
        'sort_by'  => env('SORT_BY_PARAMETER', 'sort_by'),
        'trash'    => env('TRASH_PARAMETER', 'trash'),
    ],
];
