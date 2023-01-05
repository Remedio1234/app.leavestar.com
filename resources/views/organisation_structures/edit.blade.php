 
<section class="content-header">
    <h1>
        Organisation Structure
    </h1>
</section>



<div class="content">
    @include('adminlte-templates::common.errors')
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                {!! Form::model($organisationStructure, ['route' => ['organisationStructures.update', $organisationStructure->id], 'method' => 'patch']) !!}

                 @include('settings.fieldsindex') 
          
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
