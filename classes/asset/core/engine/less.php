<?php defined('SYSPATH') or die('No direct script access.');
/**
* Lessphp engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Engine_Less extends Asset_Engine {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $file_contents
	 * @param   array   $asset
	 * @return  mixed
     * @uses    Kohana::find_file
	 */
	static public function process($file_contents, array $asset)
	{
		// Include the engine
		include_once Kohana::find_file('vendor/lessphp', 'lessc');

		// Set Less
		$lc = new lessc();
		$lc->importDir = dirname($asset['local']).DIRECTORY_SEPARATOR;

		return $lc->parse($file_contents);
	}

} // End Asset_Core_Engine_Less