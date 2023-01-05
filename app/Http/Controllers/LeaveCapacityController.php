<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLeaveCapacityRequest;
use App\Http\Requests\UpdateLeaveCapacityRequest;
use App\Repositories\LeaveCapacityRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LeaveCapacityController extends AppBaseController {

    /** @var  LeaveCapacityRepository */
    private $leaveCapacityRepository;

    public function __construct(LeaveCapacityRepository $leaveCapacityRepo) {
        $this->leaveCapacityRepository = $leaveCapacityRepo;
    }

    /**
     * Display a listing of the LeaveCapacity.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        $this->leaveCapacityRepository->pushCriteria(new RequestCriteria($request));
        $leaveCapacities = $this->leaveCapacityRepository->all();

        return view('leave_capacities.index')
                        ->with('leaveCapacities', $leaveCapacities);
    }

    /**
     * Show the form for creating a new LeaveCapacity.
     *
     * @return Response
     */
    public function create() {
        return view('leave_capacities.create');
    }

    /**
     * Store a newly created LeaveCapacity in storage.
     *
     * @param CreateLeaveCapacityRequest $request
     *
     * @return Response
     */
    public function store(CreateLeaveCapacityRequest $request) {
        $input = $request->all();

        $leaveCapacity = $this->leaveCapacityRepository->create($input);

        Flash::success('Leave Capacity saved successfully.');

        return redirect(route('leaveCapacities.index'));
    }

    /**
     * Display the specified LeaveCapacity.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        $leaveCapacity = $this->leaveCapacityRepository->findWithoutFail($id);

        if (empty($leaveCapacity)) {
            Flash::error('Leave Capacity not found');

            return redirect(route('leaveCapacities.index'));
        }

        return view('leave_capacities.show')->with('leaveCapacity', $leaveCapacity);
    }

    /**
     * Show the form for editing the specified LeaveCapacity.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {
        $leaveCapacity = $this->leaveCapacityRepository->findWithoutFail($id);

        if (empty($leaveCapacity)) {
            Flash::error('Leave Capacity not found');

            return redirect(route('leaveCapacities.index'));
        }

        return view('leave_capacities.edit')->with('leaveCapacity', $leaveCapacity);
    }

    /**
     * Update the specified LeaveCapacity in storage.
     *
     * @param  int              $id
     * @param UpdateLeaveCapacityRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateLeaveCapacityRequest $request) {
        $leaveCapacity = $this->leaveCapacityRepository->findWithoutFail($id);

        if (empty($leaveCapacity)) {
            Flash::error('Leave Capacity not found');

            return redirect(route('leaveCapacities.index'));
        }

        $leaveCapacity = $this->leaveCapacityRepository->update($request->all(), $id);

        Flash::success('Leave Capacity updated successfully.');

        return redirect(route('leaveCapacities.index'));
    }

    /**
     * Remove the specified LeaveCapacity from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id) {
        $leaveCapacity = $this->leaveCapacityRepository->findWithoutFail($id);

        if (empty($leaveCapacity)) {
            Flash::error('Leave Capacity not found');

            return redirect(route('leaveCapacities.index'));
        }

        $this->leaveCapacityRepository->delete($id);

        Flash::success('Leave Capacity deleted successfully.');

        return redirect(route('leaveCapacities.index'));
    }

    public function CheckCapacity($id) {
        if (\Request::ajax()) {
            $array['leavetype'] = \App\Models\LeaveType::find($id)->name;

            $leavecapacity = \App\Models\LeaveCapacity::where([
                        'user_id' => \Auth::user()->id,
                        'org_id' => \Session::get('current_org'),
                        'leave_type_id' => $id
                    ])->first();

            if (isset($leavecapacity)) {
                if ($leavecapacity->capacity > 0) {
                    $array['capacity'] = round($leavecapacity->capacity, 1);
                } else {
                    $array['capacity'] = 0;
                }
            } else {
                $array['capacity'] = 0;
            }
            return $array;
        } else {
            return view('errors.403');
        }
    }

}
