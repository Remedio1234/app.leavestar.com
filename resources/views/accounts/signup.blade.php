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
        <style>
            .StripeElement {
                background-color: white;
                padding: 8px 12px;
                border-radius: 4px;
                border: 1px solid transparent;
                box-shadow: 0 1px 3px 0 #e6ebf1;
                -webkit-transition: box-shadow 150ms ease;
                transition: box-shadow 150ms ease;
            }

            .StripeElement--focus {
                box-shadow: 0 1px 3px 0 #cfd7df;
            }

            .StripeElement--invalid {
                border-color: #fa755a;
            }

            .StripeElement--webkit-autofill {
                background-color: #fefde5 !important;
            }
        </style>
    </head>
    <body class="hold-transition register-page">
        <div class="register-box">
            <div class="login-logo">
                <a href="/" class="logo"><img src="/images/leavestar.svg" alt="LEAVESTAR"/></a>
            </div>

            <div class="register-box-body">
                <p class="login-box-msg">Complete and confirm the form to finish the registration</p>

                <div class="row">
                    {!! Form::open( ['route' =>'accounts.store', 'method' => 'post','id'=>'account-signup']) !!}

                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'The Name of your Business:') !!}
                        {!! Form::text('acc_name', null, ['class' => 'form-control']) !!}
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::label('email', 'UserName/Email:') !!}
                        {!! Form::email('email', null, ['class' => 'form-control']) !!}
                    </div>

                    <!-- Name Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('name', 'Your Name:') !!}
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </div>              

                    <!-- Birthday Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::label('birthday', 'Your Birthday:') !!}
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

                    <div class="form-group col-sm-12">
                        {!! Form::hidden('strip_token', null, ['class' => 'form-control']) !!}

                        <label for="card-element">
                            Credit or debit card
                        </label>
                        <div id="card-element">
                            <!-- a Stripe Element will be inserted here. -->

                        </div>

                        <!-- Used to display form errors -->
                        <div id="card-errors" role="alert"></div>

                    </div>
                    <!-- Submit Field -->
                    <div class="form-group col-sm-12">
                        {!! Form::submit('Register and Subscribe', ['class' => 'btn btn-primary']) !!}

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


        <!-- AdminLTE App -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/js/app.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>

        <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js' )}}"></script>
        {!! $validator !!}

        <script>
$(function () {
    var stripe = Stripe('<?= Config::get('stripe.stripe_pk') ?>');

// Create an instance of Elements
    var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            lineHeight: '24px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

// Create an instance of the card Element
    var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');

// Handle real-time validation errors from the card Element.
    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

// Handle form submission
    var form = document.getElementById('account-signup');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                // Send the token to your server                
                $("input[name='strip_token']").val(result.token.id);
                form.submit();
            }
        });
    });
});
        </script>


    </body>
</html>
