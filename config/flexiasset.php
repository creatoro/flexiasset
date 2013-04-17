<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	/**
	 * Enable or disable the display of compiled files
	 *
	 * This is a site wide setting.
	 *
	 * This will only set what file names are displayed in the
	 * resulting HTML code and won't initiate compiling.
	 *
	 * If enabled the file names are created based on this configuration,
	 * if disabled the original file names are displayed (which would be
	 * the behavior if you use Kohana's HTML::style or HTML::script).
	 *
	 * Example:
	 *
	 * 	'display_compiled' => Kohana::$environment !== Kohana::DEVELOPMENT,
	 *
	 */
	'display_compiled' => TRUE,

	/**
	 * Enable or disable the merging of files
	 *
	 * This is a site wide setting.
	 *
	 * Example:
	 *
	 * 	'merge' => Kohana::$environment !== Kohana::DEVELOPMENT,
	 *
	 */
	'merge'            => FALSE,

	/**
	 * The default configuration
	 *
	 * If you have multiple asset groups in your HTML, then you should create
	 * a configuration like this for each group with a unique name.
	 *
	 * Be careful to not to use names like 'display_compiled' and 'merge'
	 * as these are site wide setting names.
	 *
	 * Every setting is optional.
	 */
	'default' => array(
		/**
		 * The compressor to use for each asset type
		 *
		 * Example:
		 *
		 * 	'compressor' => array(
		 *  	Asset::STYLESHEET => 'cssmin',
		 * 		Asset::JAVASCRIPT => 'jsmin',
		 * 	),
		 *
		 */
		'compressor'       => array(),

		/**
		 * Settings for the merged files
		 *
		 * Everything is optional by default, a full configuration would look
		 * like this:
		 *
		 * 	'merge_settings' => array(
		 * 		Asset::STYLESHEET => array(
		 * 			'file'       => 'style-min',                // file name for merged styles
		 * 			'attributes' => array('media' => 'screen'), // file attributes
		 * 			'protocol'   => 'http',                     // protocol to pass to URL::base()
		 * 			'index'      => TRUE,                       // include the index page
		 * 		),
		 * 		Asset::JAVASCRIPT => array(
		 * 			'file'       => 'script-min',               // file name for merged scripts
		 * 			'attributes' => array('defer' => 'defer'),  // file attributes
		 * 			'protocol'   => 'http',                     // protocol to pass to URL::base()
		 * 			'index'      => TRUE,                       // include the index page
		 * 		),
		 * 	),
		 *
		 */
		'merge_settings'   => array(),

		/**
		 * The DocumentRoot of the website
		 */
		'root'             => DOCROOT,

		/**
		 * Enable or disable cache busting of files
		 */
		'cache_bust'       => FALSE,

		/**
		 * The directory where you want the files to be placed
		 *
		 * If this directory is different from the asset's directory, then upon compile the
		 * asset is copied to the output directory even if no processing or merging was done.
		 *
		 * If no directory is specified then the input directory is used.
		 *
		 * Example:
		 *
		 * 	'output_dir' => array(
		 *  	Asset::STYLESHEET => 'assets/css/build/',
		 * 		Asset::JAVASCRIPT => 'assets/js/build/',
		 * 	),
		 *
		 */
		'output_dir'       => array(),
	),

);