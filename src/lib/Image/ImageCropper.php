<?php namespace bookMe\lib\Image;

use Imagick;

/**
 * Class ImageCropper
 *
 * This class contains the method use to crop an image.
 *
 * The ImageCropper class contains 1 public method, crop.
 * The crop method accepts 1 parameter: $cropParams (array).
 * The crop method will crop an image using the Imagick class.
 * The crop class will delete the temporary image and write the new one to disk.
 *
 *
 * PHP Version 5.6
 *
 * License: Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package bookMe
 * @author Justin Christenson <info@justinc.me>
 * @version 1.0.0
 * @license http://opensource.org/licenses/mit-license.php
 * @link http://bookme.justinc.me
 *
 */

class ImageCropper implements ImageCropperInterface {

    /**
     * Crop the uploaded image.
     * This implementation uses the Imagick PHP class.
     *
     * @param $cropParams
     * @return bool
     */
    public function crop(array $cropParams)
    {
        $imagick = new Imagick($_SERVER['DOCUMENT_ROOT'] . $cropParams['imgUrl']);
        //Scale the image down
        $imagick->thumbnailImage(
            $cropParams['imgW'],
            $cropParams['imgH']
        );
        //Apply cropping
        $imagick->cropImage(
            $cropParams['cropW'],
            $cropParams['cropH'],
            $cropParams['imgX1'],
            $cropParams['imgY1']
        );
        //Write to disk
        if($imagick->writeImage($_SERVER['DOCUMENT_ROOT']
            . $cropParams['img_final_dir']
            . $cropParams['image_out']))
        {
            //Delete the temp image.
            if(unlink($_SERVER['DOCUMENT_ROOT'] . $cropParams['imgUrl']))
            {
                return true;
            }
            return false;
        }
    }
}