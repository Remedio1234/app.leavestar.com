<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRegisterCapacityRequest;
use App\Http\Requests\UpdateRegisterCapacityRequest;
use App\Repositories\RegisterCapacityRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class RegisterCapacityController extends AppBaseController
{
    /** @var  RegisterCapacityRepository */
    private $registerCapacityRepository;

    public function __construct(RegisterCapacityRepository $registerCapacityRepo)
    {
        $this->registerCapacityRepository = $registerCapacityRepo;
    }

    /**
     * Display a listing of the RegisterCapacity.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->registerCapacityRepository->pushCriteria(new RequestCriteria($request));
        $registerCapacities = $this->registerCapacityRepository->all();

        return view('register_capacities.index')
            ->with('registerCapacities', $registerCapacities);
    }

    /**
     * Show the form for creating a new RegisterCapacity.
     *
     * @return Response
     */
    public function create()
    {
        return view('register_capacities.create');
    }

    /**
     * Store a newly created RegisterCapacity in storage.
     *
     * @param CreateRegisterCapacityRequest $request
     *
     * @return Response
     */
    public function store(CreateRegisterCapacityRequest $request)
    {
        $input = $request->all();

        $registerCapacity = $this->registerCapacityRepository->create($input);

        Flash::success('Register Capacity saved successfully.');

        return redirect(route('registerCapacities.index'));
    }

    /**
     * Display the specified RegisterCapacity.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $registerCapacity = $this->registerCapacityRepository->findWithoutFail($id);

        if (empty($registerCapacity)) {
            Flash::error('Register Capacity not found');

            return redirect(route('registerCapacities.index'));
        }

        return view('register_capacities.show')->with('registerCapacity', $registerCapacity);
    }

    /**
     * Show the form for editing the specified RegisterCapacity.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $registerCapacity = $this->registerCapacityRepository->findWithoutFail($id);

        if (empty($registerCapacity)) {
            Flash::error('Register Capacity not found');

            return redirect(route('registerCapacities.index'));
        }

        return view('register_capacities.edit')->with('registerCapacity', $registerCapacity);
    }

    /**
     * Update the specified RegisterCapacity in storage.
     *
     * @param  int              $id
     * @param UpdateRegisterCapacityRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRegisterCapacityRequest $request)
    {
        $registerCapacity = $this->registerCapacityRepository->findWithoutFail($id);

        if (empty($registerCapacity)) {
            Flash::error('Register Capacity not found');

            return redirect(route('registerCapacities.index'));
        }

        $registerCapacity = $this->registerCapacityRepository->update($request->all(), $id);

        Flash::success('Register Capacity updated successfully.');

        return redirect(route('registerCapacities.index'));
    }

    /**
     * Remove the specified RegisterCapacity from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $registerCapacity = $this->registerCapacityRepository->findWithoutFail($id);

        if (empty($registerCapacity)) {
            Flash::error('Register Capacity not found');

            return redirect(route('registerCapacities.index'));
        }

        $this->registerCapacityRepository->delete($id);

        Flash::success('Register Capacity deleted successfully.');

        return redirect(route('registerCapacities.index'));
    }
}
