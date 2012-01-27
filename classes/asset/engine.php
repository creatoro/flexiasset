<?php defined('SYSPATH') or die('No direct script access.');
/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Asset_Engine {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $file_contents
	 * @param   array   $asset
	 * @return  mixed
	 */
	static public function process($file_contents, array $asset) {}

} // End Asset_Engine