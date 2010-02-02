<?php
session_start();

/*
* File: CaptchaSecurityImages.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 03/08/06
* Updated: 07/02/07
* Requirements: PHP 4/5 with GD and FreeType libraries
* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* File: captcha.php
* Modified By: Thomas Hunter
* Copyright: 2006 Simon Jarvis
* Updated: 1/24/2010
* Requirements: PHP 5 with GD and FreeType libraries
* Updates mainly include random letter rotations, random color
* changes for letters and noise, and working with each character
* instead of an entire string.
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/

$width = isset($_GET['width']) ? $_GET['width'] : '150';
$height = isset($_GET['height']) ? $_GET['height'] : '40';
$characters = rand(6,8);

if (isset($_SERVER['WINDIR'])) {
	$font = './includes/monofont.ttf';
} else {
	putenv('GDFONTPATH=' . realpath('./includes/'));
	$font = 'monofont';
}

$captcha = new CaptchaSecurityImages($width, $height, $font, $characters);

class CaptchaSecurityImages {
	function __construct($width='150', $height='40', $font='monofont', $characters='6') {
		/* font size will be 75% of the image height */
		$font_size = $height * 0.6;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colors */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$line_color = imagecolorallocate($image, rand(180,255), rand(180,255), rand(180,255));
		$noise_color = imagecolorallocate($image, rand(180,255), rand(180,255), rand(180,255));
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) {
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $line_color);
		}
		/* create text */
		$code = '';
		$spacing = $width / ($characters + 1);
		for ($i = 1; $i <= $characters; $i++) {
			$text_color = imagecolorallocate($image, rand(0,127), rand(0,127), rand(0,127));
			$character = $this->generateCharacter();
			$code .= $character;
			$angle = rand(-30, 30);
			$y = ($height - $textbox[5])/1.3;
			$position = $spacing * $i * .9;
			imagettftext($image, $font_size, $angle, $position, $y, $text_color, $font, $character) or die('Error in imagettftext function');
		}
		header('Content-Type: image/png');
		imagepng($image);
		imagedestroy($image);
		$_SESSION['security_code'] = $code;
	}

	function generateCharacter() {
		$possible = '23456789bcdfghjkmnpqrstvwxyz';
		return substr($possible, mt_rand(0, strlen($possible)-1), 1);
	}
}