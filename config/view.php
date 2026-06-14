<?php

$compiledPath = env(
    'VIEW_COMPILED_PATH',
    sys_get_temp_dir().DIRECTORY_SEPARATOR.'affipress-compiled-views'
);

if (! is_dir($compiledPath)) {
    @mkdir($compiledPath, 0777, true);
}

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => $compiledPath,
];
