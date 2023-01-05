<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOpenHourRequest;
use App\Http\Requests\UpdateOpenHourRequest;
use App\Repositories\OpenHourRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class OpenHourController extends AppBaseController {

    /** @var  OpenHourRepository */
    private $openHourRepository;
    protected $validationRules = [
        'dayOfWeek' => 'required',
    ];

    public function __construct(OpenHourRepository $openHourRepo) {
        $this->middleware('auth');
        $this->openHourRepository = $openHourRepo;
    }

    /**
     * Display a listing of the OpenHour.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            $setting_id = $organisationStructure->setting_id;
            $openHours = \App\Models\OpenHour::where('setting_id', $setting_id)->orderby('dayOfWeek')->get();

            return view('open_hours.index')
                            ->with(['organisationStructure' => $organisationStructure, 'openHours' => $openHours, 'validator' => $validator, 'view' => 'openhour']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new OpenHour.
     *
     * @return Response
     */
    public function create(Request $request) {
        $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();
        $open_hours = \App\Models\OpenHour::where('setting_id', $organisationStructure->setting_id)->get();
        $set = [];
        $weeklist = [
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thurday',
            '5' => 'Friday',
            '6' => 'Saturday',
            '7' => 'Sunday',
        ];
        foreach ($open_hours as $item) {
            if (array_key_exists($item->dayOfWeek, $weeklist)) {
                unset($weeklist[$item->dayOfWeek]);
            }
        }
        if (sizeof($weeklist) > 0) {
            $org_id = $request->session()->get('current_org');
            if (\App\User::checkUserRole((\Auth::user()->id), $org_id)) {
                $validator = \JsValidator::make($this->validationRules);

                $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

                return view('open_hours.create')
                                ->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'openhour']);
            } else {
                return view('errors.403');
            }
        } else {
            Flash::error('Can not add more opening hour setting.');
            return redirect(route('openHours.index', ['org_id' => $request['org_id']]));
        }
    }

    /**
     * Store a newly created OpenHour in storage.
     *
     * @param CreateOpenHourRequest $request
     *
     * @return Response
     */
    public function store(CreateOpenHourRequest $request) {

        if (\Request::ajax()) {

            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            //create the new leave type and list it 
            $input = $request->all();
            $this->openHourRepository->create($input);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $openHours = \App\Models\OpenHour::where('setting_id', $setting_id)->get();

            $alert = 'Open Hour Rule saved successfully.';
            return redirect(route('openHours.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('open_hours.index')->with(['organisationStructure' => $organisationStructure, 'openHours' => $openHours, 'validator' => $validator, 'alert' => $alert, 'view' => 'openhour']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified OpenHour.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $openHour = $this->openHourRepository->findWithoutFail($id);
//
//        if (empty($openHour)) {
//            Flash::error('Open Hour not found');
//
//            return redirect(route('openHours.index'));
//        }
//
//        return view('open_hours.show')->with('openHour', $openHour);
    }

    /**
     * Show the form for editing the specified OpenHour.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\Models\OpenHour::checkBelonging($id, $request['org_id'])) {
            $validator = \JsValidator::make($this->validationRules);
            $openHour = $this->openHourRepository->findWithoutFail($id);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('open_hours.edit')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'openHour' => $openHour, 'view' => 'openhour']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified OpenHour in storage.
     *
     * @param  int              $id
     * @param UpdateOpenHourRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOpenHourRequest $request) {

        if (\Request::ajax()) {
            $org_id = $request['org_id'];
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $openhours = \App\Models\OpenHour::where('id', '!=', $id)->where(['dayOfWeek' => $request['dayOfWeek'], 'setting_id' => $organisationStructure->setting_id])->first();
            
            if (!isset($openhours)) {
                $this->openHourRepository->update($request->all(), $id);

                $alert = 'Open Hour Rule updated successfully.';
                return redirect(route('openHours.index', ['org_id' => $org_id]))->with('status', $alert);
            } else {
                Flash::error('Open Hour Setting Conflict');
                return redirect(route('openHours.index', ['org_id' => $org_id]));
            }
//return view('open_hours.index')->with(['organisationStructure' => $organisationStructure, 'openHours' => $openHour, 'validator' => $validator, 'alert' => $alert, 'view' => 'openhour']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified OpenHour from storage.
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
            $this->openHourRepository->delete($array);

            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $openhour = \App\Models\OpenHour::where('setting_id', $setting_id)->get();


            $alert = 'Open Hour Rule deleted successfully.';
            return redirect(route('openHours.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('open_hours.index')->with(['organisationStructure' => $organisationStructure, 'openHours' => $openhour, 'validator' => $validator, 'alert' => $alert, 'view' => 'openhour']);
        } else {
            return view('errors.403');
        }
    }

}
