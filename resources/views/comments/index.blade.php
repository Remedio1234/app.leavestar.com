<?php if (sizeof($comments) > 0) { ?>
    @foreach($comments as $comment)
    <?php
    $curr_user = \Auth::user()->id;
    ?>
    <div class="content_block comment-block">
        <div class="col-xs-2 comment-image">
            <img src="/images/user-default.svg" alt="" class="user-image"/>
        </div>
        <div class="col-xs-10">
            <div class="comments">
                <p><strong><em><?= \App\User::find($comment->sender_id)->name ?></em></strong> {!! $comment->content !!}</p>
                <div class="comment-meta">
                    <?php if ($curr_user == $comment->sender_id) { ?>
                        <div class="pull-left">
                            {!! Form::open(['route' => ['comments.destroy', $comment->id], 'method' => 'delete','class'=>'form-render3']) !!}
                            {!! Form::hidden('leave_id',$leave_id  ) !!}
                            {!! Form::button('delete', ['type' => 'submit' , 'onclick' => "return confirm('Are you sure?')", 'class' => 'btn btn-link']) !!}  
                            {!! Form::close() !!}
                        </div>
                    <?php } ?>
                    <?= $comment->created_at ?>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div> 


    @endforeach

<?php } else {
    ?>
    <div class="content_block">
        <p class="content"> No comments</p>
    </div>
<?php } ?>
<div class="comment_add">
    {!! Form::open(['route' =>'comments.store','class'=>'form-render3']) !!}
    <!-- Content Field -->
    {!! Form::hidden('leave_id',$leave_id  ) !!}

    <div class="form-group col-sm-9">

        {!! Form::textarea('content', null, ['class' => 'form-control','placeholder'=>'Add Comment Here' ,'rows'=>2]) !!}
    </div>

    <!-- Submit Field -->
    <div class="form-group col-sm-3">
        {!! Form::submit('Add', ['class' => 'btn btn-primary']) !!}

    </div>

    {!! Form::close() !!}


</div>    


<!--<div class="hide_button">
    <a href=""   >Hide^</a>
</div>-->

<script>
    $(function () {
        $('.form-render3').ajaxForm({
            beforeSubmit: function (arr, $form, options) {

                if ($(this).find('.has-error').length > 0) {
                    return false;
                }
            },
            success: function (res, status, xhr, form) {

                var id = form.find('input[name="leave_id"]').val();
                $(".leaveappid" + id).find('.comment_content').empty().html(res);
            },
        });

    });
</script>    