<?php
namespace X2board\Includes\Classes;
/* Copyright (C) XEHub <https://www.xehub.io> */
/* WP port by singleview.co.kr */

/**
 * Contains methods for accessing file system
 *
 * @author XEHub (developers@xpressengine.com)
 */
if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if (!class_exists('\\X2board\\Includes\\Classes\\FileHandler')) {

	class FileHandler {

		/**
		 * Convert size in string into numeric value
		 *
		 * @see self::filesize()
		 * @param $val Size in string (ex., 10, 10K, 10M, 10G )
		 * @return int converted size
		 */
		public static function returnBytes($val) {
			$unit = strtoupper(substr($val, -1));
			$val = (float)$val;
			switch ($unit) {
				case 'G': $val *= 1024;
				case 'M': $val *= 1024;
				case 'K': $val *= 1024;
			}
			return round($val);
		}

		/**
		 * Makes file size byte into KB, MB according to the size
		 *
		 * @see self::returnBytes()
		 * @param int $size Number of the size
		 * @return string File size string
		 */
		public static function filesize($size) {
			if(!$size) {
				return '0Byte';
			}
			if($size === 1)	{
				return '1Byte';
			}
			if($size < 1024) {
				return $size . 'Bytes';
			}
			if($size >= 1024 && $size < 1024 * 1024) {
				return sprintf("%0.1fKB", $size / 1024);
			}
			return sprintf("%0.2fMB", $size / (1024 * 1024));
		}

		/**
		 * Return list of the files in the path
		 *
		 * The array does not contain files, such as '.', '..', and files starting with '.'
		 *
		 * @param string $path Path of target directory
		 * @param string $filter If specified, return only files matching with the filter
		 * @param bool $to_lower If TRUE, file names will be changed into lower case.
		 * @param bool $concat_prefix If TRUE, return file name as absolute path
		 * @return string[] Array of the filenames in the path
		 */
		public static function readDir($path, $filter = '', $to_lower = FALSE, $concat_prefix = FALSE) {
			$path = self::_getRealPath($path);
			$output = array();

			if(substr($path, -1) != '/') {
				$path .= '/';
			}

			if(!is_dir($path)) {
				return $output;
			}

			$files = scandir($path);
			foreach($files as $file) {
				if($file[0] == '.' || ($filter && !preg_match($filter, $file))) {
					continue;
				}

				if($to_lower) {
					$file = strtolower($file);
				}

				if($filter) {
					$file = preg_replace($filter, '$1', $file);
				}

				if($concat_prefix) {
					$file = sprintf('%s%s', str_replace(X2B_PATH, '', $path), $file);
				}
				$output[] = str_replace(array('/\\', '//'), '/', $file);
			}
			return $output;
		}

		/**
		 * Changes path of target file, directory into absolute path
		 * function getRealPath($source)
		 * @param string $source path to change into absolute path
		 * @return string Absolute path
		 */
		private static function _getRealPath($source) {
			if(strlen($source) >= 2 && substr_compare($source, './', 0, 2) === 0) {
				return _X2B_PATH_ . substr($source, 2);
			}
			return $source;
		}

		/**
		 * Check file exists.
		 *
		 * @param string $filename Target file name
		 * @return bool Returns FALSE if the file does not exists, or Returns full path file(string).
		 */
		public static function exists($s_filename) {
			$s_filename = self::_getRealPath($s_filename);
			return file_exists($s_filename) ? $s_filename : FALSE;
		}

		/**
		 * Remove a directory only if it is empty
		 * removeBlankDir($path)
		 * @param string $path Path of the target directory
		 * @return void
		 */
		public static function remove_blank_dir($path) {
			if(($path = self::is_x2b_dir($path)) === FALSE) {
				return;
			}

			$files = array_diff(scandir($path), array('..', '.'));
			if(count($files) < 1) {
				rmdir($path);
				return;
			}

			foreach($files as $file) {
				if(($target = self::is_x2b_dir($path . DIRECTORY_SEPARATOR . $file)) === FALSE){
					continue;
				}
				self::remove_blank_dir($target);
			}
		}

		/**
		 * Check it is dir
		 * isDir($path)
		 * @param string $dir Target dir path
		 * @return bool Returns FALSE if the dir is not dir, or Returns full path of dir(string).
		 */
		public static function is_x2b_dir($path) {
			$path = self::_getRealPath($path);
			return is_dir($path) ? $path : FALSE;
		}

		/**
		 * Check available memory to load image file
		 * checkMemoryLoadImage(&$imageInfo)
		 * @param array $imageInfo Image info retrieved by getimagesize function
		 * @return bool TRUE: it's ok, FALSE: otherwise
		 */
		private static function _check_memory_load_image(&$imageInfo) {
			$memoryLimit = self::returnBytes(ini_get('memory_limit'));
			if($memoryLimit == -1) {
				return true;
			}

			$K64 = 65536;
			$TWEAKFACTOR = 2.0;
			$channels = isset($imageInfo['channels']) ? $imageInfo['channels'] : 6;
			// if(!$channels) {
			// 	$channels = 6; //for png
			// }

			$memoryNeeded = round(($imageInfo[0] * $imageInfo[1] * $imageInfo['bits'] * $channels / 8 + $K64 ) * $TWEAKFACTOR);
			$availableMemory = self::returnBytes(ini_get('memory_limit')) - memory_get_usage();
			if($availableMemory < $memoryNeeded) {
				return FALSE;
			}
			return TRUE;
		}

		/**
		 * Moves an image file (resizing is possible)
		 * createImageFile($source_file, $target_file, $resize_width = 0, $resize_height = 0, $target_type = '', $thumbnail_type = 'crop', $thumbnail_transparent = FALSE)
		 * @param string $source_file Path of the source file
		 * @param string $target_file Path of the target file
		 * @param int $resize_width Width to resize
		 * @param int $resize_height Height to resize
		 * @param string $target_type If $target_type is set (gif, jpg, png, bmp), result image will be saved as target type
		 * @param string $thumbnail_type Thumbnail type(crop, ratio)
		 * @param bool $thumbnail_transparent If $target_type is png, set background set transparent color
		 * @return bool TRUE: success, FALSE: failed
		 */
		public static function create_image_file($source_file, $target_file, $resize_width = 0, $resize_height = 0, $target_type = '', $thumbnail_type = 'crop', $thumbnail_transparent = FALSE) {
			// check params
			if (($source_file = self::exists($source_file)) === FALSE) {
				return;
			}

			$target_file = self::_getRealPath($target_file);
			if(!$resize_width) {
				$resize_width = 100;
			}

			if(!$resize_height) {
				$resize_height = $resize_width;
			}

			// retrieve source image's information
			$imageInfo = getimagesize($source_file);
			if(!self::_check_memory_load_image($imageInfo)) {
				return FALSE;
			}

			list($width, $height, $type, $attrs) = $imageInfo;
			if($width < 1 || $height < 1) {
				return;
			}

			switch($type) {
				case '1' :
					$type = 'gif';
					break;
				case '2' :
					$type = 'jpg';
					break;
				case '3' :
					$type = 'png';
					break;
				case '6' :
					$type = 'bmp';
					break;
				default :
					return;
			}

			if(!$target_type) {
				$target_type = $type;
			}
			$target_type = strtolower($target_type);

			// if original image is larger than specified size to resize, calculate the ratio
			$width_per = ($resize_width > 0 && $width >= $resize_width) ? $resize_width / $width : 1;
			$height_per = ($resize_height > 0 && $height >= $resize_height) ? $resize_height / $height : 1;

			$per = NULL;
			if($thumbnail_type == 'ratio') {
				$per = ($width_per > $height_per) ? $height_per : $width_per;
				$resize_width = $width * $per;
				$resize_height = $height * $per;
			}
			else {
				$per = ($width_per < $height_per) ? $height_per : $width_per;
			}

			// create temporary image with target size
			$thumb = NULL;
			if(function_exists('imagecreateTRUEcolor')) {
				$thumb = imagecreateTRUEcolor($resize_width, $resize_height);
			}
			else if(function_exists('imagecreate')) {
				$thumb = imagecreate($resize_width, $resize_height);
			}

			if(!$thumb) {
				return FALSE;
			}

			if(function_exists('imagecolorallocatealpha') && $target_type == 'png' && $thumbnail_transparent) {
				imagefill($thumb, 0, 0, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
				
				if(function_exists('imagesavealpha')) {
					imagesavealpha($thumb, TRUE);
				}

				if(function_exists('imagealphablending')) {
					imagealphablending($thumb, TRUE);
				}
			}
			else {
				imagefilledrectangle($thumb, 0, 0, $resize_width - 1, $resize_height - 1, imagecolorallocate($thumb, 255, 255, 255));
			}

			// create temporary image having original type
			$source = NULL;
			switch($type) {
				case 'gif' :
					if(function_exists('imagecreatefromgif')) {
						$source = @imagecreatefromgif($source_file);
					}
					break;
				case 'jpeg' :
				case 'jpg' :
					if(function_exists('imagecreatefromjpeg')) {
						$source = @imagecreatefromjpeg($source_file);
					}
					break;
				case 'png' :
					if(function_exists('imagecreatefrompng')) {
						$source = @imagecreatefrompng($source_file);
					}
					break;
				case 'wbmp' :
				case 'bmp' :
					if(function_exists('imagecreatefromwbmp')) {
						$source = @imagecreatefromwbmp($source_file);
					}
					break;
			}

			if(!$source) {
				imagedestroy($thumb);
				return FALSE;
			}

			// resize original image and put it into temporary image
			$new_width = (int) ($width * $per);
			$new_height = (int) ($height * $per);

			$x = $y = 0;
			if($thumbnail_type == 'crop') {
				$x = (int) ($resize_width / 2 - $new_width / 2);
				$y = (int) ($resize_height / 2 - $new_height / 2);
			}

			if(function_exists('imagecopyresampled')) {
				imagecopyresampled($thumb, $source, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
			}
			else {
				imagecopyresized($thumb, $source, $x, $y, 0, 0, $new_width, $new_height, $width, $height);
			}
			
			// create directory
			if( !file_exists( dirname($target_file) ) ) {
				wp_mkdir_p( dirname($target_file) );
			}

			// write into the file
			$output = NULL;
			switch($target_type) {
				case 'gif' :
					if(function_exists('imagegif')) {
						$output = imagegif($thumb, $target_file);
					}
					break;
				case 'jpeg' :
				case 'jpg' :
					if(function_exists('imagejpeg')) {
						$output = imagejpeg($thumb, $target_file, 100);
					}
					break;
				case 'png' :
					if(function_exists('imagepng')) {
						$output = imagepng($thumb, $target_file, 9);
					}
					break;
				case 'wbmp' :
				case 'bmp' :
					if(function_exists('imagewbmp')) {
						$output = imagewbmp($thumb, $target_file, 100);
					}
					break;
			}

			imagedestroy($thumb);
			imagedestroy($source);

			if(!$output) {
				return FALSE;
			}
			@chmod($target_file, 0644);
			return TRUE;
		}
	}
}
/* End of file FileHandler.class.php */