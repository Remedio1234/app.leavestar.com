@extends('layouts.setting_layout')

@section('content')
<section class="content-header">
    <h1 class="pull-left">Xero Connection</h1>

</section>
<div class="content">
    <div class="clearfix"></div>

    @include('flash::message')

    <div class="clearfix"></div>
    <div class="box box-primary">
        <div class="box-body">
            <?php
            $accounting_token = App\Models\AccountingToken::where([
                        'org_str_id' => \Session::get('XeroOrg'),
                        'accsoft_id' => 1
                    ])->first();
            if (!(isset($accounting_token))) {
                ?> 
                <table class="table table-striped">
                    <tr>
                        <td>No Xero Connection</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="btn-group pull-right " style="margin-top: -10px;margin-bottom: 5px">
                                <button type="button" class="btn btn-primary  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Connect <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="/xero/connect">Connect Xero</a></li>
                                    <li><a href="/xerousa/connect">Connect Xero USA</a></li>

                                </ul>
                            </div>


                        </td>
                    </tr>
                </table>
                <?php
            } else {
                ?>
                <hr/>
                <p><strong>Current Xero Organsation Name:</strong>    <?= $accounting_token->xero_org_name ?> </p>
                <p><strong>Xero Version:</strong>    <?= \App\Models\accountingSoftware::find($accounting_token->accsoft_id)->name ?> </p>
                <hr/>
                <p>
                <div class="btn-group" style="margin-top: -10px;margin-bottom: 5px">
                    <button type="button" class="btn btn-success  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Synchronise <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="/xero/connect">Synchronise Xero</a></li>
                        <li><a href="/xerousa/connect">Synchronise Xero USA</a></li>

                    </ul>
                    <a class="btn btn-danger button-open-right"   href="javascript:return false;" data-href="/xero/disconnect/?id=<?= $accounting_token->id ?>">Disconnect</a>
                </div>

                </p>

                <?php
            }
            ?>
        </div>
    </div>
</div>
@endsection