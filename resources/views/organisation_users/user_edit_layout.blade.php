@extends('layouts.app')

@section('content')
<?php
switch ($view) {
    case "basic":
        $class_basic = "active";
        $class_email = "normal";
        $class_feeds = "normal";
        break;
    case "email":
        $class_basic = "normal";
        $class_email = "active";
        $class_feeds = "normal";
        break;
    case "feeds":
        $class_basic = "normal";
        $class_email = "normal";
        $class_feeds = "active";
        break;
}
?>



<div class="content">
    <h1>User Settings</h1>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?= $class_basic ?>"><a href="/organisationUser/editUser" >Basic</a></li>
        <li role="presentation" class="<?= $class_email ?>"> <a href="/organisationUser/editEmail"  >Email</a></li>
        <li role="presentation" class="<?= $class_feeds ?>" ><a href="/customizedFeeds"   >Calendar Feeds</a></li>

    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        @yield('render')

    </div>


</div><!-- /container -->




@endsection