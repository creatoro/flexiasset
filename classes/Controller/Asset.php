<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Asset extends Controller {

    public function action_compile()
    {
        // Enable compile
        Asset::$compile = TRUE;
    }

} // End Controller_Asset