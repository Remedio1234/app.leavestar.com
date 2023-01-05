<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrganisationUserAPIRequest;
use App\Http\Requests\API\UpdateOrganisationUserAPIRequest;
use App\Models\OrganisationUser;
use App\Repositories\OrganisationUserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class OrganisationUserController
 * @package App\Http\Controllers\API
 */

class OrganisationUserAPIController extends AppBaseController
{
    /** @var  OrganisationUserRepository */
    private $organisationUserRepository;

    public function __construct(OrganisationUserRepository $organisationUserRepo)
    {
        $this->organisationUserRepository = $organisationUserRepo;
    }

    /**
     * Display a listing of the OrganisationUser.
     * GET|HEAD /organisationUsers
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->organisationUserRepository->pushCriteria(new RequestCriteria($request));
        $this->organisationUserRepository->pushCriteria(new LimitOffsetCriteria($request));
        $organisationUsers = $this->organisationUserRepository->all();

        return $this->sendResponse($organisationUsers->toArray(), 'Organisation Users retrieved successfully');
    }

    /**
     * Store a newly created OrganisationUser in storage.
     * POST /organisationUsers
     *
     * @param CreateOrganisationUserAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateOrganisationUserAPIRequest $request)
    {
        $input = $request->all();

        $organisationUsers = $this->organisationUserRepository->create($input);

        return $this->sendResponse($organisationUsers->toArray(), 'Organisation User saved successfully');
    }

    /**
     * Display the specified OrganisationUser.
     * GET|HEAD /organisationUsers/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var OrganisationUser $organisationUser */
        $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

        if (empty($organisationUser)) {
            return $this->sendError('Organisation User not found');
        }

        return $this->sendResponse($organisationUser->toArray(), 'Organisation User retrieved successfully');
    }

    /**
     * Update the specified OrganisationUser in storage.
     * PUT/PATCH /organisationUsers/{id}
     *
     * @param  int $id
     * @param UpdateOrganisationUserAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrganisationUserAPIRequest $request)
    {
        $input = $request->all();

        /** @var OrganisationUser $organisationUser */
        $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

        if (empty($organisationUser)) {
            return $this->sendError('Organisation User not found');
        }

        $organisationUser = $this->organisationUserRepository->update($input, $id);

        return $this->sendResponse($organisationUser->toArray(), 'OrganisationUser updated successfully');
    }

    /**
     * Remove the specified OrganisationUser from storage.
     * DELETE /organisationUsers/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var OrganisationUser $organisationUser */
        $organisationUser = $this->organisationUserRepository->findWithoutFail($id);

        if (empty($organisationUser)) {
            return $this->sendError('Organisation User not found');
        }

        $organisationUser->delete();

        return $this->sendResponse($id, 'Organisation User deleted successfully');
    }
}
