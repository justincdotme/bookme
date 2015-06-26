<div class="modal fade" id="delete-image-confirm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="confirmHead">Confirm Delete Image?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure that you want to delete this image?</p>
            </div>
            <div class="modal-footer">
                <form id="delete-image-form" method="POST">
                    <input type="hidden" name="_METHOD" value="DELETE"/>
                    <input type="hidden" name="_CSRF" value="<?php echo $data->csrfToken; ?>"/>
                    <button type="button" id="cancel-btn" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="delete-submit" class="btn btn-danger btn-ok">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>