<?php defined('SYSPATH') or die('No direct script access.');
/**
* Create the HTML code for remote assets
*
* @package    OpenBuildings/asset-merger
* @author     Ivan Kerin
* @copyright  (c) 2011 OpenBuildings Inc.
* @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
*/
class Asset_Core_Local extends Asset {

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

		// Set global config
		$this->_global_config = $instance->_global_config;

		// Set config
		$this->_config = $instance->_config;

		// Set assets
		$this->_assets = $instance->_local_assets;

		// Set merge
		$this->_merge = $this->_global_config['merge'];

		// Set display compiled
		$this->_display_compiled = $instance->_display_compiled;
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

				foreach ($assets as $key => $asset)
				{
					// Set output file for each asset
					$assets[$key] = $this->output_file($asset, $compile);
				}

				if ($this->_display_compiled)
				{
					if ($compile)
					{
						// Compile assets
						$assets = $this->compile($assets);

						if ($this->_merge)
						{
							// Save compiled and merged asset
							$this->save($assets);
						}
						else
						{
							foreach ($assets as $asset)
							{
								// Save each compiled asset
								$this->save($asset);
							}
						}
					}

					if ($this->_merge)
					{
						if ( ! $compile)
						{
							// Use the first asset's information for HTML as compile didn't run,
							// so we have an array of assets
							$assets = reset($assets);
						}
						else
						{
							// Get merge settings
							$merge_settings = Arr::get($this->_config, 'merge_settings', array(
								Asset::STYLESHEET => array(),
								Asset::JAVASCRIPT => array(),
							));

							// Get merge settings for current type
							$merge_settings = Arr::get($merge_settings, $assets['type'], array());

							// Add information for HTML code
							$assets = Arr::merge($assets, array(
								'attributes' => Arr::get($merge_settings, 'attributes'),
								'protocol'   => Arr::get($merge_settings, 'protocol'),
								'index'      => Arr::get($merge_settings, 'index', FALSE),
							));
						}

						// Add HTML code for merged assets
						$html .= $this->html($assets);
					}
					else
					{
						// Assets are not merged, just processed or compressed
						foreach ($assets as $asset)
						{
							// Add HTML code for each asset
							$html .= $this->html($asset);
						}
					}
				}
				else
				{
					// Output original files without compiling
					foreach ($assets as $asset)
					{
						// Use the original filename as output
						$asset['output'] = $asset['remote'];

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
			$extension_index = array_search(Asset::$extensions[$asset['type']], $fileparts);

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

			// Get compressor
			$compressor = Arr::get($this->_config, 'compressor', array());

			if ($compressor = Arr::get($compressor, $asset['type']) AND ! strstr($asset['filename'], '.min.'))
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
						'output'     => $asset['output'],
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

				// Add to compiled assets
				$compiled_assets[] = Arr::merge($asset, array('compiled_content' => $compiled_content));
			}
		}

		if ($this->_merge)
		{
			// Set compiled assets
			$compiled_assets = Arr::merge($asset_to_merge, array('compiled_content' => $compiled_content));
		}

		return $compiled_assets;
	}

	/**
	 * Set the output file
	 *
	 * Sets the output file for each asset based on the configuration.
	 *
	 * @param   array   $asset
	 * @param   bool    $compile
	 * @return  array
	 */
	protected function output_file($asset, $compile)
	{
		if ($this->_merge)
		{
			// Get output directory
			$output_dir = Arr::get($this->_config, 'output_dir', array());

			// Set basic file name
			$file_name = Arr::get($output_dir, $asset['type'], $asset['input_dir']);
		}
		else
		{
			// Set basic file name
			$file_name = $asset['input_dir'];
		}

		if ($this->_merge AND $this->_display_compiled)
		{
			// Get merge settings
			$merge_settings = Arr::get($this->_config, 'merge_settings', array(
				Asset::STYLESHEET => array(),
				Asset::JAVASCRIPT => array(),
			));

			// Get merge settings for current type
			$merge_settings = Arr::get($merge_settings, $asset['type'], array());

			// Get configured file name
			$configured_name = Arr::get($merge_settings, 'file', $this->_instance);

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
			// No merging will be done, add original file name
			$file_name .= $asset['filename'];
		}

		// Add extension to file name
		$file_name .= '.'.$asset['extension'];

		// Add file name to asset
		$asset['output'] = $file_name;

		if ($compile AND $asset['output'] === $asset['remote'] AND ($this->_merge OR count(Arr::get($this->_config, 'compressor', array())) > 0))
		{
			// Don't allow the original asset to be overwritten
			throw new Kohana_Exception('Using these settings the original asset :asset will be overwritten', array(
				':asset' => $asset['remote'],
			));
		}

		return $asset;
	}

	/**
	 * Save asset to file
	 *
	 * Saves the compiled content to a file, or copies the file as it is
	 * if needed.
	 *
	 * @param   array   $asset
	 * @return  void
	 */
	protected function save($asset)
	{
		if (Arr::get($asset, 'compiled_content'))
		{
			// Save compiled file
			file_put_contents($asset['output'], $asset['compiled_content']);
		}
		elseif ( ! $this->_merge AND $asset['output'] !== $asset['remote'])
		{
			// Copy file
			copy($asset['local'], $asset['output']);
		}
	}

	/**
	 * Create the HTML code for a single asset
	 *
	 * @param   array  $asset
	 * @return  mixed
	 */
	protected function html($asset)
	{
		if (Arr::get($this->_config, 'cache_bust'))
		{
			// Add cache busting to file name
			$asset['output'] .= '?v'.time();
		}

		// Set HTML
		$html = HTML::$asset['type']($asset['output'], $asset['attributes'], $asset['protocol'], $asset['index']);

		return $html;
	}

} // End Asset_Core_Local