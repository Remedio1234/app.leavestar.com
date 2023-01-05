<?php

use App\Models\OrganisationUser;
use App\Repositories\OrganisationUserRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationUserRepositoryTest extends TestCase
{
    use MakeOrganisationUserTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OrganisationUserRepository
     */
    protected $organisationUserRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organisationUserRepo = App::make(OrganisationUserRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOrganisationUser()
    {
        $organisationUser = $this->fakeOrganisationUserData();
        $createdOrganisationUser = $this->organisationUserRepo->create($organisationUser);
        $createdOrganisationUser = $createdOrganisationUser->toArray();
        $this->assertArrayHasKey('id', $createdOrganisationUser);
        $this->assertNotNull($createdOrganisationUser['id'], 'Created OrganisationUser must have id specified');
        $this->assertNotNull(OrganisationUser::find($createdOrganisationUser['id']), 'OrganisationUser with given id must be in DB');
        $this->assertModelData($organisationUser, $createdOrganisationUser);
    }

    /**
     * @test read
     */
    public function testReadOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $dbOrganisationUser = $this->organisationUserRepo->find($organisationUser->id);
        $dbOrganisationUser = $dbOrganisationUser->toArray();
        $this->assertModelData($organisationUser->toArray(), $dbOrganisationUser);
    }

    /**
     * @test update
     */
    public function testUpdateOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $fakeOrganisationUser = $this->fakeOrganisationUserData();
        $updatedOrganisationUser = $this->organisationUserRepo->update($fakeOrganisationUser, $organisationUser->id);
        $this->assertModelData($fakeOrganisationUser, $updatedOrganisationUser->toArray());
        $dbOrganisationUser = $this->organisationUserRepo->find($organisationUser->id);
        $this->assertModelData($fakeOrganisationUser, $dbOrganisationUser->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOrganisationUser()
    {
        $organisationUser = $this->makeOrganisationUser();
        $resp = $this->organisationUserRepo->delete($organisationUser->id);
        $this->assertTrue($resp);
        $this->assertNull(OrganisationUser::find($organisationUser->id), 'OrganisationUser should not exist in DB');
    }
}
