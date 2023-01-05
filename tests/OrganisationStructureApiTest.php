<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationStructureApiTest extends TestCase
{
    use MakeOrganisationStructureTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOrganisationStructure()
    {
        $organisationStructure = $this->fakeOrganisationStructureData();
        $this->json('POST', '/api/v1/organisationStructures', $organisationStructure);

        $this->assertApiResponse($organisationStructure);
    }

    /**
     * @test
     */
    public function testReadOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $this->json('GET', '/api/v1/organisationStructures/'.$organisationStructure->id);

        $this->assertApiResponse($organisationStructure->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $editedOrganisationStructure = $this->fakeOrganisationStructureData();

        $this->json('PUT', '/api/v1/organisationStructures/'.$organisationStructure->id, $editedOrganisationStructure);

        $this->assertApiResponse($editedOrganisationStructure);
    }

    /**
     * @test
     */
    public function testDeleteOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $this->json('DELETE', '/api/v1/organisationStructures/'.$organisationStructure->iidd);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/organisationStructures/'.$organisationStructure->id);

        $this->assertResponseStatus(404);
    }
}
