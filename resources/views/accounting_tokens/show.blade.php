@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Accounting Token
        </h1>
    </section>
    <div class="content">
        <div class="box box-primary">
            <div class="box-body">
                @include('accounting_tokens.show_fields')
                <a href="{!! route('accountingTokens.index') !!}" class="btn btn-default">Back</a>
            </div>
        </div>
    </div>
@endsection
