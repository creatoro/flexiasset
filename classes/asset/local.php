<?php defined('SYSPATH') or die('No direct script access.');
/**
* Create the HTML code for remote assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Local extends Asset {

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
		$this->_assets = $instance->_local_assets;
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

				if ($compile)
				{
					// Compile assets
					$assets = $this->compile($assets);

					if ( ! isset($assets['output']))
					{
						// Assets are not merged, just processed or compressed
						foreach ($assets as $asset)
						{
							// Add HTML code for each asset
							$html .= $this->html($asset);
						}
					}
					else
					{
						// Add information for HTML code
						$assets = Arr::merge($assets, array(
							'attributes' => Arr::get($this->_config['merge_settings'][$assets['type']], 'attributes'),
							'protocol'   => Arr::get($this->_config['merge_settings'][$assets['type']], 'protocol'),
							'index'      => Arr::get($this->_config['merge_settings'][$assets['type']], 'index', FALSE),
						));

						// Add HTML code for merged assets
						$html .= $this->html($assets);
					}
				}
				else
				{
					// Compile not needed
					foreach ($assets as $asset)
					{
						// Save asset in a different directory if needed
						$asset = $this->save($asset);

						// Add HTML code for each asset
						$html .= $this->html($asset);
					}
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
	 * Compile assets
	 *
	 * Processes assets with engines and compressors, it also merges
	 * them if needed.
	 *
	 * @param   array  $assets
	 * @return  mixed
	 */
	protected function compile($assets)
	{
		// Set compiled content
		$compiled_content = NULL;

		foreach ($assets as $asset)
		{
			// Set default file contents
			$file_contents = NULL;

			// Get the file parts
			$fileparts = explode('.', $asset['basename']);

			// Get the extension index
			$extension_index = array_search($this->_config['extension'][$asset['type']], $fileparts);

			// Set engines
			$engines = array_reverse(array_slice($fileparts, $extension_index + 1));

			if ( ! empty($engines))
			{
				// Get file contents
				$file_contents = file_get_contents($asset['local']);

				foreach ($engines as $engine)
				{
					// Set engine class
					$engine_class = 'Asset_Engine_'.ucfirst($engine);

					if ( ! class_exists($engine_class))
					{
						// No such engine
						throw new Kohana_Exception('The asset engine :engine does not exist', array(
							':engine' => strtoupper($engine),
						));
					}

					// Load engine
					$engine = new $engine_class();

					// Process content
					$file_contents = $engine->process($file_contents, $asset);
				}
			}

			if ($compressor = Arr::get($this->_config['compressor'], $asset['type']))
			{
				// Set compressor class
				$compressor_class = 'Asset_Compressor_'.ucfirst($compressor);

				if ( ! class_exists($compressor_class))
				{
					// No such engine
					throw new Kohana_Exception('The asset compressor :compressor does not exist', array(
						':compressor' => strtoupper($compressor),
					));
				}

				// Load compressor
				$compressor = new $compressor_class();

				if ($file_contents === NULL)
				{
					// Get file contents
					$file_contents = file_get_contents($asset['local']);
				}

				// Process content
				$file_contents = $compressor->process($file_contents, $asset);
			}

			if ($this->_merge)
			{
				if ( ! isset($asset_to_merge))
				{
					// Set information for saving the merged asset
					$asset_to_merge = array(
						'type'       => $asset['type'],
						'extension'  => $asset['extension'],
					);
				}

				if ($file_contents === NULL)
				{
					// No processing was done, but merge is needed, get file contents
					$file_contents = file_get_contents($asset['local']);
				}

				// Add comment to compiled content
				$compiled_content .= "/* File: ".$asset['remote']."\n   Compiled at: ".date("Y-m-d H:i:s")." \n================================ */\n";

				// Add to compiled content
				$compiled_content .= $file_contents;
			}
			else
			{
				// No merge needed
				$compiled_content = $file_contents;

				// Save file
				$saved_file[] = $this->save($asset, $compiled_content);
			}
		}

		if ($this->_merge)
		{
			// Save merged file
			$saved_file = $this->save($asset_to_merge, $compiled_content);
		}

		return $saved_file;
	}

	/**
	 * Save asset to file
	 *
	 * Saves the compiled content to a file, or copies the file as it is
	 * if needed.
	 *
	 * @param   array   $asset
	 * @param   string  $compiled_content
	 * @return  array
	 */
	public function save($asset, $compiled_content = NULL)
	{
		// Set basic file name
		$file_name = Arr::get($this->_config['output_dir'], $asset['type'], $asset['input_dir']);

		if ($this->_merge AND $compiled_content)
		{
			// Get configured file name
			$configured_name = Arr::get($this->_config['merge_settings'][$asset['type']], 'file', $this->_instance);

			if (is_array($configured_name))
			{
				// Get the name for the current asset type
				$configured_name = Arr::get($configured_name, $asset['type'], $this->_instance);
			}

			// Add merged file name
			$file_name .= $configured_name;
		}
		else
		{
			// No merging done, add original file name
			$file_name .= $asset['filename'];
		}

		// Add extension to file name
		$file_name .= '.'.$asset['extension'];

		if ($compiled_content !== NULL)
		{
			// Save compiled file
			file_put_contents($file_name, $compiled_content);
		}
		elseif ($file_name !== $asset['remote'])
		{
			// Copy file
			copy($asset['local'], $file_name);
		}

		// Add file name to asset
		$asset['output'] = $file_name;

		return $asset;
	}

	/**
	 * Create the HTML code for a single asset
	 *
	 * @param   array  $asset
	 * @return  mixed
	 */
	protected function html($asset)
	{
		if ($this->_config['cache_bust'])
		{
			// Add cache busting to file name
			$asset['output'] .= '?v'.time();
		}

		// Set HTML
		$html = HTML::$asset['type']($asset['output'], $asset['attributes'], $asset['protocol'], $asset['index']);

		return $html;
	}

} // End Asset_Local