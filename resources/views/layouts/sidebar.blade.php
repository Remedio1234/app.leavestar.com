<aside class="main-sidebar" id="sidebar-wrapper">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <div class="hidden-xs">
            <a href="/" class="logo"><img src="/images/logo-01.svg" alt="LEAVESTAR"/></a>
        </div>

        <div class="mobile-wrap">
            <!-- Sidebar user panel (optional) -->
            <div class="user-panel">
                <?php
                $profile = \Auth::user()->profile_pic;
                $image = $profile ? url("/") . '/' . $profile : "/images/user-default.svg";
                //$image = url("/") . '/' . "/images/user-default.svg";
                ?>
                <div class="image" style="background-image: url('<?= $image ?>');"></div>
                <div class="info">
                    @if ((\Auth::user()->id==1 ))
                    <div class="btn-group edit-user">
                        <button type="button" class="btn btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Admin<span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{!! url('/logout') !!}" class="btn btn-default btn-flat"
                                   onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    Sign out
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="btn-group edit-user" id="usernameDropdown">
                        <button type="button" class="btn btn-block dropdown-toggle"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ Auth::user()->name}} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="btn btn-default btn-flat" href="/organisationUser/editUser">User Setting</a>
                            </li>
                            <li>
                                <a class="btn btn-default btn-flat" href="/organisationUser/enableTour">User Tour</a>
                            </li>
                            <li>
                                <a href="{!! url('/logout') !!}" class="btn btn-default btn-flat"
                                   onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                    Sign out
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-group org-switcher">
                        <button type="button" class="btn btn-block dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            $org_id = session('current_org');
                            $name = \App\Models\OrganisationStructure::where('id', $org_id)->first()->name;
                            echo $name;
                            ?>
                        </button>
                        <?php
                        $org_list = \App\Models\OrganisationUser::join('organisation_structure', 'organisation_structure.id', '=', 'organisation_user.org_str_id')
                                ->where([['organisation_user.user_id', \Auth::user()->id], ['organisation_user.org_str_id', '<>', $org_id]])
                                ->where('organisation_structure.parent_id', '<>', null)
                                ->get();
                        if (sizeof($org_list) !== 0) {
                            ?>
                            <ul class="dropdown-menu">
                                <?php foreach ($org_list as $item) {
                                    ?>
                                    <li><a href="<?= url('/home/changeorg', ['org_id' => $item->org_str_id]); ?>"><?= \App\Models\OrganisationStructure::where('id', $item->org_str_id)->first()->name ?></a></li>
                                <?php }
                                ?>


                            </ul>
                            <?php
                        }
                        ?>

                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar Menu -->
            <ul class="sidebar-menu">
                @include('layouts.menu')
            </ul>
            <!-- /.sidebar-menu -->
        </div>
    </section>
    <!-- /.sidebar -->
</aside>
<div class="visible-xs mobile-menu">
    <a href="/" class="phone-logo"><img src="/images/mobile-logo.svg" alt="LEAVESTAR"/></a>
    <button class="btn btn-primary btn-lg toggle-menu pull-right" type="button"><span class="glyphicon glyphicon-menu-hamburger"></span></button>
</div>