<?php defined('SYSPATH') or die('No direct script access.');
/**
* Coffiescript engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Coffee extends Asset_Engine {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $file_contents
	 * @param   array   $asset
	 * @return  mixed
	 */
	static public function process($file_contents, array $asset)
	{
		// Set error reporting
		$old = error_reporting(E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT));

		// Include the engine
		include_once Kohana::find_file('vendor/coffeescript', 'coffeescript');

		// Set content
		$file_contents = CoffeeScript\compile($file_contents);

		// Set error reporting
		error_reporting($old);

		return $file_contents;
	}

} // End Asset_Engine_Coffee