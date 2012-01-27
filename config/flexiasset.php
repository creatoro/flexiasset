<?php defined('SYSPATH') OR die('No direct script access.');

return array(

	'default' => array(
		/**
		 * Enable or disable the merging of files
		 *
		 * Example:
		 *
		 * 	'merge' => Kohana::$environment !== Kohana::DEVELOPMENT,
		 *
		 */
		'merge'          => FALSE,

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
		'merge_settings' => array(
			Asset::STYLESHEET => array(),
			Asset::JAVASCRIPT => array(),
		),

		/**
		 * Enable or disable cache busting of files
		 */
		'cache_bust'     => FALSE,

		/**
		 * The DocumentRoot of the website
		 */
		'root'           => DOCROOT,

		/**
		 * The extensions of the asset types
		 */
		'extension'      => array(
			Asset::STYLESHEET => 'css',
			Asset::JAVASCRIPT => 'js',
		),

		/**
		 * The directory where you want the files to be placed
		 *
		 * If this directory is different from the asset's directory, then the
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
		'output_dir'     => array(),

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
		'compressor'     => array(),
	),

);