<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Leavestar</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!--
                <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
               
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/css/select2.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/css/AdminLTE.min.css">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/css/skins/_all-skins.min.css">-->
        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.css">
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
        <link rel="stylesheet" type="text/css" href="/css/jquery.timepicker.css" /> 
        <link rel="stylesheet" type="text/css" href="/css/bootstrap-tour.min.css" />  
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">-->
        <!--<link rel="stylesheet" href="https://cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.15/datatables.min.css">-->

        <!--ICON -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">

        <!--Icon Ending!-->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/dt-1.10.15/datatables.min.css"/>
        <link rel="stylesheet" href="/css/easy-autocomplete.min.css"> 
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">


        <link rel="stylesheet" href="/css/app-light-blue.css?version=<?= time() ?>">
        <style>
            @media all and (-ms-high-contrast:none)
            {
                .right-sidebar{
                    right:100%!important;
                    transform:translateX(0%);

                }
                .open-right-sidebar .right-sidebar{
                    transform:translateX(100%);
                }
            }
#leaveType {
  text-align: center;
  color: #15b75e;
  font-size: 20px;
  margin-top: 150px !important;
  font-weight: bold;
}

#emptyRecords {
    margin-top: 150px !important;
    text-align: center;
    font-family: Poppins,sans-serif;
    font-size: 20px;
}

#emptyWeeklyRecords {
    margin-top: 150px !important;
    text-align: center;
    font-family: Poppins,sans-serif;
    font-size: 20px;
}
        </style>

        @yield('css')
    </head>
    <div id="ajax-loading-fullscreen"   style="display: none;">
        <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>
    </div>
    <body class="skin-blue sidebar-mini">

        @if (!Auth::guest())
        <div class="wrapper">
            <!-- Left side column. contains the logo and sidebar -->
            @include('layouts.sidebar')
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper main">
                @yield('content')
            </div>

            <div class="right-sidebar">

            </div>

            <div id="ajax-loading-inner"  style="display: none;">
                <i class="fa fa-refresh fa-spin" style="font-size:24px"></i>
            </div>


        </div>
        @else
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{!! url('/') !!}">
                        InfyOm Generator
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        <li><a href="{!! url('/home') !!}">Home</a></li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        <li><a href="{!! url('/login') !!}">Login</a></li>
                        <li><a href="{!! url('/register') !!}">Register</a></li>
                    </ul>
                </div>
            </div>
        </nav>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        @endif


        <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->
        <script>
            window.Laravel = <?php
echo json_encode([
    'csrfToken' => csrf_token(),
]);
?>
        </script>

        <script src="/js/app.js?version=2.3"></script>
        <script src="https://cdn.jsdelivr.net/jquery.form/4.2.1/jquery.form.min.js" integrity="sha384-tIwI8+qJdZBtYYCKwRkjxBGQVZS3gGozr3CtI+5JF/oL1JmPEHzCEnIKbDbLTCer" crossorigin="anonymous"></script>

<!--        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />-->
        <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.3/js/app.min.js"></script>-->


        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
        <!--Tour Guide-->
        <script src="/js/bootstrap-tour.min.js"></script> 
        <?php
        $userLevel = \App\Models\OrganisationUser::checkLevel(\Auth::user()->id, \Session::get('current_org'));
        $guideTour = \App\User::find(\Auth::user()->id)->tourGuide;
        if ($guideTour == 0) {
            switch ($userLevel) {
                case 1:
                    $tour ='';
                    break;
                case 2:
                    $tour = '<script src="/js/tourAccount.js"></script>';
                    break;
                case 3:
                    $currentOrg = App\Models\OrganisationStructure::find(\Session::get('current_org'));
                    $parentOrg = App\Models\OrganisationStructure::find($currentOrg->parent_id);
                    $grantparentOrg = App\Models\OrganisationStructure::find($parentOrg->parent_id);
                    if (isset($grantparentOrg)) {
                        $tour = '<script src="/js/tourManagerWithoutXero.js"></script>';
                    } else {
                        $tour = '<script src="/js/tourManagerWithXero.js"></script>';
                    }

                    break;
                case 4:
                    $tour = '<script src="/js/tourNormal.js"></script>';
                    break;
            }
            echo $tour;
        }
        ?>
        <script src="/js/table2excel.js"></script>
        <script src="/js/reports.js"></script>
        <script src="/js/jquery.mjs.nestedSortable.js"></script>
        <script src="/js/orgtree.js?version=1.2"></script>
        <script src="/js/matching.js?version=1.3"></script>
        <script src="/js/jquery.easy-autocomplete.min.js"></script>
        <script type="text/javascript" src="/js/jquery.timepicker.js"></script>
        <script src="https://jonthornton.github.io/Datepair.js/dist/datepair.js"></script>
        <script src="https://jonthornton.github.io/Datepair.js/dist/jquery.datepair.js"></script>

        <!--<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>-->
        <!--<script type="text/javascript" src="//cdn.datatables.net/v/bs/jq-2.2.4/dt-1.10.15/datatables.min.js"></script>-->
        <!--<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jqc-1.12.4/dt-1.10.15/datatables.min.js"></script>-->
        <script type="text/javascript" src="https://cdn.datatables.net/v/bs/dt-1.10.15/datatables.min.js"></script>

        <!-- jQuery 2.1.4 -->

        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.2/js/select2.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script> 

        <!-- AdminLTE App -->

<script>
if (!$(".dummy").is(':visible')) {
    $("#emptyRecords").show();
    $("#leaveType").hide();
}
</script>
        @yield('scripts')

    </body>

</html>
