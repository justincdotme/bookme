<?php namespace bookMe\Controller;

use bookMe\lib\Csrf\Csrf;
use bookMe\lib\Image\ImageCropper;
use bookMe\lib\Image\ImageUpload;
use bookMe\lib\Http\Response;
use bookMe\Model\PropertyImage;
use Exception;
use Imagick;
use SplFileInfo;

class PropertyImageController extends Controller
{
    protected $_propertyImage;
    protected $_cropper;
    protected $_tmpPath;
    protected $_fileName;
    protected $_width;
    protected $_height;
    protected $_imageOut;
    protected $_pid;
    protected $_response;

    public function __construct()
    {
        parent::__construct();
        $this->_propertyImage = new PropertyImage();
        $this->_cropper = new ImageCropper($this->_request);
        $this->_response = new Response();
    }

    /**
     * Upload a new property image.
     *
     * @return string
     */
    public function upload()
    {
        if(!Csrf::checkToken($this->_request->getInput('_CSRF')))
        {
            $response = [
                'status' => 'error',
                'message' => 'csrf'
            ];
            return $this->_response->returnJson($response);
        }

        $file = $this->_request->getUploadedFile('img');
        $uploader = new ImageUpload(new Imagick($file['tmp_name']));
        $uploadResult = $uploader->upload($file);

        return $this->_response->returnJson($uploadResult);
    }

    /**
     * Save an image model.
     *
     * @return string
     */
    public function store()
    {
        if(!Csrf::checkToken($this->_request->getInput('_CSRF')))
        {
            $response = [
                'status' => 'error',
                'message' => 'csrf'
            ];
            return $this->_response->returnJson($response);
        }
        if($this->crop())
        {
            try {
                $this->_propertyImage->pid = $this->_pid;
                $this->_propertyImage->image_full_path = $this->_imageOut;
                $this->_propertyImage->save();
            }catch(Exception $e) {
                $response = [
                    'status' => 'error',
                    'message' => GENERIC_UPLOAD_ERROR_MESSAGE
                ];
                return $this->_response->returnJson($response);
            }
            $response = [
                'status' => 'success',
                'url' => $this->_imageOut
            ];
            return $this->_response->returnJson($response);
        }
        $response = [
            'status' => 'error',
            'message' => GENERIC_UPLOAD_ERROR_MESSAGE
        ];
        return $this->_response->returnJson($response);
    }

    /**
     * Crop a property image.
     *
     */
    public function crop()
    {
        $this->_pid = $this->_request->getInput('pid');
        $imgUrl = $this->_request->getInput('imgUrl');
        $imgInfo = new SplFileInfo($imgUrl);
        $cropParams = $this->_request->getAllPostInput();
        $cropParams['img_final_dir'] = '/images/properties/';
        $cropParams['image_out'] = $this->_pid . '-' . uniqid() . '.' . $imgInfo->getExtension();

        $this->_imageOut = $cropParams['img_final_dir'] . $cropParams['image_out'];

        if($this->_cropper->crop($cropParams))
        {
            return true;
        }
        return false;
    }

    /**
     * Delete a property.
     *
     * @param $id
     * @return string
     */
    public function destroy($id)
    {
        if(!Csrf::checkToken($this->_request->getInput('_CSRF')))
        {
            $response = [
                'status' => 'error',
                'message' => 'csrf'
            ];
            return $this->_response->returnJson($response);
        }
        try {
            $this->_propertyImage = $this->_propertyImage->findOrFail($id);
            $filePath = $_SERVER['DOCUMENT_ROOT'] . $this->_propertyImage->image_full_path;
            unlink($filePath);
            $this->_propertyImage->delete();
        } catch(Exception $e) {
            $response = [
                'status' => 'error',
                'message' => GENERIC_UPLOAD_ERROR_MESSAGE
            ];
            return $this->_response->returnJson($response);
        }
        $response = [
            'status' => 'success'
        ];
        return $this->_response->returnJson($response);
    }
}
