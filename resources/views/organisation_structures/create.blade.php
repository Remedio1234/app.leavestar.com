 
<div class="content-normal">
    <section class="content-header">
        <h1>
            Organisation Structure
        </h1>
    </section>
    <div class="clearfix"></div>
    @include('adminlte-templates::common.errors')
    <div class="clearfix"></div>
    <div class="box box-primary">

        <div class="box-body">
            <div class="row">
                {!! Form::open(['route' => 'organisationStructures.store' ,'class'=>'form-render','id'=>'org_create']) !!}

                @include('organisation_structures.fields')

                <input type="hidden" name="parent_id" value=<?= $parent_id ?> >
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>



<!-- Laravel Javascript Validation -->
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js' )}}"></script>

{!! $validator !!}
