<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBlockDateRequest;
use App\Http\Requests\UpdateBlockDateRequest;
use App\Repositories\BlockDateRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BlockDateController extends AppBaseController {

    /** @var  BlockDateRepository */
    protected $validationRules = [
        'start_date' => 'required ',
        'end_date' => 'required ',
        'limits' => 'required|numeric',
    ];
    private $blockDateRepository;

    public function __construct(BlockDateRepository $blockDateRepo) {
        $this->middleware('auth');
        $this->blockDateRepository = $blockDateRepo;
    }

    /**
     * Display a listing of the BlockDate.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            $setting_id = $organisationStructure->setting_id;
            $block_dates = \App\Models\BlockDate::where('setting_id', $setting_id)->get();

            return view('block_dates.index')
                            ->with(['organisationStructure' => $organisationStructure, 'block_dates' => $block_dates, 'validator' => $validator, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new BlockDate.
     *
     * @return Response
     */
    public function create(Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\User::checkUserRole((\Auth::user()->id), $org_id)) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('block_dates.create')
                            ->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created BlockDate in storage.
     *
     * @param CreateBlockDateRequest $request
     *
     * @return Response
     */
    public function store(CreateBlockDateRequest $request) {

        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];

            //create the new leave type and list it 
            $input = $request->all();
            $blockDate = $this->blockDateRepository->create($input);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $block_dates = \App\Models\BlockDate::where('setting_id', $setting_id)->get();


            $alert = 'Block Dates saved successfully.';
            return redirect(route('blockDates.index', ['org_id' => $org_id]))->with('status', $alert);

//return view('block_dates.index')->with(['organisationStructure' => $organisationStructure, 'block_dates' => $block_dates, 'validator' => $validator, 'alert' => $alert, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified BlockDate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {

        return view('errors.403');
//        $blockDate = $this->blockDateRepository->findWithoutFail($id);
//
//        if (empty($blockDate)) {
//            Flash::error('Block Date not found');
//
//            return redirect(route('blockDates.index'));
//        }
//
//        return view('block_dates.show')->with('blockDate', $blockDate);
    }

    /**
     * Show the form for editing the specified BlockDate.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\Models\BlockDate::checkBelonging($id, $request['org_id'])) {
            $validator = \JsValidator::make($this->validationRules);
            $blockDate = $this->blockDateRepository->findWithoutFail($id);

            $dates = \App\Models\Setting::getLocalTime($org_id, $blockDate->start_date);
            $dates2 = \App\Models\Setting::getLocalTime($org_id, $blockDate->end_date);

            $blockDate->date_range = $dates . ' - ' . $dates2;

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('block_dates.edit')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'block_dates' => $blockDate, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified BlockDate in storage.
     *
     * @param  int              $id
     * @param UpdateBlockDateRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBlockDateRequest $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            $this->blockDateRepository->update($request->all(), $id);

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $block_dates = \App\Models\BlockDate::where('setting_id', $setting_id)->get();

            $alert = 'Block Dates saved successfully';
            return redirect(route('blockDates.index', ['org_id' => $org_id]))->with('status', $alert);
            ;
            //return view('block_dates.index')->with(['organisationStructure' => $organisationStructure, 'block_dates' => $block_dates, 'validator' => $validator, 'alert' => $alert, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified BlockDate from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {

        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            // pass the org_id with a tricky way
            $array['id'] = $id;
            $array['org_id'] = $org_id;

            $this->blockDateRepository->delete($array);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $block_dates = \App\Models\BlockDate::where('setting_id', $setting_id)->get();

            $alert = 'Block Dates Rule deleted.';
            return redirect(route('blockDates.index', ['org_id' => $org_id]))->with('status', $alert);
// return view('block_dates.index')->with(['organisationStructure' => $organisationStructure, 'block_dates' => $block_dates, 'validator' => $validator, 'alert' => $alert, 'view' => 'blockdate']);
        } else {
            return view('errors.403');
        }
    }

}
