<?php defined('SYSPATH') or die('No direct script access.');
/**
* Collection of assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset {

	// Default short names for types
	const JAVASCRIPT = 'script';
	const STYLESHEET = 'style';

	/**
	 * @var  string  default configuration
	 */
	public static $default = 'default';

	/**
	 * @var  array  Asset instances
	 */
	public static $instances = array();

	/**
	 * Get a singleton Asset instance. If configuration is not specified,
	 * the default configuration is loaded.
	 *
	 *     // Load the default asset configuration
	 *     $asset = Asset::instance('head');
	 *
	 *     // Create a custom configured instance
	 *     $asset = Asset::instance('head', 'custom');
	 *
	 * @param   string  $name    instance name
	 * @param   array   $config  configuration parameters
	 * @return  Asset
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			// Use the default instance name
			$name = Asset::$default;
		}

		if ( ! isset(Asset::$instances[$name]))
		{
			if ($config === NULL)
			{
				// Load the configuration for this asset group
				$config = Kohana::$config->load('flexiasset')->$name;
			}

			// Create the asset instance
			new Asset($name, $config);
		}

		return Asset::$instances[$name];
	}

	/**
	 * @var  string  instance name
	 */
	protected $_instance;

	/**
	 * @var  array  configuration
	 */
	protected $_config;

	/**
	 * @var  bool  render compiled files or original files
	 */
	protected $_display_compiled = FALSE;

	/**
	 * Stores the asset configuration locally and name the instance.
	 *
	 * [!!] This method cannot be accessed directly, you must use [Asset::instance].
	 *
	 * @param   string  $name
	 * @param   array   $config
	 * @return  void
	 */
	protected function __construct($name, array $config)
	{
		// Set the instance name
		$this->_instance = $name;

		// Store the config locally
		$this->_config = $config;

		// Set render compiled
		$this->_display_compiled = $this->_config['display_compiled'];

		// Store the asset instance
		Asset::$instances[$name] = $this;
	}

	/**
	 * Add style
	 *
	 * @param   string  $file
	 * @param   array   $attributes
	 * @param   mixed   $protocol
	 * @param   bool    $index
	 * @return  Asset
	 */
	public function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		return $this->add(Asset::STYLESHEET, $file, $attributes, $protocol, $index);
	}

	/**
	 * Add script
	 *
	 * @param   string  $file
	 * @param   array   $attributes
	 * @param   mixed   $protocol
	 * @param   bool    $index
	 * @return  Asset
	 */
	public function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		return $this->add(Asset::JAVASCRIPT, $file, $attributes, $protocol, $index);
	}

	/**
	 * @var  array  local assets
	 */
	protected $_local_assets = array();

	/**
	 * @var  array  remote assets
	 */
	protected $_remote_assets = array();

	/**
	 * Adds assets to the appropriate type
	 *
	 * @param   string  $type
	 * @param   string  $file
	 * @param   array   $attributes
	 * @param   mixed   $protocol
	 * @param   bool    $index
	 * @return  Asset
	 */
	protected function add($type, $file, $attributes, $protocol, $index)
	{
		// Add basic information to asset
		$asset = array(
			'type'       => $type,
			'extension'  => $this->_config['extension'][$type],
			'attributes' => $attributes,
			'protocol'   => $protocol,
			'index'      => $index,
		);

		// Get all file information
		$asset = $this->file_info($file, $asset);

		if ($condition = Arr::get($asset['attributes'], 'ie_condition'))
		{
			// Remove condition from attributes
			unset($asset['attributes']['ie_condition']);
		}

		if ($asset['local'])
		{
			// Add to local assets
			$this->_local_assets[$type][$condition][] = $asset;
		}
		else
		{
			// Add to remote assets
			$this->_remote_assets[$type][$condition][] = $asset;
		}

		return $this;
	}

	/**
	 * Get file information
	 *
	 * @param   string  $file
	 * @param   array   $basic_info
	 * @return  array
	 */
	protected function file_info($file, $basic_info)
	{
		// Set up the initial array to return
		$info = array(
			// Path that is used for the HTML tag
			'remote' => $file,
			// Path on the filesystem
			'local'  => $file,
		);

		// Merge in basic info
		$info = Arr::merge($info, $basic_info);

		// Check to see if it's a remote asset
		if (Valid::url($file))
		{
			// Disallow local path access
			$info['local'] = NULL;

			// Nothing else needed
			return $info;
		}

		// Set the full local path to file
		$info['local'] = $this->_config['root'].$info['local'];

		// Normalize the local path
		$info['local'] = str_replace('\\', '/', $info['local']);

		// Get path information
		$pathinfo = pathinfo($info['remote']);

		// Get basename
		$info['basename'] = $pathinfo['basename'];

		// Get basename
		$info['filename'] = $pathinfo['filename'];

		// Get the input directory
		$info['input_dir'] = $pathinfo['dirname'].'/';

		return $info;
	}

	/**
	 * Renders the HTML code
	 *
	 * @param   bool  $compile
	 * @return  string
	 */
	public function render($compile = FALSE)
	{
		if ($compile)
		{
			// Force displaying of compiled files
			$this->_display_compiled = TRUE;
		}

		// Set local and remote HTML
		$local_html = $remote_html = '';

		if ( ! empty($this->_local_assets))
		{
			// Local HTML
			$local_html = new Asset_Local($this->_instance, $compile);

			// Render local HTML
			$local_html = $local_html->render($compile);
		}

		if ( ! empty($this->_remote_assets))
		{
			// Remote HTML
			$remote_html = new Asset_Remote($this->_instance, $compile);

			// Render remote HTML
			$remote_html = $remote_html->render($compile);
		}

		return $local_html.$remote_html;
	}

} // End Assets