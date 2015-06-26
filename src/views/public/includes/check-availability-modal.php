<div class="modal fade" id="check-availability-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Check Availability</h4>
            </div>
            <div class="modal-body">
                <form id="checkAvailability" action="/property/check" method="POST">
                    <input type="hidden" name="property" value="<?php echo $data->property->pid; ?>" >
                    <div class="row">
                        <div class="col-sm-12 alert alert-danger error-container">
                            <h2>Errors Have Occurred</h2>
                            <ul class="errors">

                            </ul>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label class="control-label">Check In Date</label>
                                <input type="text" value="" name="check_in" placeholder="Check In" class="form-control required" required readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-sm-12">
                                <label class="control-label">Check Out Date</label>
                                <input type="text" value="" name="check_out" placeholder="Check Out" class="form-control required" required readonly>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Check</button>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>