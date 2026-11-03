<?php

// Само настройката, която сменяме: страниците са в resources/js/pages и са .jsx.
// Останалите настройки идват от пакета.
return [
    'testing' => [
        'ensure_pages_exist' => true,
        'page_paths' => [resource_path('js/pages')],
        'page_extensions' => ['jsx'],
    ],
];
