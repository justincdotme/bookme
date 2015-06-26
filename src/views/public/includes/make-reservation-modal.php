<div class="modal fade" id="make-reservation-modal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Make a Reservation</h4>
            </div>
            <div class="modal-body">
                <form id="reserve" action="/property/reserve" method="POST">
                    <input type="hidden" name="pid" value="<?php echo $data->property->pid; ?>" >
                    <div class="row">
                        <div class="col-sm-12 alert alert-danger error-container">
                            <h2>Errors Have Occurred</h2>
                            <ul class="errors">

                            </ul>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">First Name</label>
                            <input type="text" placeholder="First Name" name="first_name" class="form-control required" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Last Name</label>
                            <input type="text" placeholder="Last Name" value="" name="last_name" class="form-control required" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Address Line 1</label>
                            <input type="text" placeholder="Address Line 1" value="" name="addr_street_1" class="form-control required" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Address Line 2</label>
                            <input type="text" placeholder="Address Line 2" value="" name="addr_street_2" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">City</label>
                            <input type="text" placeholder="City" value="" name="addr_city" class="form-control required" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">State</label>
                            <?php include 'state-dropdown-list.php'; ?>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="control-label">Zip</label>
                            <input type="text" maxlength="5" placeholder="Zip" size="5" value="" name="addr_zip" class="form-control required" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Home Phone</label>
                            <input type="text" maxlength="12" placeholder="Home Phone" name="home_phone" class="form-control phone required" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="control-label">Email Address</label>
                            <input type="text" placeholder="email@address.com" name="email_address" class="form-control email required" required>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label class="control-label">Check In Date</label>
                            <input type="text" value="" name="check_in" placeholder="Check In" class="form-control required" required readonly>
                        </div>
                        <div class="form-group col-md-5">
                            <label class="control-label">Check Out Date</label>
                            <input type="text" value="" name="check_out" placeholder="Check Out" class="form-control required" required readonly>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="control-label">Guests</label>
                            <input type="number" value="1" min="1" name="guests" class="form-control required" required>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2">
                            <div class="col-sm-6">
                                <button type="button" class=" btn btn-danger col-sm-12" data-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-primary col-sm-12">Reserve</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
