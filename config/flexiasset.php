<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	'default' => array(
		/**
		 * Enable or disable the display of compiled files
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
		 * Enable or disable the merging of files
		 *
		 * Example:
		 *
		 * 	'merge' => Kohana::$environment !== Kohana::DEVELOPMENT,
		 *
		 */
		'merge'            => FALSE,

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
		'merge_settings'   => array(
			Asset::STYLESHEET => array(),
			Asset::JAVASCRIPT => array(),
		),

		/**
		 * The DocumentRoot of the website
		 */
		'root'             => DOCROOT,

		/**
		 * The extensions of the asset types
		 */
		'extension'        => array(
			Asset::STYLESHEET => 'css',
			Asset::JAVASCRIPT => 'js',
		),

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