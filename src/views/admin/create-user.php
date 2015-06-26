<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Create User</h1>
            <div class="col-sm-12">
                <?php
                    if(isset($data->errors))
                    {
                        $htmlOut = '';
                        $htmlOut .= '<div class="alert alert-danger">' . PHP_EOL;
                        $htmlOut .= '<h1>Errors have occurred!</h1>';
                        $htmlOut .= '<ul>' . PHP_EOL;
                        foreach($data->errors as $field => $error)
                        {
                            foreach($error as $msg)
                            {
                                $htmlOut .= '<li>' . PHP_EOL;
                                $htmlOut .= $msg;
                                $htmlOut .= '</li>' . PHP_EOL;
                            }
                        }
                        $htmlOut .= '</ul>' . PHP_EOL;
                        $htmlOut .= '</div>' . PHP_EOL;
                        echo $htmlOut;
                    }
                ?>
                <ul class="alert alert-danger error-container">

                </ul>
                <form id="add-user" method="POST" action="/admin/users" >
                    <input type="hidden" value="<?php echo $data->csrfToken; ?>" name="_CSRF" class="form-control" >
                    <div class="form-group col-md-12">
                        <label class="control-label">User Name</label>
                        <input type="text" value="<?php echo isset($data->name) ? $data->name : null; ?>" name="name" placeholder="User Name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">E-Mail Address</label>
                        <input type="email" value="<?php echo isset($data->email) ? $data->email : null; ?>" name="email" placeholder="email@address.com" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">Password</label>
                        <input type="password" name="password" placeholder="Password" class="form-control" required>
                    </div>
                    <div class="form-group col-md-12">
                        <label class="control-label">Repeat Password</label>
                        <input type="password" name="password-repeat" placeholder="Repeat Password" class="form-control" required>
                    </div>
                    <div class="clearfix"></div>
                    <div class="form-group col-md-12">
                        <button class="btn btn-primary col-md-12" type="submit">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script>
    var bookMe = window.bookMe || {};
    bookMe.users = {};
    bookMe.users.errors = [];
    bookMe.users.form = $("form#add-user")
    bookMe.users.errorList = $('ul.error-container');

    /**
     * Validate form fields.
     *
     */
    bookMe.users.validate = function()
    {
        bookMe.users.errors = [];
        bookMe.users.hasError = false;

        var username = $('input[name="name"]').val();
        var email = $('input[name="email"]').val();
        var password = $('input[name="password"]').val();
        var passwordRepeat = $('input[name="password-repeat"]').val();

        bookMe.users.checkRequired([username, email, password, passwordRepeat]);
        bookMe.users.passwordsMatch(password, passwordRepeat);
        bookMe.users.checkEmail(email);

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
                bookMe.users.errors.push('Please fill out all fields.');
                return false;
            }
        }
        return true;
    };

    /**
     * Check that the supplied value is an email address.
     *
     */
    bookMe.users.checkEmail = function(email)
    {
        var rx = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
        if(!rx.test(email))
        {
            bookMe.users.hasError = true;
            bookMe.users.errors.push('Please enter a valid email address');
            return false;
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
     * Prevent default form submit action.
     *
     */
    bookMe.users.form.submit(function(e)
    {
        if(!bookMe.users.validate())
        {
            bookMe.users.showErrors();
            e.preventDefault();
        }
    });
</script>
</body>
</html>