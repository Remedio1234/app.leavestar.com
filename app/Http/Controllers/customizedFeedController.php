<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatecustomizedFeedRequest;
use App\Http\Requests\UpdatecustomizedFeedRequest;
use App\Repositories\customizedFeedRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class customizedFeedController extends AppBaseController {

    /** @var  customizedFeedRepository */
    private $customizedFeedRepository;

    public function __construct(customizedFeedRepository $customizedFeedRepo) {
        $this->middleware('auth');
        $this->customizedFeedRepository = $customizedFeedRepo;
    }

    /**
     * Display a listing of the customizedFeed.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        $this->customizedFeedRepository->pushCriteria(new RequestCriteria($request));
        $customizedFeeds = \App\Models\customizedFeed::where([
                    'org_id' => $request->session()->get('current_org'),
                    'user_id' => \Auth::user()->id,
                ])->get();

        return view('customized_feeds.index')
                        ->with(['customizedFeeds' => $customizedFeeds, 'view' => 'feeds']);
    }

    /**
     * Show the form for creating a new customizedFeed.
     *
     * @return Response
     */
    public function create() {
        return view('customized_feeds.create')->with([ 'view' => 'feeds']);
        ;
    }

    /**
     * Store a newly created customizedFeed in storage.
     *
     * @param CreatecustomizedFeedRequest $request
     *
     * @return Response
     */
    public function store(CreatecustomizedFeedRequest $request) {
        $input = $request->all();

        $customizedFeed = \App\Models\customizedFeed::create([
                    'org_id' => $request->session()->get('current_org'),
                    'user_id' => \Auth::user()->id,
                    'feed' => str_replace(' ', '', $request['feed']),
                    'description' => isset($request['description']) ? $request['description'] : "",
                    'feedcolor' => $request['feedcolor'],
        ]);

        Flash::success('Customized Feed saved successfully.');

        return redirect(route('customizedFeeds.index'));
    }

    /**
     * Display the specified customizedFeed.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $customizedFeed = $this->customizedFeedRepository->findWithoutFail($id);
//
//        if (empty($customizedFeed)) {
//            Flash::error('Customized Feed not found');
//
//            return redirect(route('customizedFeeds.index'));
//        }
//
//        return view('customized_feeds.show')->with('customizedFeed', $customizedFeed);
    }

    /**
     * Show the form for editing the specified customizedFeed.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $customized_feed = \App\Models\customizedFeed::where([
                    'id' => $id,
                    'org_id' => $request->session()->get('current_org'),
                    'user_id' => \Auth::user()->id,
                ])->first();
        if (isset($customized_feed)) {
            $customizedFeed = $this->customizedFeedRepository->findWithoutFail($id);

            if (empty($customizedFeed)) {
                Flash::error('Customized Feed not found');

                return redirect(route('customizedFeeds.index'));
            }

            return view('customized_feeds.edit')->with(['customizedFeed' => $customizedFeed, 'view' => 'feeds']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified customizedFeed in storage.
     *
     * @param  int              $id
     * @param UpdatecustomizedFeedRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatecustomizedFeedRequest $request) {
        $customized_feed = \App\Models\customizedFeed::where([
                    'id' => $id,
                    'org_id' => $request->session()->get('current_org'),
                    'user_id' => \Auth::user()->id,
                ])->first();
        if (isset($customized_feed)) {
            $customizedFeed = $this->customizedFeedRepository->findWithoutFail($id);

            if (empty($customizedFeed)) {
                Flash::error('Customized Feed not found');

                return redirect(route('customizedFeeds.index'));
            }
            $customizedFeed = \App\Models\customizedFeed::updateOrCreate([
                        'id' => $id
                            ], [
                        'org_id' => $request->session()->get('current_org'),
                        'user_id' => \Auth::user()->id,
                        'feed' => str_replace(' ', '', $request['feed']),
                        'description' => isset($request['description']) ? $request['description'] : "",
                        'feedcolor' => $request['feedcolor'],
            ]);


            Flash::success('Customized Feed updated successfully.');

            return redirect(route('customizedFeeds.index'));
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified customizedFeed from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {
        $customized_feed = \App\Models\customizedFeed::where([
                    'id' => $id,
                    'org_id' => $request->session()->get('current_org'),
                    'user_id' => \Auth::user()->id,
                ])->first();
        if (isset($customized_feed)) {
            $customizedFeed = $this->customizedFeedRepository->findWithoutFail($id);

            if (empty($customizedFeed)) {
                Flash::error('Customized Feed not found');

                return redirect(route('customizedFeeds.index'));
            }

            $this->customizedFeedRepository->delete($id);

            Flash::success('Customized Feed deleted successfully.');

            return redirect(route('customizedFeeds.index'));
        } else {
            return view('errors.403');
        }
    }

}
