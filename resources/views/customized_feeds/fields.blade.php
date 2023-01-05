 
<?php
$colorList = \App\Models\customizedFeed::getColorFeedList();
?>
<!-- Feed Field -->
<div class="form-group col-sm-12">
    {!! Form::label('feed', 'Feed(URL):') !!}
    {!! Form::text('feed', null, ['class' => 'form-control']) !!}
</div>

<!-- Feed Field -->
<div class="form-group col-sm-12">
    {!! Form::label('description', 'Feed Description:') !!}
    {!! Form::textarea('description', null, ['class' => 'form-control','row'=>'2']) !!}
</div>


<div class="form-group col-sm-12 ">
    {!! Form::label('feedcolor', 'Feed Color:') !!}
    {!! Form::text('feedcolor', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
    <a href="{!! route('customizedFeeds.index') !!}" class="btn btn-default">Cancel</a>
</div>




@section('scripts')
<script type="text/javascript">

    $('#feedcolor').colorpicker();

</script>

@stop