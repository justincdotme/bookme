<?php namespace bookMe\lib\Image;

use Imagick;

/**
 * Class ImageUpload
 *
 * This class contains the methods used check if a property is available.
 *
 * The ImageUpload class contains 1 method, upload.
 * The ImageUpload class has 1 dependency, an instance of the Imagick class.
 *
 * The upload method accepts 1 parameter: $file (array).
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
 * @link http://bookme.demos.justinc.me
 *
 */

class ImageUpload implements ImageUploadInterface {

    protected $_imagick;
    protected $_allowedTypes;

    public function __construct(Imagick $imagick)
    {
        $this->_imagick = $imagick;
        $this->_allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    }

    /**
     * Upload an image.
     *
     * @param $file
     * @return array
     */
    public function upload(array $file)
    {
        if(!$file)
        {
            $status = [
                'status'  => 'error',
                'message' => GENERIC_UPLOAD_ERROR_MESSAGE
            ];
            return $status;
        }

        $tempName = $file['tmp_name'];

        if(is_null($tempName))
        {
            $status = [
                'status'  => 'error',
                'message' => GENERIC_UPLOAD_ERROR_MESSAGE
            ];
            return $status;
        }
        $imageInfo = getimagesize($tempName);
        if(!$imageInfo)
        {
            $status = [
                'status'  => 'error',
                'message' => 'Only images are allowed'
            ];
            return $status;
        }

        $fileType = image_type_to_mime_type(exif_imagetype($tempName));

        if(!in_array($fileType, $this->_allowedTypes))
        {
            $status = [
                'status'  => 'error',
                'message' => 'File type not allowed'
            ];
            return $status;
        }

        $fileName = htmlentities($file['name']);
        $height = $this->_imagick->getImageHeight();
        $width = $this->_imagick->getImageWidth();
        $uploadPath = $_SERVER['DOCUMENT_ROOT'] . PROPERTY_IMG_TMP_DIR;

        if(!move_uploaded_file($tempName, $uploadPath . "/$fileName"))
        {
            $status = [
                'status' => 'error',
                'message' => 'Can\'t move file'
            ];
            return $status;
        }

        $status = [
            'status' => 'success',
            'url' => PROPERTY_IMG_TMP_DIR . '/' . $fileName,
            'width' => $width,
            'height' => $height,
            'token' => $_SESSION['csrf_token']
        ];
        return $status;
    }
}