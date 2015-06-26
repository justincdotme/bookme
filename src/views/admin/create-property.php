<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Create Property</h1>
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
            <ul class="alert alert-danger error-container">

            </ul>
            <form id="property-form" method="POST" action="/admin/properties" >
                <input type="hidden" value="<?php echo $data->csrfToken; ?>" name="_CSRF" class="form-control" >
                <div class="col-sm-12">
                    <div class="form-group col-md-6">
                        <label class="control-label">Property Name</label>
                        <input type="text" value="<?php echo isset($data->_property->name) ? $data->_property->name : null; ?>" name="name" placeholder="Property Name (Required)" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Property Rate</label>
                        <div class="input-group">
                            <div class="input-group-addon">$</div>
                            <input type="text" value="<?php echo isset($data->_property->rate) ? $data->_property->rate : null; ?>" name="rate" placeholder="Nightly Rate" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Short Description</label>
                        <textarea name="short_desc" class="form-control" rows="4"><?php echo isset($data->_property->short_desc) ? $data->_property->short_desc : null; ?></textarea>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label">Long Description</label>
                        <textarea name="long_desc" class="form-control" rows="4"><?php echo isset($data->_property->short_desc) ? $data->_property->short_desc : null; ?></textarea>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row-top-buffer">
                    <div class="col-sm-6">
                        <button class="btn btn-primary col-sm-12" type="submit">Save Changes</button>
                    </div>
                    <div class="col-sm-6">
                        <a href="<?php echo POST_ADD_PROPERTY_URL ?>" class="btn btn-danger col-sm-12" role="button" type="submit">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="/js/vendor/croppic.min.js"></script>
<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>

<script>
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
</script>
<link rel="stylesheet" href="/css/vendor/croppic.css"/>
<script src="/js/validate-property.js"></script>
</body>
</html>