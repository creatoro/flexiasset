<?php defined('SYSPATH') or die('No direct script access.');
/**
* Create the HTML code for remote assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Remote extends Asset {

	/**
	 * @var  array  assets
	 */
	protected $_assets;

	/**
	 * Load configuration and set up environment
	 *
	 * @param   string  $name
	 * @param   bool    $compile
	 * @return  void
	 */
	public function __construct($name, $compile)
	{
		// Load instance
		$instance = Asset::instance($name);

		// Set instance
		$this->_instance = $instance->_instance;

		// Set merge
		$this->_merge = $instance->_config['merge'];

		// Set config
		$this->_config = $instance->_config;

		// Set assets
		$this->_assets = $instance->_remote_assets;
	}

	/**
	 * Render the HTML code for assets
	 *
	 * Adds conditions to assets if needed. Assets are grouped by conditions.
	 *
	 * @param   bool  $compile
	 * @return  string
	 */
	public function render($compile = FALSE)
	{
		// Set HTML
		$html = '';

		foreach ($this->_assets as $condition_group)
		{
			foreach ($condition_group as $condition => $assets)
			{
				if ($condition != NULL)
				{
					// Open condition
					$html .= '<!--[if '.$condition.']>';
				}

				// Compile not needed
				foreach ($assets as $asset)
				{
					// Add HTML code for each asset
					$html .= $this->html($asset);
				}

				if ($condition != NULL)
				{
					// Close condition
					$html .= '<![endif]-->';
				}
			}
		}

		return $html;
	}

	/**
	 * Create the HTML code for a single asset
	 *
	 * @param   array  $asset
	 * @return  mixed
	 */
	protected function html($asset)
	{
		// Set HTML
		$html = HTML::$asset['type']($asset['remote'], $asset['attributes'], $asset['protocol'], $asset['index']);

		return $html;
	}

} // End Asset_Core_Remote