<?php defined('SYSPATH') or die('No direct script access.');

// Asset route
Route::set('asset', 'asset(/<action>(/<id>))')
    ->defaults(array(
        'controller' => 'asset',
        'action' => 'compile',
    ));