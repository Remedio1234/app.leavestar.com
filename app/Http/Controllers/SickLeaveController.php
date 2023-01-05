<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSickLeaveRequest;
use App\Http\Requests\UpdateSickLeaveRequest;
use App\Repositories\SickLeaveRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class SickLeaveController extends AppBaseController {

    /** @var  SickLeaveRepository */
    protected $validationRules = [
        'rule_type' => 'required',
        'value' => 'required',
    ];
    private $sickLeaveRepository;

    public function __construct(SickLeaveRepository $sickLeaveRepo) {
        $this->middleware('auth');
        $this->sickLeaveRepository = $sickLeaveRepo;
    }

    /**
     * Display a listing of the SickLeave.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            $setting_id = $organisationStructure->setting_id;
            $sickleave = \App\Models\SickLeave::where('setting_id', $setting_id)->get();

            return view('sick_leaves.index')
                            ->with(['organisationStructure' => $organisationStructure, 'sick_leaves' => $sickleave, 'validator' => $validator, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new SickLeave.
     *
     * @return Response
     */
    public function create(Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\User::checkUserRole((\Auth::user()->id), $org_id)) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('sick_leaves.create')
                            ->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created SickLeave in storage.
     *
     * @param CreateSickLeaveRequest $request
     *
     * @return Response
     */
    public function store(CreateSickLeaveRequest $request) {

        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            //create the new leave type and list it 
            $input = $request->all();
            $this->sickLeaveRepository->create($input);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $sick_leaves = \App\Models\SickLeave::where('setting_id', $setting_id)->get();

            $alert = 'Sick Leave Rule saved successfully.';
            return redirect(route('sickLeaves.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('sick_leaves.index')->with(['organisationStructure' => $organisationStructure, 'sick_leaves' => $sick_leaves, 'validator' => $validator, 'alert' => $alert, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified SickLeave.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $sickLeave = $this->sickLeaveRepository->findWithoutFail($id);
//
//        if (empty($sickLeave)) {
//            Flash::error('Sick Leave not found');
//
//            return redirect(route('sickLeaves.index'));
//        }
//
//        return view('sick_leaves.show')->with('sickLeave', $sickLeave);
    }

    /**
     * Show the form for editing the specified SickLeave.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\Models\SickLeave::checkBelonging($id, $request['org_id'])) {
            $validator = \JsValidator::make($this->validationRules);
            $sick_leaves = $this->sickLeaveRepository->findWithoutFail($id);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('sick_leaves.edit')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'sick_leaves' => $sick_leaves, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified SickLeave in storage.
     *
     * @param  int              $id
     * @param UpdateSickLeaveRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateSickLeaveRequest $request) {

        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            $this->sickLeaveRepository->update($request->all(), $id);

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $sick_leaves = \App\Models\SickLeave::where('setting_id', $setting_id)->get();

            $alert = 'Sick Leave Rule updated successfully.';
            return redirect(route('sickLeaves.index', ['org_id' => $org_id]))->with('status', $alert);
            //return view('sick_leaves.index')->with(['organisationStructure' => $organisationStructure, 'sick_leaves' => $sick_leaves, 'validator' => $validator, 'alert' => $alert, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified SickLeave from storage.
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

            $this->sickLeaveRepository->delete($array);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $sick_leaves = \App\Models\SickLeave::where('setting_id', $setting_id)->get();


            $alert = 'Sick Leave Rule deleted successfully.';
            return redirect(route('sickLeaves.index', ['org_id' => $org_id]))->with('status', $alert);
// return view('sick_leaves.index')->with(['organisationStructure' => $organisationStructure, 'sick_leaves' => $sick_leaves, 'validator' => $validator, 'alert' => $alert, 'view' => 'sickleave']);
        } else {
            return view('errors.403');
        }
    }

    public function renderPartical(Request $request) {
        $type = $request["type"];
        $model = $request["sick_leaves"];

        if ($type == 1) {
            return view('sick_leaves.fields-partical-single');
        } else {
            return view('sick_leaves.fields-partical-checkbox')->with('model', $model);
        }
    }

}
