<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>  Registration Page</title>

        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <link rel="stylesheet" href="/css/app.css?version=1.8">
        @yield('css')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="hold-transition register-page">
        <div class="register-box">
            <div class="login-logo">
                <a href="/" class="logo"><img src="/images/logo-01.svg" alt="LEAVESTAR"/></a>
            </div>

            <div class="register-box-body">
                <p class="login-box-msg">Complete and confirm the form to finish the registration</p>

                <div class="row">
                    {!! Form::model($userRegister, ['action' =>'UserRegisterController@Register', 'method' => 'post','id'=>'form-register']) !!}

                    <div class="form-group col-sm-12">
                        {!! Form::label('org_id', 'You have been assigned to:') !!}
                        <?= App\Models\OrganisationStructure::find($userRegister->org_id)->name ?>
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('role', 'As:') !!}
                        <?= ($userRegister->is_admin != 'yes') ? "Normal User" : "Manager" ?>
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('email', 'Email:') !!}
                        {!! Form::email('email', null, ['class' => 'form-control']) !!}
                    </div>

                    <!-- Name Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>


                    <!-- Phone Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('phone', 'Phone:') !!}
                        {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                    </div>

                    <!-- Birthday Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('birthday', 'Birthday:') !!}
                        {!! Form::date('birthday', null, ['class' => 'form-control']) !!}
                    </div>


                    <div class="form-group col-sm-12">
                        {!! Form::label('password', 'Password:') !!}
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('password', 'Confirm Password:') !!}
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                    </div>

                    <!-- Submit Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::submit('Register and Login', ['class' => 'btn btn-primary']) !!}

                    </div>

                    {!! Form::close() !!}
                </div>

                <a href="{{ url('/login') }}" class="text-center">I already have a membership</a>
            </div>
            <!-- /.form-box -->
        </div>
        <!-- /.register-box -->

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/iCheck/1.0.2/icheck.min.js"></script>

        <!-- AdminLTE App -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/js/app.min.js"></script>



        <script>
$(function () {
    $('input').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%' // optional
    });
});
        </script>

        <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js' )}}"></script>
        {!! $validator !!}
    </body>
</html>
