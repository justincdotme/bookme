<?php include 'includes/header.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <h1>Login</h1>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="alert alert-info">
                                <span><strong>Username:</strong> demo.user@justinc.me</span> <br>
                                <span><strong>Password:</strong> demo123</span>
                                <hr>
                                <p class="small">
                                    * The application resets periodically.
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <?php if (isset($data->loginError)) : ?>
                            <div class="alert alert-danger">
                                <h3><?php echo $data->loginError ?></h3>
                            </div>
                            <?php endif; ?>
                            <form action="/admin/login" method="POST" role="form" class="form-horizontal">
                                <div class="form-group col-md-12">
                                    <label class="control-label">E-Mail Address</label>
                                    <input type="email" value="" name="email" placeholder="email@address.com" class="form-control" required>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="control-label">Password</label>
                                    <input type="password" name="password" placeholder="Password" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group col-sm-4 col-sm-offset-4">
                                    <button style="margin-right: 15px;" class="btn btn-primary" type="submit">
                                        Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>