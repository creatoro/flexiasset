<?php defined('SYSPATH') or die('No direct script access.');
/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
abstract class Asset_Compressor {

	/**
	 * Compress content of asset
	 *
	 * @param   string  $content
	 * @return  mixed
	 */
	static public function process($content) {}

} // Asset_Compressor