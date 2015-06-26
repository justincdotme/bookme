<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="thumbnail" id="detail-primary-image">
                                <img class="inline" src="<?php echo !$data->property->images->isEmpty() ? $data->property->images->first()->image_full_path : 'http://placehold.it/350x350'; ?>" />
                            </div>
                            <div class="row">
                                <div class="col-sm-12 detail-thumbnails">
                                    <?php
                                    if(!$data->property->images->isEmpty())
                                    {
                                        foreach($data->property->images as $image)
                                        {
                                            $imgHtml = '<a class="col-sm-4 thumbnail">';
                                            $imgHtml .= "<img src=\"$image->image_full_path\" />";
                                            $imgHtml .= '</a>';
                                            echo $imgHtml;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <h1 class="inline"><?php echo isset($data->property->name) ? $data->property->name : 'Unnamed Property'; ?></h1>
                            <h2 class="inline pull-right"><a class="property-edit btn btn-info" data-backdrop="static" data-toggle="modal" data-target="#make-reservation-modal">Reserve Now!</a></h2>
                            <h2 class="inline pull-right"><a class="property-edit btn btn-primary" data-backdrop="static" data-toggle="modal" data-target="#check-availability-modal">Check Availability</a></h2>
                            <h3>Nightly Rate - <?php echo $data->property->getFormattedRate(); ?></h3>
                            <div class="clearfix"></div>
                            <?php echo $data->property->long_desc; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    include 'includes/footer.php';
    include 'includes/make-reservation-modal.php';
    include 'includes/check-availability-modal.php';
?>
<link rel="stylesheet" href="/css/vendor/jquery-ui.min.css">
<link rel="stylesheet" href="/css/vendor/jquery-ui.structure.min.css">
<link rel="stylesheet" href="/css/vendor/jquery-ui.theme.min.css">
<script src="/js/vendor/jquery-ui.min.js"></script>
<script>
    var reservation = window.reservation || {};

    reservation.reservationForm = $('form#reserve');
    reservation.checkAvailabilityForm = $('form#checkAvailability');
    reservation.errors = {};
    reservation.errorContainer = $('.error-container');
    reservation.testMode = false;

    /**
     * Do validation checks.
     * Show/clear errors as required.
     *
     * @returns {boolean}
     */
    reservation.validate = function(form)
    {
        reservation.clearErrors();
        if(reservation.checkRequired(form) && reservation.checkPhone(form) && reservation.checkValidEmailAddress(form))
        {
            return true;
        }

        reservation.showErrors();
        return false;
    };

    /**
     * Check valid email address format.
     *
     * @returns {boolean}
     */
    reservation.checkValidEmailAddress = function(form)
    {
        var error = false;
        $('.email', form).each(function()
        {
            var jThis = $(this);
            var field = jThis.attr('name');
            var input = 'input[name="' + field + '"]';
            var emailAddress = $(input).val();
            var emailTest = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
            if(!emailTest.test(emailAddress))
            {
                if($.inArray('invalidEmail', reservation.errors[field]) === -1) {
                    if(typeof(reservation.errors[field]) === 'undefined')
                    {
                        reservation.errors[field] = ['The email address is not valid'];
                        error = true;
                    }else {
                        var fieldErrors = reservation.errors[field];
                        fieldErrors.push('The email address is not valid');
                        error = true;
                    }
                }
            }
        });
        return error ? false : true;
    };

    /**
     * Check all required fields are not empty.
     *
     * @returns {boolean}
     */
    reservation.checkRequired = function(form)
    {
        var error = false;
        $('.required', form).each(function()
        {
            var jThis = $(this);
            var field = jThis.attr('name');
            if(jThis.val() === '')
            {
                if($.inArray('required', reservation.errors[field]) === -1) {
                    if(typeof(reservation.errors[field]) === 'undefined')
                    {
                        reservation.errors[field] = ['The ' + field + ' field is required.'];
                        error = true;
                    }else {
                        var fieldErrors = reservation.errors[field];
                        fieldErrors.push('The ' + field + ' field is required.');
                        error = true;
                    }
                }
            }
        });
        return error ? false : true;
    };

    /**
     * Check valid US phone number format.
     *
     * @returns {boolean}
     */
    reservation.checkPhone = function(form)
    {
        var error = false;
        $('.phone', form).each(function()
        {
            var jThis = $(this);
            var field = jThis.attr('name');
            var input = 'input[name="' + field + '"]';
            var inputVal = $(input).val();
            var phoneTest = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/.test(inputVal);
            if(!phoneTest) {
                error = true;
                if($.inArray('invalidPhone', reservation.errors[field]) === -1) {
                    if(typeof(reservation.errors[field]) === 'undefined')
                    {
                        reservation.errors[field] = ['The phone number is not valid.'];
                    }else {
                        var fieldErrors = reservation.errors[field];
                        fieldErrors.push('The phone number is not valid.');
                    }
                }
            }
        });
        return error ? false : true;
    };

    /**
     * Auto-hyphenate the phone number fields.
     *
     */
    reservation.autoHyphenate = function()
    {
        $('.phone').keyup(function(){
            //Remove hyphens that the user enters.
            var phoneNum = $(this).val().split("-").join("");
            if($(this).val().length > 3){
                phoneNum = phoneNum.match(new RegExp('.{1,4}$|.{1,3}', 'g')).join("-");
                $(this).val(phoneNum);
            }
        });
    };

    /**
     * Display any errors.
     *
     */
    reservation.showErrors = function()
    {
        reservation.highlightFields();
        reservation.displayErrorMessages();
    };

    /**
     * Display error messages
     *
     */
    reservation.displayErrorMessages = function()
    {
        var errorListLi = [];
        for(var error in reservation.errors)
        {
            if(reservation.errors.hasOwnProperty(error))
            {
                var errorString = '<li>' + reservation.errors[error] + '</li>';
                if($.inArray(errorString, errorListLi) === -1)
                {
                    errorListLi.push('<li>' + reservation.errors[error] + '</li>');
                }
            }
        }
        var list = errorListLi.join('');
        reservation.errorContainer.find('ul.errors').append(list);
        reservation.errorContainer.show();
    };

    /**
     * Highlight error fields.
     *
     */
    reservation.highlightFields = function()
    {
        for(var i in reservation.errors)
        {
            if(reservation.errors.hasOwnProperty(i))
            {
                var input = 'input[name="' + i + '"]';
                var select = 'select[name="' + i + '"]';
                $(input).addClass("error");
                $(select).addClass("error");
            }
        }
    };

    /**
     * Clear errors object.
     * Remove error field highlights.
     * Clear the error container of messages.
     *
     */
    reservation.clearErrors = function()
    {
        reservation.errors = {};
        $('.error').removeClass("error");
        reservation.errorContainer.hide().find('ul.errors').empty();
    };


    /**
     * Add errors from server to errors object.
     *
     */
    reservation.handleServerErrors = function(errors)
    {
        reservation.clearErrors();
        for (var field in errors) {
            if (errors.hasOwnProperty(field)) {
                for (var error in errors[field]) {
                    if (errors[field].hasOwnProperty(error)) {
                        var errorMessage = errors[field][error];
                        if($.inArray(errorMessage, reservation.errors[field]) === -1) {
                            if(typeof(reservation.errors[field]) === 'undefined')
                            {
                                reservation.errors[field] = [errorMessage];
                                error = true;
                            }else {
                                var fieldErrors = reservation.errors[field];
                                fieldErrors.push(errorMessage);
                                error = true;
                            }
                        }
                    }
                }
            }
        }
        reservation.showErrors();
    };

    /**
     * Display reservation confirmation message.
     *
     */
    reservation.showThankYou = function()
    {
        var thankYouMessage = '<div class="thankYouMessage">';
        thankYouMessage += '<h1>Thank you!</h1>';
        thankYouMessage += '<h2>A represantative will contact you shortly to confirm your reservation.</h2>';
        thankYouMessage += '<p class="text-center"><a data-dismiss="modal">Close Window</a></p>';
        thankYouMessage += '</div>';
        reservation.reservationForm.slideUp(function()
        {
            reservation.reservationForm.after(thankYouMessage);
            $('div.thankYouMessage').slideDown();
        });
    };

    /**
     * Handle reservation form POST.
     *
     */
    reservation.createReservation = function()
    {
        var formData = reservation.reservationForm.serialize();

        $.post('/property/reserve', formData, function(d)
        {
            if(d.status === 'error')
            {
                reservation.handleServerErrors(d.errors);
            }
            if(d.status === 'success')
            {
                reservation.reservationForm.trigger('reset');
                reservation.showThankYou();
            }
        });
    };

    /**
     * Handle reservation form submission.
     *
     * @returns {boolean}
     */
    reservation.reservationForm.submit(function(e)
    {
        e.preventDefault();
        if(reservation.testMode)
        {
            return reservation.createReservation();
        }
        if(reservation.validate(reservation.reservationForm))
        {
            reservation.clearErrors();
            return reservation.createReservation();
        }
    });

    /**
     * Display a property availability success message.
     *
     */
    reservation.showAvailableSuccess = function()
    {
          reservation.checkAvailabilityForm.slideUp(function()
          {
              var successMsg = '<h3 class="availableSuccess">This property is available for the selected date range.</h3>';
              reservation.checkAvailabilityForm.before(successMsg);
              reservation.checkAvailabilityForm.slideDown();
          });
    };

    /**
     * Display a property availability success message.
     *
     */
    reservation.showAvailableFailure = function()
    {
        reservation.checkAvailabilityForm.slideUp(function()
        {
            var successMsg = '<h3 class="availableError alert-danger">Please select a different date range.</h3>';
            reservation.checkAvailabilityForm.before(successMsg);
            reservation.checkAvailabilityForm.slideDown();
        });
    };

    /**
     * Send AJAX POST request.
     * Hide/display error messages.
     *
     */
    reservation.checkAvailability = function()
    {
        var formData = reservation.checkAvailabilityForm.serialize();

        $.post('/property/check', formData, function(d)
        {
            reservation.clearErrors();
            $("h3.availableSuccess, h3.availableError").slideUp(function()
            {
                $('h3.availableSuccess').remove();
                $('h3.availableError').remove();
            });
            if(d.status === 'unavailable')
            {
                reservation.showAvailableFailure();
            }
            if(d.status === 'error')
            {
                reservation.handleServerErrors(d.errors);
            }
            if(d.status === 'success')
            {
                reservation.showAvailableSuccess();
            }
        });
    };

    /**
     * Handle check availability form submission.
     *
     */
    reservation.checkAvailabilityForm.submit(function(e)
    {
        e.preventDefault();
        if(reservation.testMode)
        {
            return reservation.checkAvailability();
        }
        if(reservation.validate(reservation.checkAvailabilityForm))
        {
            reservation.clearErrors();
            return reservation.checkAvailability();
        }
    });

    //Enable auto-hyphenation for phone number fields.
    reservation.autoHyphenate();

    /**
     * Clear modal on close
     *
     */
    $("#make-reservation-modal, #check-availability-modal").on('hidden.bs.modal', function(e)
    {
        reservation.clearErrors();
        $('h3.availableSuccess').remove();
        reservation.reservationForm.trigger('reset');
        reservation.checkAvailabilityForm.trigger('reset');
    });

    /**
     * Swap out the main image when a thumbnail is clicked.
     */
    $(".detail-thumbnails a.thumbnail").each(function()
    {
        var thumbnailUrl = $(this).find('img').attr('src');

        $(this).click(function()
        {
            $('#detail-primary-image').find('img').attr('src', thumbnailUrl);
        });
    });

    /**
     * Set date picker minimum dates.
     *
     */
    $('input[name="check_in"]').datepicker({
        minDate: 0,
        onSelect: function(date)
        {
            var checkIn = $('input[name="check_in"]').datepicker('getDate');
            var date = new Date(Date.parse(checkIn));
            date.setDate(date.getDate() + 1);
            var minCheckOutDate = date.toDateString();
            minCheckOutDate = new Date(Date.parse(minCheckOutDate));
            $('input[name="check_out"]').datepicker("option", "minDate", minCheckOutDate);
        }
    });

    /**
     * Set date picker minimum dates.
     *
     */
    $('input[name="check_out"]').datepicker({
        minDate: 0
    });
</script>
</body>
</html>