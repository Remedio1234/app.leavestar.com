<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationApiTest extends TestCase
{
    use MakeOrganisationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOrganisation()
    {
        $organisation = $this->fakeOrganisationData();
        $this->json('POST', '/api/v1/organisations', $organisation);

        $this->assertApiResponse($organisation);
    }

    /**
     * @test
     */
    public function testReadOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $this->json('GET', '/api/v1/organisations/'.$organisation->id);

        $this->assertApiResponse($organisation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $editedOrganisation = $this->fakeOrganisationData();

        $this->json('PUT', '/api/v1/organisations/'.$organisation->id, $editedOrganisation);

        $this->assertApiResponse($editedOrganisation);
    }

    /**
     * @test
     */
    public function testDeleteOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $this->json('DELETE', '/api/v1/organisations/'.$organisation->iidd);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/organisations/'.$organisation->id);

        $this->assertResponseStatus(404);
    }
}
