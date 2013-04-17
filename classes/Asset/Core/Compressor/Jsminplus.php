<?php defined('SYSPATH') or die('No direct script access.');
/**
* jsminplus engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Compressor_Jsminplus extends Asset_Compressor {

	/**
	 * Compress content of asset
	 *
	 * @param   string  $content
	 * @return  mixed
     * @uses    Kohana::find_file
     * @uses    jsminplus::minify
	 */
	static public function process($content)
	{
		// Include the processor
		include_once Kohana::find_file('vendor', 'jsminplus/jsminplus');
		
		return jsminplus::minify($content);
	}

} // End Asset_Core_Compressor_Jsminplus