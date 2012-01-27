<?php defined('SYSPATH') or die('No direct script access.');
/**
* Pure php engine
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Engine_Php {

	/**
	 * Process asset with an engine
	 *
	 * @param   string  $file_contents
	 * @param   array   $asset
	 * @return  mixed
	 */
	static public function process($file_contents, array $asset)
	{
		// Turn on output buffering
		ob_start();

		// Eval
		eval('?>'.$file_contents.' ?>');


		return ob_get_clean();
	}

} // End Asset_Engine_Php