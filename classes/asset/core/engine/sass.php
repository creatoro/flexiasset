<?php defined('SYSPATH') or die('No direct script access.');
/**
* Sass css engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Engine_Sass extends Asset_Engine {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $file_contents
	 * @param   array   $asset
	 * @return  mixed
     * @uses    Kohana::find_file
     * @uses    SassParser
	 */
	static public function process($file_contents, array $asset)
	{
		// Set error reporting
		$old = error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

		// Include the engine
		include_once Kohana::find_file('vendor/PHamlP/sass', 'SassParser');

		// Set SASS
		$sass = new SassParser(array());

		// Set content
		$file_contents = $sass->toCss($file_contents, FALSE);

		// Set error reporting
		error_reporting($old);

		return $file_contents;
	}

} // End Asset_Core_Engine_Sass