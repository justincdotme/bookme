<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Edit Property</h1>
            <?php
            if(isset($data->errors))
            {
                $htmlOut = '';
                $htmlOut .= '<div class="alert alert-danger">' . "\r\n";
                $htmlOut .= '<h1>Errors have occurred!</h1>';
                $htmlOut .= '<ul>' . "\r\n";
                foreach($data->errors as $field => $error)
                {
                    foreach($error as $msg)
                    {
                        $htmlOut .= '<li>' . "\r\n";
                        $htmlOut .= $msg;
                        $htmlOut .= '</li>' . "\r\n";
                    }
                }
                $htmlOut .= '</ul>' . "\r\n";
                $htmlOut .= '</div>' . "\r\n";
                echo $htmlOut;
            }
            ?>
            <form id="edit-property-form" method="POST" action="/admin/properties/<?php echo $data->property->pid; ?>" enctype="multipart/form-data">
                <input type="hidden" name="_METHOD" value="PUT"/>
                <input type="hidden" value="<?php echo $data->csrfToken; ?>" name="_CSRF" class="form-control" >
                <div class="col-sm-6">
                    <div class="form-group col-md-12">
                        <label class="control-label">Property Name</label>
                        <input type="text" value="<?php echo isset($data->property->name) ? $data->property->name : null; ?>" name="name" class="form-control" >
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">Property Rate</label>
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            <input type="text" value="<?php echo isset($data->property->rate) ? $data->property->rate : null; ?>" name="rate" class="form-control" >
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="row">
                            <div class="col-sm-12">
                                <label id="imgUploadLabel">Upload Images</label>
                                <div aria-labelledby="#imgUploadLabel" id="propImgUpload"></div>
                            </div>
                        </div>
                        <div id="property-image-row" class="row">
                            <div class="property-gallery-overlay"></div>
                            <?php
                            if(!$data->property->images->isEmpty())
                            {
                                foreach($data->property->images as $image)
                                {
                                    $html = '<div class="col-xs-6 col-md-4 row-top-buffer">';
                                    $html .= '<a class="thumbnail">';
                                    $html .= "<img src=\"$image->image_full_path\" />";
                                    $html .= '</a>';
                                    $html .= '<a class="btn btn-danger pull-right" data-toggle="modal" data-href="/admin/properties/images/' . $image->img_id . '" data-target="#delete-image-confirm">Delete</a>';
                                    $html .= '</div>';
                                    echo $html;
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group col-md-12">
                        <label class="control-label">Short Description</label>
                        <textarea name="short_desc" class="form-control" rows="4"><?php echo isset($data->property->short_desc) ? $data->property->short_desc : null; ?></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">Long Description</label>
                        <textarea name="long_desc" class="form-control" rows="4"><?php echo isset($data->property->long_desc) ? $data->property->long_desc : null; ?></textarea>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="form-group col-md-12 row-top-buffer">
                    <button class="btn btn-primary col-md-12" type="submit">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include 'includes/add-image-modal.php';
include 'includes/delete-image-confirmation-modal.php';
include 'includes/general-error-modal.php';
include 'includes/footer.php';
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="/js/vendor/croppic.min.js"></script>
<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
<script>
    var editProperty = window.editProperty || {};

    /**
     * Initialize the TinyMCE plugin.
     * http://www.tinymce.com
     */
    tinymce.init({
        selector:'textarea',
        plugins: [
            "link"
        ],
        menubar : false,
        height: 250,
        toolbar: "undo redo | styleselect | bold italic | alignleft aligncenter alignright | bullist numlist | link"
    });

    /**
     * Initialize the CropPic plugin.
     * http://www.croppic.net
     */
    editProperty.imgParams = {
        cropData: {
            "pid": <?php echo $data->property->pid ?>,
            "_CSRF" : $('input[name="_CSRF"]').val()
        },
        onAfterImgCrop: function()
        {
            $('#add-image-modal').modal();
        },
        onError: function()
        {
            editProperty.showError();
        },
        doubleZoomControls: false,
        rotateControls: false,
        uploadUrl: '/admin/properties/images/upload',
        cropUrl: '/admin/properties/images/crop',
        imgEyecandy: false,
        loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> '
    };

    //Initialize the CropPic plugin with the above params.
    editProperty.imgUpload = new Croppic('propImgUpload', editProperty.imgParams);

    /**
     * Hide the success modal if the error modal displays.
     * CropPic doesn't offer an onSuccess method so we compensate here.
     *
     */
    editProperty.showError = function()
    {
        var errorModal = $("#gen-error");
        errorModal.modal();
        errorModal.on('hidden.bs.modal', function (e) {
            $('#add-image-modal').modal('hide');
        })
    };

    /**
     * Reset the uploader on error.
     */
    $('#gen-error').on('hidden.bs.modal', function(e)
    {
        //Reset the uploader
        editProperty.imgUpload.destroy();
        editProperty.imgUpload = new Croppic('propImgUpload', editProperty.imgParams);
    });

    /**
     * Reset the uploader and fetch latest images after a successful upload.
     */
    $('#add-image-modal').on('hidden.bs.modal', function(e)
    {
        //Reset the uploader
        editProperty.imgUpload.destroy();
        editProperty.imgUpload = new Croppic('propImgUpload', editProperty.imgParams);
        $('.property-gallery-overlay').fadeIn();
        editProperty.getImages();
    });

    /**
     * Submit the delete image form via ajax.
     */
    $('form#delete-image-form').submit(function(e)
    {
        var url = $(this).attr('action');
        var data = $(this).serialize();
        $.post(url, data, function(d)
        {
            //Refresh the images
            $('.property-gallery-overlay').fadeIn();
            editProperty.getImages();
            //The modal method does not work on the submit event
            //So we click the cancel button to close the modal.
            $('#cancel-btn').click();
        });
        e.preventDefault();
    });

    /**
     * Get the latest set of images.
     */
    editProperty.getImages = function()
    {
        $.ajax({
            url: '/admin/properties/edit/' + <?php echo $data->property->pid ?>,
            dataType: 'html',
            success: function(d) {
                //Update the CSRF token
                var newToken = $(d).find('input[name="_CSRF"]').val();
                //CSRF check is done on upload but Croppic only sends cropData in second POST request so CSRF token is appended to image upload form here.
                //This token is for the next upload.
                $('input[name="_CSRF"]').val(newToken)
                $('form.propImgUpload_imgUploadForm').append('<input type="hidden" name="_CSRF" value="' + newToken + '">');
                //Add the new CSRF token to the POST request.
                editProperty.imgParams.cropData._CSRF = newToken;
                var imageDiv = $(d).find('#property-image-row');
                $('#property-image-row').html(imageDiv).find('.property-gallery-overlay').show().fadeOut(500);
            }
        });
    };

    /**
     * Pass product id to image delete modal.
     *
     */
    $('#delete-image-confirm').on('show.bs.modal', function(e) {
        $(this).find('form').attr('action', $(e.relatedTarget).data('href'));
    });

    //CSRF check is done on upload but Croppic only sends cropData in second POST request (the crop) so CSRF token is appended to image upload form here.
    $('form.propImgUpload_imgUploadForm').append('<input type="hidden" name="_CSRF" value="<?php echo $data->csrfToken; ?>">');

</script>
<link rel="stylesheet" href="/css/vendor/croppic.css"/>
</body>
</html>