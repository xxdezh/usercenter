<?php

/**
 * ImageMagick Image Driver.
 *
 * $Id: ImageMagick.php 3769 2008-12-15 00:48:56Z zombor $
 *
 * @package    Image
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
Mod::import('system.components.image.Image_Driver');
class Image_ImageMagick_Driver extends Image_Driver {

	// Directory that IM is installed in
	protected $dir = '';

	// Command extension (exe for windows)
	protected $ext = '';

	// Temporary image filename
	protected $tmp_image;
	
	protected $_args = array();

	/**
	 * Attempts to detect the ImageMagick installation directory.
	 *
	 * @throws  Kohana_Exception
	 * @param   array   configuration
	 * @return  void
	 */
	public function __construct($config)
	{
		if (empty($config['directory']))
		{
			// Attempt to locate IM by using "which" (only works for *nix!)
			if ( ! is_file($path = exec('which convert')))
				throw new CException('image imagemagick not_found');

			$config['directory'] = dirname($path);
		}

		// Set the command extension
		$this->ext = (PHP_SHLIB_SUFFIX === 'dll') ? '.exe' : '';

		// Check to make sure the provided path is correct
		if ( ! is_file(realpath($config['directory']).'/convert'.$this->ext))
			throw new CException('image imagemagick not_found');

		// Set the installation directory
		$this->dir = str_replace('\\', '/', realpath($config['directory'])).'/';
	}

	/**
	 * Creates a temporary image and executes the given actions. By creating a
	 * temporary copy of the image before manipulating it, this process is atomic.
	 */
	public function process($image, $actions, $dir, $file, $render = FALSE)
	{
		// We only need the filename
		$image = $image['file'];
		$this->tmp_image = $image;

		// Quality change is done last
		$quality = NULL;
		if (array_key_exists('quality', $actions)) {
			$quality = (int) $actions['quality'];
			unset($actions['quality']);
		}

		// Use 95 for the default quality
		empty($quality) and $quality = 95;

		// All calls to these will need to be escaped, so do it now
		$this->cmd_image = escapeshellarg($image);
		$this->new_image = ($render)? $this->cmd_image : escapeshellarg($dir.$file);

		if ($status = $this->execute($actions))
		{
			// Use convert to change the image into its final version. This is
			// done to allow the file type to change correctly, and to handle
			// the quality conversion in the most effective way possible.
			$this->_args[] = '-quality '.$quality.'%';
			$error = exec(escapeshellcmd($this->dir.'convert'.$this->ext).' '.join(' ', $this->_args).' '.$this->cmd_image.' '.$this->new_image);
			if ($error)
			{
				$this->errors[] = $error;
			}
			else
			{
				// Output the image directly to the browser
				if ($render !== FALSE)
				{
					$contents = file_get_contents($this->tmp_image);
					switch ($image['type'])
					{
						case IMAGETYPE_JPEG:
							header('Content-Type: image/jpeg');
						break;
						case IMAGETYPE_GIF:
							header('Content-Type: image/gif');
						break;
						case IMAGETYPE_PNG:
							header('Content-Type: image/png');
						break;
 					}
					echo $contents;
				}
			}
		}

		$this->_args = array();

		return $status;
	}

	public function crop($prop)
	{
		// Sanitize and normalize the properties into geometry
		$this->sanitize_geometry($prop);

		// Set the IM geometry based on the properties
		$geometry = escapeshellarg($prop['width'].'x'.$prop['height'].'+'.$prop['left'].'+'.$prop['top']);

		$this->_args[] = '-crop '.$geometry;

		return TRUE;
	}

	public function flip($dir)
	{
		// Convert the direction into a IM command
		$this->_args[] = ($dir === Image::HORIZONTAL) ? '-flop' : '-flip';

		return TRUE;
	}

	public function resize($prop)
	{
		switch ($prop['master'])
		{
			case Image::WIDTH:  // Wx
				$dim = escapeshellarg($prop['width'].'x');
			break;
			case Image::HEIGHT: // xH
				$dim = escapeshellarg('x'.$prop['height']);
			break;
			case Image::AUTO:   // WxH
				$dim = escapeshellarg($prop['width'].'x'.$prop['height']);
			break;
			case Image::NONE:   // WxH!
				$dim = escapeshellarg($prop['width'].'x'.$prop['height'].'!');
			break;
		}

		$this->_args[] = '-resize '.$dim;

		return TRUE;
	}

	public function rotate($amt)
	{
		$this->_args[] = '-rotate '.escapeshellarg($amt).' -background transparent';

		return TRUE;
	}

	public function sharpen($amount)
	{
		// Set the sigma, radius, and amount. The amount formula allows a nice
		// spread between 1 and 100 without pixelizing the image badly.
		$sigma  = 0.5;
		$radius = $sigma * 2;
		$amount = round(($amount / 80) * 3.14, 2);

		// Convert the amount to an IM command
		$sharpen = escapeshellarg($radius.'x'.$sigma.'+'.$amount.'+0');

		$this->_args[] = '-unsharp '.$sharpen;

		return TRUE;
	}

	public function grayscale($unused) {
		$this->_args[] = '-colorspace Gray';
		return true;
	 }
	 
	 public function emboss($radius) {
		 $this->args[] = '-emboss '.$radius;
	 }

	public function negate($unused) {
		 $this->args[] = '-negate ';
	 }

	protected function properties()
	{
		return array_slice(getimagesize($this->tmp_image), 0, 2, FALSE);
	}

} // End Image ImageMagick Driver