<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationUserApiTest extends TestCase
{
    use MakeOrganisationUserTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateOrganisationUser()
    {
        $organisationUser = $this->fakeOrganisationUserData();
        $this->json('POST', '/api/v1/organisationUsers', $organisationUser);

        $this->assertApiResponse($organisationUser);
    }

    /**
     * @test
     */
    public function testReadOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $this->json('GET', '/api/v1/organisationUsers/'.$organisationUser->id);

        $this->assertApiResponse($organisationUser->toArray());
    }

    /**
     * @test
     */
    public function testUpdateOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $editedOrganisationUser = $this->fakeOrganisationUserData();

        $this->json('PUT', '/api/v1/organisationUsers/'.$organisationUser->id, $editedOrganisationUser);

        $this->assertApiResponse($editedOrganisationUser);
    }

    /**
     * @test
     */
    public function testDeleteOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $this->json('DELETE', '/api/v1/organisationUsers/'.$organisationUser->iidd);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/organisationUsers/'.$organisationUser->id);

        $this->assertResponseStatus(404);
    }
}
