<?php

namespace App\Repositories;

use App\Models\Comment;
use InfyOm\Generator\Common\BaseRepository;

class CommentRepository extends BaseRepository {

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'leave_id',
        'content',
    ];

    /**
     * Configure the Model
     * */
    public function model() {
        return Comment::class;
    }

    public function create(array $attributes) {
        $user_id = \Auth::user()->id;

        $comment = new Comment;
        $comment->sender_id = $user_id;
        $comment->leave_id = $attributes['leave_id'];
        $comment->content = $attributes['content'];
        $comment->save();

        return $comment;
    }

}
