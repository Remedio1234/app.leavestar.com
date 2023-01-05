<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Repositories\CommentRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CommentController extends AppBaseController {

    /** @var  CommentRepository */
    private $commentRepository;

    public function __construct(CommentRepository $commentRepo) {
        $this->middleware('auth');
        $this->commentRepository = $commentRepo;
    }

    /**
     * Display a listing of the Comment.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $leave_id = $request['leave_id'];
            $comments = \App\Models\Comment::where('leave_id', $leave_id)->get();
            return view('comments.index')
                            ->with(['comments' => $comments, 'leave_id' => $leave_id]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new Comment.
     *
     * @return Response
     */
    public function create() {
        if (\Request::ajax()) {
            return view('comments.create');
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created Comment in storage.
     *
     * @param CreateCommentRequest $request
     *
     * @return Response
     */
    public function store(CreateCommentRequest $request) {
        if (\Request::ajax()) {
            $input = $request->all();
            $comment = $this->commentRepository->create($input);
            $leave_id = $request['leave_id'];
            $comments = \App\Models\Comment::where('leave_id', $leave_id)->get();

            $user = \Auth::user();
            $user->sendCommentNotification($comment);

            return view('comments.index')
                            ->with(['comments' => $comments, 'leave_id' => $leave_id]);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified Comment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');

//        $comment = $this->commentRepository->findWithoutFail($id);
//
//        if (empty($comment)) {
//            Flash::error('Comment not found');
//
//            return redirect(route('comments.index'));
//        }
//
//        return view('comments.show')->with('comment', $comment);
    }

    /**
     * Show the form for editing the specified Comment.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {
        return view('errors.403');

//        $comment = $this->commentRepository->findWithoutFail($id);
//
//        if (empty($comment)) {
//            Flash::error('Comment not found');
//
//            return redirect(route('comments.index'));
//        }
//
//        return view('comments.edit')->with('comment', $comment);
    }

    /**
     * Update the specified Comment in storage.
     *
     * @param  int              $id
     * @param UpdateCommentRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCommentRequest $request) {
        return view('errors.403');
//        $comment = $this->commentRepository->findWithoutFail($id);
//
//        if (empty($comment)) {
//            Flash::error('Comment not found');
//
//            return redirect(route('comments.index'));
//        }
//
//        $comment = $this->commentRepository->update($request->all(), $id);
//
//        Flash::success('Comment updated successfully.');
//
//        return redirect(route('comments.index'));
    }

    /**
     * Remove the specified Comment from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id) {
        if (\Request::ajax()) {
            $leave_id = \App\Models\Comment::find($id)->leave_id;
            $this->commentRepository->delete($id);
            $comments = \App\Models\Comment::where('leave_id', $leave_id)->get();
            return view('comments.index')
                            ->with(['comments' => $comments, 'leave_id' => $leave_id]);
        } else {
            return view('errors.403');
        }
    }

}
