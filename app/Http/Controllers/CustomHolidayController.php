<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCustomHolidayRequest;
use App\Http\Requests\UpdateCustomHolidayRequest;
use App\Repositories\CustomHolidayRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class CustomHolidayController extends AppBaseController {

    /** @var  CustomHolidayRepository */
    protected $validationRules = [
        'name' => 'required',
        'date' => 'required',
    ];
    private $customHolidayRepository;

    public function __construct(CustomHolidayRepository $customHolidayRepo) {
        $this->middleware('auth');
        $this->customHolidayRepository = $customHolidayRepo;
    }

    /**
     * Display a listing of the CustomHoliday.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            $setting_id = $organisationStructure->setting_id;
            $custom_holidays = \App\Models\CustomHoliday::where('setting_id', $setting_id)->get();

            return view('custom_holidays.index')
                            ->with(['organisationStructure' => $organisationStructure, 'custom_holidays' => $custom_holidays, 'validator' => $validator, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Show the form for creating a new CustomHoliday.
     *
     * @return Response
     */
    public function create(Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\User::checkUserRole((\Auth::user()->id), $org_id)) {
            $validator = \JsValidator::make($this->validationRules);
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('custom_holidays.create')
                            ->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Store a newly created CustomHoliday in storage.
     *
     * @param CreateCustomHolidayRequest $request
     *
     * @return Response
     */
    public function store(CreateCustomHolidayRequest $request) {
        if (\Request::ajax()) {
            //get organisation and validator
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];
            //create the new leave type and list it 
            $input = $request->all();

            $this->customHolidayRepository->create($input);
            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $custom_holidays = \App\Models\CustomHoliday::where('setting_id', $setting_id)->get();

            $alert = 'Custom Holiday created successfully.';
            return redirect(route('customHolidays.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('custom_holidays.index')->with(['organisationStructure' => $organisationStructure, 'custom_holidays' => $custom_holidays, 'validator' => $validator, 'alert' => $alert, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Display the specified CustomHoliday.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        return view('errors.403');
//        $customHoliday = $this->customHolidayRepository->findWithoutFail($id);
//
//        if (empty($customHoliday)) {
//            Flash::error('Custom Holiday not found');
//
//            return redirect(route('customHolidays.index'));
//        }
//
//        return view('custom_holidays.show')->with('customHoliday', $customHoliday);
    }

    /**
     * Show the form for editing the specified CustomHoliday.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id, Request $request) {
        $org_id = $request->session()->get('current_org');
        if (\App\Models\CustomHoliday::checkBelonging($id, $request['org_id'])) {
            $validator = \JsValidator::make($this->validationRules);
            $customHoliday = $this->customHolidayRepository->findWithoutFail($id);
            $org_id = $request->session()->get('current_org');

            $dates = \App\Models\Setting::getLocalTime($org_id, $customHoliday->start_date);
            $dates2 = \App\Models\Setting::getLocalTime($org_id, $customHoliday->end_date);
            $customHoliday->date_range = $dates . ' - ' . $dates2;

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $request['org_id'])->first();

            return view('custom_holidays.edit')->with(['organisationStructure' => $organisationStructure, 'validator' => $validator, 'custom_holidays' => $customHoliday, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Update the specified CustomHoliday in storage.
     *
     * @param  int              $id
     * @param UpdateCustomHolidayRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCustomHolidayRequest $request) {
        if (\Request::ajax()) {
            $validator = \JsValidator::make($this->validationRules);
            $org_id = $request['org_id'];

            $this->customHolidayRepository->update($request->all(), $id);

            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $custom_holiday = \App\Models\CustomHoliday::where('setting_id', $setting_id)->get();

            $alert = 'Custom Holiday updated successfully.';
            return redirect(route('customHolidays.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('custom_holidays.index')->with(['organisationStructure' => $organisationStructure, 'custom_holidays' => $custom_holiday, 'validator' => $validator, 'alert' => $alert, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

    /**
     * Remove the specified CustomHoliday from storage.
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

            $this->customHolidayRepository->delete($array);

            //get the new setting_id
            $organisationStructure = \App\Models\OrganisationStructure::with('setting')->where('id', $org_id)->first();
            $setting_id = $organisationStructure->setting_id;
            $custom_holidays = \App\Models\CustomHoliday::where('setting_id', $setting_id)->get();


            $alert = 'Custom Holiday deleted successfully.';
            return redirect(route('customHolidays.index', ['org_id' => $org_id]))->with('status', $alert);
//return view('custom_holidays.index')->with(['organisationStructure' => $organisationStructure, 'custom_holidays' => $custom_holidays, 'validator' => $validator, 'alert' => $alert, 'view' => 'custom_holiday']);
        } else {
            return view('errors.403');
        }
    }

}
