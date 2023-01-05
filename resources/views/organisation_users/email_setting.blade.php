@extends('organisation_users.user_edit_layout')

@section('render')
<section class="content-header">
    <h1>
        Email Setting
    </h1>
</section>
<div class="content">
    @include('adminlte-templates::common.errors')
    @include('flash::message')
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">

                <?php
                $org_user = \App\Models\OrganisationUser::where([
                            'org_str_id' => \Session::get('current_org'),
                            'user_id' => \Auth::user()->id
                        ])->first();
                if (isset($org_user->email_provider) && ($org_user->email_provider == 'gmail')) {
                    $img = "/images/Gmail.png";
                    $linked = "Gmail";
                }
                if (isset($org_user->email_provider) && ($org_user->email_provider == 'outlook')) {
                    $img = "/images/Microsoft_Outlook.png";
                    $linked = "Microsoft";
                }
                ?>

                <?php
                if (isset($img)) {
                    ?>
                
                    <img src="<?= $img ?>" style="max-width:100px;">
                    <p><?=  $linked  ?> has been linked.</p>
                    <?php
                } else {
                    ?>
                    No Email has been linked.
                    <?php
                }
                ?>
                <?php
                if (\Auth::user()->id != 1) {
                    ?>
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary">Link Email </button>
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="/gmail/connect">Gmail</a></li>
                            <li><a href="/outlook/connect">Microsoft</a></li>

                        </ul>
                    </div>
                <?php }
                ?>

            </div>
        </div>
    </div>
</div>
@endsection
