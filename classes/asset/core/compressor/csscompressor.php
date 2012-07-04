<?php defined('SYSPATH') or die('No direct script access.');
/**
* minify_css_compressor processor
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Compressor_Csscompressor extends Asset_Compressor {

	/**
	 * Compress content of asset
	 *
	 * @param   string  $content
	 * @return  mixed
     * @uses    Kohana::find_file
     * @uses    minify_css_compressor::process
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'minify_css_compressor/Compressor');
		
		return minify_css_compressor::process($content);
	}

} // End Asset_Core_Compressor_Csscompressor