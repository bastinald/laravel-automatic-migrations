<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Model Paths
    |--------------------------------------------------------------------------
    |
    | This value is the paths to your model files. These paths are used by the
    | command in order to fetch the contents of the migration methods in each
    | model. Specify multiple paths using an array.
    |
    */

    'model_paths' => app_path('Models'),

    /*
    |--------------------------------------------------------------------------
    | Stub Path
    |--------------------------------------------------------------------------
    |
    | This value is the path to the stubs the package should use when executing
    | commands. To use your own stubs, make sure you vendor:publish the package
    | stubs and update this value to point to the resource path.
    |
    */

    'stub_path' => base_path('vendor/bastinald/laravel-automatic-migrations/resources/stubs'),

];
