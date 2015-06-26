<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Properties</h1>
        </div>
    </div>
    <div class="row row-top-buffer">
        <table id="admin_products_list" class="table table-striped table-responsive table-hover table-bordered text-center">
            <thead>
            <tr>
                <th class="col-sm-3">Name</th>
                <th class="col-sm-1">Rate</th>
                <th class="col-sm-5">Short Description</th>
                <th class="col-sm-1">Images</th>
                <th class="col-sm-2">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
                if(!$data->properties->isEmpty())
                {
                    foreach ($data->properties as $property)
                    {
                        $unitHtml = '<tr>' . PHP_EOL;
                        $unitHtml .= '<td>' . PHP_EOL;
                        $unitHtml .= '<a href="/admin/properties/edit/' . $property->pid . '">' . $property->name . '</a>' . PHP_EOL;
                        $unitHtml .= '</td>' . PHP_EOL;
                        $unitHtml .= '<td>' . PHP_EOL;
                        $unitHtml .= $property->getFormattedRate() . PHP_EOL;
                        $unitHtml .= '</td>' . PHP_EOL;
                        $unitHtml .= '<td>' . PHP_EOL;
                        $unitHtml .= $property->short_desc . PHP_EOL;
                        $unitHtml .= '</td>' . PHP_EOL;
                        $unitHtml .= '<td>' . PHP_EOL;
                        $unitHtml .= count($property->images) . PHP_EOL;
                        $unitHtml .= '</td>' . PHP_EOL;
                        $unitHtml .= '<td class="order-action-icons">' . PHP_EOL;
                        $unitHtml .= '<a class="property-edit btn btn-danger" data-toggle="modal" data-href="/admin/properties/' . $property->pid . '" data-target="#delete-confirm">Delete</a>' . PHP_EOL;
                        $unitHtml .= '<a class="property-edit btn btn-info" href="/admin/properties/edit/' . $property->pid . '">Edit </a>' . PHP_EOL;
                        $unitHtml .= '</td>' . PHP_EOL;
                        $unitHtml .= '</tr>' . PHP_EOL;
                        echo $unitHtml;
                    }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="confirmHead">Confirm Delete Property?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure that you want to delete this property?</p>
            </div>
            <div class="modal-footer">
                <form method="POST">
                    <input type="hidden" name="_METHOD" value="DELETE"/>
                    <input type="hidden" name="_CSRF" value="<?php echo $data->csrfToken; ?>"/>
                    <button type="button" id="cancel-btn" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-ok" id="delete-submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>

    /**
     * Pass the form action to the modal window.
     * This is used for updating and deleting properties.
     */
    $('#delete-confirm').on('show.bs.modal', function(e) {
        $(this).find('form').attr('action', $(e.relatedTarget).data('href'));
    });
</script>
</body>
</html>