<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeaveTypeRequest;
use App\Http\Requests\UpdateLeaveTypeRequest;
use App\Repositories\LeaveTypeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LeaveTypeController extends AppBaseController {

    /** @var  LeaveTypeRepository */
    protected $validationRules = [
        'name' => 'required',
    ];
    private $leaveTypeRepository;

    public function __construct(LeaveTypeRepository $leaveTypeRepo) {
        $this->middleware('auth');
        $this->leaveTypeRepository = $leaveTypeRepo;
    }

    /**
     * Display a listing of the LeaveType.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();
            //Find the setting model with all the relationship attach to it
            $root_org = \App\Models\OrganisationStructure::findRootOrg($request['org_id']);

            $leave_types = \App\Models\LeaveType::where('org_id', $root_org)->get();

            //$this->leaveTypeRepository->pushCriteria(new RequestCriteria($request));
            //$leaveTypes = $this->leaveTypeRepository->all();
            return view('leave_types.index')
                            ->with(['organisationStructure' => $organisationStructure, 'leave_type' => $leave_types, 'validator' => $validator, 'view' => 'leavetype']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new LeaveType.
     *
     * @return Response
     */
    public function create(Request $request) {

        $validator = \JsValidator::make($this->validationRules);
        $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

        return view('leave_types.create')
                        ->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'leavetype']);
    }

    /**
     * Store a newly created LeaveType in storage.
     *
     * @param CreateLeaveTypeRequest $request
     *
     * @return Response
     */
    public function store(CreateLeaveTypeRequest $request) {
        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            //create the new leave type and list it 
            $input = $request->all();
            $leaveType = $this->leaveTypeRepository->create($input);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $root_org = \App\Models\OrganisationStructure::findRootOrg($org_id);
            $leave_types = \App\Models\LeaveType::where('org_id', $root_org)->get();

            $alert = 'Leave Type saved successfully.';
            return redirect(route('leaveTypes.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('leave_types.index')->with(['organisationStructure' => $organisationStructure, 'leave_type' => $leave_types, 'validator' => $validator, 'alert' => $alert, 'view' => 'leavetype']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified LeaveType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $leaveType = $this->leaveTypeRepository->findWithoutFail($id);
//
//        if (empty($leaveType)) {
//            Flash::error('Leave Type not found');
//
//            return redirect(route('leaveTypes.index'));
//        }
//
//        return view('leave_types.show')->with('leaveType', $leaveType);
    }

    /**
     * Show the form for editing the specified LeaveType.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $validator = \JsValidator::make($this->validationRules);
        $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();
        $leaveType = $this->leaveTypeRepository->findWithoutFail($id);


        return view('leave_types.edit')->with(['leaveType' => $leaveType, 'organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'leavetype']);
    }

    /**
     * Update the specified LeaveType in storage.
     *
     * @param  int              $id
     * @param UpdateLeaveTypeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLeaveTypeRequest $request) {
        $leaveType = $this->leaveTypeRepository->findWithoutFail($id);

        if (empty($leaveType)) {
            Flash::error('Leave Type not found');

            return redirect(route('leaveTypes.index'));
        }

        $leaveType = $this->leaveTypeRepository->update($request->all(), $id);

        Flash::success('Leave Type updated successfully.');
        $org_id = $request['org_id'];
        return redirect(route('leaveTypes.index', ['org_id' => $org_id]));
    }

    /**
     * Remove the specified LeaveType from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id, Request $request) {

        if ((\Request::ajax()) && (($request['org_id']) == \App\Models\OrganisationStructure::findRootOrg($request['org_id']))) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            // pass the org_id with a tricky way
            $array['id'] = $id;
            $array['org_id'] = $org_id;

            $leaveType = $this->leaveTypeRepository->delete($array);
            //get the new setting_id


            $alert = 'Leave Type deleted successfully.';
            return redirect(route('leaveTypes.index', ['org_id' => $org_id]))->with('status', $alert);
        } else {
            return view('errors.403');
        }
    }

}
