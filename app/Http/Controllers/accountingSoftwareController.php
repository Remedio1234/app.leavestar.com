<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateaccountingSoftwareRequest;
use App\Http\Requests\UpdateaccountingSoftwareRequest;
use App\Repositories\accountingSoftwareRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class accountingSoftwareController extends AppBaseController {

    /** @var  accountingSoftwareRepository */
    private $accountingSoftwareRepository;

    public function __construct(accountingSoftwareRepository $accountingSoftwareRepo) {
        $this->middleware('auth');
        $this->accountingSoftwareRepository = $accountingSoftwareRepo;
    }

    /**
     * Display a listing of the accountingSoftware.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) {
        $this->accountingSoftwareRepository->pushCriteria(new RequestCriteria($request));
        $accountingSoftwares = $this->accountingSoftwareRepository->all();

        return view('accounting_softwares.index')
                        ->with('accountingSoftwares', $accountingSoftwares);
    }

    /**
     * Show the form for creating a new accountingSoftware.
     *
     * @return Response
     */
    public function create() {
        return view('accounting_softwares.create');
    }

    /**
     * Store a newly created accountingSoftware in storage.
     *
     * @param CreateaccountingSoftwareRequest $request
     *
     * @return Response
     */
    public function store(CreateaccountingSoftwareRequest $request) {
        $input = $request->all();

        $accountingSoftware = $this->accountingSoftwareRepository->create($input);

        Flash::success('Accounting Software saved successfully.');

        return redirect(route('accountingSoftwares.index'));
    }

    /**
     * Display the specified accountingSoftware.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id) {
        $accountingSoftware = $this->accountingSoftwareRepository->findWithoutFail($id);

        if (empty($accountingSoftware)) {
            Flash::error('Accounting Software not found');

            return redirect(route('accountingSoftwares.index'));
        }

        return view('accounting_softwares.show')->with('accountingSoftware', $accountingSoftware);
    }

    /**
     * Show the form for editing the specified accountingSoftware.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id) {
        $accountingSoftware = $this->accountingSoftwareRepository->findWithoutFail($id);

        if (empty($accountingSoftware)) {
            Flash::error('Accounting Software not found');

            return redirect(route('accountingSoftwares.index'));
        }

        return view('accounting_softwares.edit')->with('accountingSoftware', $accountingSoftware);
    }

    /**
     * Update the specified accountingSoftware in storage.
     *
     * @param  int              $id
     * @param UpdateaccountingSoftwareRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateaccountingSoftwareRequest $request) {
        $accountingSoftware = $this->accountingSoftwareRepository->findWithoutFail($id);

        if (empty($accountingSoftware)) {
            Flash::error('Accounting Software not found');

            return redirect(route('accountingSoftwares.index'));
        }

        $accountingSoftware = $this->accountingSoftwareRepository->update($request->all(), $id);

        Flash::success('Accounting Software updated successfully.');

        return redirect(route('accountingSoftwares.index'));
    }

    /**
     * Remove the specified accountingSoftware from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id) {
        $accountingSoftware = $this->accountingSoftwareRepository->findWithoutFail($id);

        if (empty($accountingSoftware)) {
            Flash::error('Accounting Software not found');

            return redirect(route('accountingSoftwares.index'));
        }

        $this->accountingSoftwareRepository->delete($id);

        Flash::success('Accounting Software deleted successfully.');

        return redirect(route('accountingSoftwares.index'));
    }

}
