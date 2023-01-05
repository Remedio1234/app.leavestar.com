@extends('organisation_users.user_edit_layout')

@section('render')
    <section class="content-header">
        <h1>
            Customized Feed
        </h1>
   </section>
   <div class="content">
       @include('adminlte-templates::common.errors')
       <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                   {!! Form::model($customizedFeed, ['route' => ['customizedFeeds.update', $customizedFeed->id], 'method' => 'patch']) !!}

                        @include('customized_feeds.fields')

                   {!! Form::close() !!}
               </div>
           </div>
       </div>
   </div>
@endsection