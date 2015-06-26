<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Users</h1>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table id="admin_products_list" class="table table-striped table-responsive table-hover table-bordered text-center">
                        <thead>
                            <tr>
                                <th class="col-sm-4">Name</th>
                                <th class="col-sm-4">Email</th>
                                <th class="col-sm-2">Edit</th>
                                <th class="col-sm-2">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!$data->users->isEmpty())
                            {
                                $htmlOut = '';
                                foreach($data->users as $user)
                                {
                                    $htmlOut .= '<tr>' . PHP_EOL;
                                    $htmlOut .= '<td>' . PHP_EOL;
                                    $htmlOut .= $user->name . PHP_EOL;
                                    $htmlOut .= '</td>' . PHP_EOL;
                                    $htmlOut .= '<td>' . PHP_EOL;
                                    $htmlOut .= '<a href="mailto:' . $user->email .  '">' . $user->email . '</a>' . PHP_EOL;
                                    $htmlOut .= '</td>' . PHP_EOL;
                                    $htmlOut .= '<td>' . PHP_EOL;
                                    if(intval($data->currentUser) === 1)
                                    {
                                        $htmlOut .=  '<a class="btn btn-info" data-toggle="modal" data-href="/admin/users/' . $user->uid . '" data-target="#password-change">Edit</a>' . PHP_EOL;
                                    }else if($data->currentUser === $user->uid) {
                                        $htmlOut .=  '<a class="btn btn-info" data-toggle="modal" data-href="/admin/users/' . $user->uid . '" data-target="#password-change">Edit</a>' . PHP_EOL;
                                    }else {
                                        $htmlOut .= '-';
                                    }
                                    $htmlOut .= '</td>' . PHP_EOL;
                                    $htmlOut .= '<td class="vcenter">' . PHP_EOL;
                                    if(intval($user->uid) === 1)
                                    {
                                        $htmlOut .=  '-' . PHP_EOL;
                                    } else if(intval($data->currentUser) === 1 || $user->uid === $data->currentUser) {
                                        $htmlOut .=  '<a class="btn btn-danger" data-toggle="modal" data-href="/admin/users/' . $user->uid . '" data-target="#delete-confirm">Delete</a>' . PHP_EOL;

                                    } else {
                                        $htmlOut .=  '-' . PHP_EOL;
                                    }
                                    $htmlOut .= '</td>' . PHP_EOL;
                                    $htmlOut .= '</tr>' . PHP_EOL;
                                }
                                echo $htmlOut;
                            }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="password-change" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="confirmHead">Update Password</h4>
            </div>
            <div class="modal-body">
                <ul class="alert alert-danger error-container">

                </ul>
                <form id="update-password" method="POST">
                    <input type="hidden" name="_METHOD" value="PUT"/>
                    <input type="hidden" name="_CSRF" value="<?php echo $data->csrfToken; ?>"/>
                    <div class="form-group">
                        <label class="control-label">New Password</label>
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Repeat New Password</label>
                        <input type="password" name="password-repeat" placeholder="Repeat Password" class="form-control" required>
                    </div>
                    <div class="form-group text-center">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-ok" id="update-submit">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="confirmHead">Confirm Delete User?</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure that you want to delete this user?</p>
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
     * Pass the data-href property to the modal window and use it as the form action.
     * This is used for updating and deleting users.
     */
    $('#delete-confirm, #password-change').on('show.bs.modal', function(e) {
        $(this).find('form').attr('action', $(e.relatedTarget).data('href'));
    });

    var bookMe = window.bookMe || {};
    bookMe.users = {};
    bookMe.users.errors = [];
    bookMe.users.form = $("form#update-password")
    bookMe.users.errorList = $('ul.error-container');

    /**
     * Validate form fields.
     *
     */
    bookMe.users.validate = function()
    {
        bookMe.users.errors = [];
        bookMe.users.hasError = false;

        var password = $('input[name="password"]').val();
        var passwordRepeat = $('input[name="password-repeat"]').val();

        bookMe.users.checkRequired([password, passwordRepeat]);
        bookMe.users.passwordsMatch(password, passwordRepeat);

        if(bookMe.users.errors.length > 0)
        {
            bookMe.users.showErrors();
            return false;
        }

        bookMe.users.errorList.hide();
        bookMe.users.errorList.empty();
        return true;
    };

    /**
     * Check that passwords match.
     *
     */
    bookMe.users.passwordsMatch = function(password, repeatPassword)
    {
        if(password !== repeatPassword)
        {
            bookMe.users.hasError = true;
            bookMe.users.errors.push('The password fields must match.');
            return false;
        }
        return true;
    };

    /**
     * Check that required fields are filled out.
     *
     */
    bookMe.users.checkRequired = function(fields)
    {
        for(var i=0; i < fields.length; i++)
        {
            if(!fields[i])
            {
                bookMe.users.hasError = true;
                bookMe.users.errors.push('Please fill out both password fields.');
                return false;
            }
        }
        return true;
    };

    /**
     * Display error messages.
     *
     */
    bookMe.users.showErrors = function()
    {
        bookMe.users.errorList.hide();
        bookMe.users.errorList.empty();
        bookMe.users.errorList.show();
        for(var i=0; i < bookMe.users.errors.length; i++)
        {
            bookMe.users.errorList.append('<li>' + bookMe.users.errors[i] + '</li>');
        }
    };

    /**
     * Handle form submission
     *
     */
    bookMe.users.doSubmit = function()
    {
        var data = bookMe.users.form.serialize();
        var action = bookMe.users.form.attr('action');
        var submitBtn = $('button#update-submit');
        var submitText = submitBtn.text();
        $.post(action, data, function(d)
        {
            submitBtn.prop('disabled', true);
            if(d.status === 'success')
            {
                submitBtn.text("Success!").removeClass('btn-primary').addClass('btn-success');
                window.setTimeout(function()
                {
                    $('#password-change').modal('hide');
                    submitBtn.removeClass('btn-success').addClass('btn-primary').text(submitText).prop('disabled', false);
                    $('input[name="password"], input[name="password-repeat"]').val("");
                }, 2000);
            }
        });
    };

    /**
     * Prevent default form submit action.
     *
     */
    bookMe.users.form.submit(function(e)
    {
        e.preventDefault();
        if(bookMe.users.validate())
        {
            bookMe.users.doSubmit();
        }
    });
</script>
</body>
</html>