<?php

/**
 * openDeal - Opensource Deals Platform
 *
 * @author      Craig Smith <vxdhost@gmail.com>
 * @copyright   2014 Craig Smith
 * @link        https://github.com/openDeal/openDeal
 * @license     https://raw.githubusercontent.com/openDeal/openDeal/master/LICENSE
 * @since       1.0.0
 * @package     Core
 * GPLV3 Licence
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class Captcha {

    /**
     * Holds the current code
     * @var string 
     */
    protected $code;

    /**
     * Width of the Captcha
     * @var int 
     */
    protected $width = 150;

    /**
     * Height of the Captcha
     * @var int 
     */
    protected $height = 50;

    /**
     * Constructor
     */
    function __construct() {
        $this->code = substr(sha1(mt_rand()), 17, 6);
    }

    /**
     * Returns the current code
     * @return string
     */
    function getCode() {
        return $this->code;
    }

    /**
     * renders the current captcha image
     */
    function showImage() {
        $image = imagecreatetruecolor($this->width, $this->height);

        $width = imagesx($image);
        $height = imagesy($image);

        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 250, 250, 250);
        $red = imagecolorallocatealpha($image, 225, 225, 225, 75);
        $green = imagecolorallocatealpha($image, 150, 150, 150, 75);
        $blue = imagecolorallocatealpha($image, 200, 200, 200, 75);

        imagefilledrectangle($image, 0, 0, $width, $height, $white);

        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red);


        $points = array(
            rand(0, 150), rand(0, 50), // Point 1 (x, y)
            rand(0, 150), rand(0, 50), // Point 2 (x, y)
            rand(0, 150), rand(0, 50), // Point 3 (x, y)
            rand(0, 150), rand(0, 50), // Point 4 (x, y)
            rand(0, 150), rand(0, 50), // Point 5 (x, y)
            rand(0, 150), rand(0, 50), // Point 6 (x, y)
        );

        imagefilledpolygon($image, $points, 4, $blue);

        imagefilledpolygon($image, array_reverse($points), 6, $green);

        imagefilledrectangle($image, 0, 0, $width, 0, $black);
        imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black);
        imagefilledrectangle($image, 0, 0, 0, $height - 1, $black);
        imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black);

        for ($i = 0; $i < 50; $i++) {
            //imagefilledrectangle($im, $i + $i2, 5, $i + $i3, 70, $black);
            imagesetthickness($image, rand(1, 5));
            imagearc(
                    $image, rand(1, 150), // x-coordinate of the center.
                    rand(1, 50), // y-coordinate of the center.
                    rand(1, 150), // The arc width.
                    rand(1, 50), // The arc height.
                    rand(1, 150), // The arc start angle, in degrees.
                    rand(1, 50), // The arc end angle, in degrees.
                    (rand(0, 1) ? $green : $red) // A color identifier.
            );
        }


        if (function_exists("imagettftext")) {
            $font = DIR_ROOT . 'view/fonts/Duality.ttf';
            imagettftext($image, 24, rand(-5, 5), intval(($width - (strlen($this->code) * 10)) / 2), intval(($height + 10) / 2), $black, $font, $this->code);
        } else {
            imagestring($image, 5, intval(($width - (strlen($this->code) * 9)) / 2), intval(($height - 15) / 2), $this->code, $black);
        }
        header('Content-type: image/jpeg');

        imagejpeg($image);

        imagedestroy($image);
    }

}
