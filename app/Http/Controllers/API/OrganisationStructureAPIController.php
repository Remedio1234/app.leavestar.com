<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateOrganisationStructureAPIRequest;
use App\Http\Requests\API\UpdateOrganisationStructureAPIRequest;
use App\Models\OrganisationStructure;
use App\Repositories\OrganisationStructureRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class OrganisationStructureController
 * @package App\Http\Controllers\API
 */

class OrganisationStructureAPIController extends AppBaseController
{
    /** @var  OrganisationStructureRepository */
    private $organisationStructureRepository;

    public function __construct(OrganisationStructureRepository $organisationStructureRepo)
    {
        $this->organisationStructureRepository = $organisationStructureRepo;
    }

    /**
     * Display a listing of the OrganisationStructure.
     * GET|HEAD /organisationStructures
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->organisationStructureRepository->pushCriteria(new RequestCriteria($request));
        $this->organisationStructureRepository->pushCriteria(new LimitOffsetCriteria($request));
        $organisationStructures = $this->organisationStructureRepository->all();

        return $this->sendResponse($organisationStructures->toArray(), 'Organisation Structures retrieved successfully');
    }

    /**
     * Store a newly created OrganisationStructure in storage.
     * POST /organisationStructures
     *
     * @param CreateOrganisationStructureAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateOrganisationStructureAPIRequest $request)
    {
        $input = $request->all();

        $organisationStructures = $this->organisationStructureRepository->create($input);

        return $this->sendResponse($organisationStructures->toArray(), 'Organisation Structure saved successfully');
    }

    /**
     * Display the specified OrganisationStructure.
     * GET|HEAD /organisationStructures/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var OrganisationStructure $organisationStructure */
        $organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);

        if (empty($organisationStructure)) {
            return $this->sendError('Organisation Structure not found');
        }

        return $this->sendResponse($organisationStructure->toArray(), 'Organisation Structure retrieved successfully');
    }

    /**
     * Update the specified OrganisationStructure in storage.
     * PUT/PATCH /organisationStructures/{id}
     *
     * @param  int $id
     * @param UpdateOrganisationStructureAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrganisationStructureAPIRequest $request)
    {
        $input = $request->all();

        /** @var OrganisationStructure $organisationStructure */
        $organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);

        if (empty($organisationStructure)) {
            return $this->sendError('Organisation Structure not found');
        }

        $organisationStructure = $this->organisationStructureRepository->update($input, $id);

        return $this->sendResponse($organisationStructure->toArray(), 'OrganisationStructure updated successfully');
    }

    /**
     * Remove the specified OrganisationStructure from storage.
     * DELETE /organisationStructures/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var OrganisationStructure $organisationStructure */
        $organisationStructure = $this->organisationStructureRepository->findWithoutFail($id);

        if (empty($organisationStructure)) {
            return $this->sendError('Organisation Structure not found');
        }

        $organisationStructure->delete();

        return $this->sendResponse($id, 'Organisation Structure deleted successfully');
    }
}
