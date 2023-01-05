<?php

use App\Models\Organisation;
use App\Repositories\OrganisationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationRepositoryTest extends TestCase
{
    use MakeOrganisationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OrganisationRepository
     */
    protected $organisationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organisationRepo = App::make(OrganisationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOrganisation()
    {
        $organisation = $this->fakeOrganisationData();
        $createdOrganisation = $this->organisationRepo->create($organisation);
        $createdOrganisation = $createdOrganisation->toArray();
        $this->assertArrayHasKey('id', $createdOrganisation);
        $this->assertNotNull($createdOrganisation['id'], 'Created Organisation must have id specified');
        $this->assertNotNull(Organisation::find($createdOrganisation['id']), 'Organisation with given id must be in DB');
        $this->assertModelData($organisation, $createdOrganisation);
    }

    /**
     * @test read
     */
    public function testReadOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $dbOrganisation = $this->organisationRepo->find($organisation->id);
        $dbOrganisation = $dbOrganisation->toArray();
        $this->assertModelData($organisation->toArray(), $dbOrganisation);
    }

    /**
     * @test update
     */
    public function testUpdateOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $fakeOrganisation = $this->fakeOrganisationData();
        $updatedOrganisation = $this->organisationRepo->update($fakeOrganisation, $organisation->id);
        $this->assertModelData($fakeOrganisation, $updatedOrganisation->toArray());
        $dbOrganisation = $this->organisationRepo->find($organisation->id);
        $this->assertModelData($fakeOrganisation, $dbOrganisation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOrganisation()
    {
        $organisation = $this->makeOrganisation();
        $resp = $this->organisationRepo->delete($organisation->id);
        $this->assertTrue($resp);
        $this->assertNull(Organisation::find($organisation->id), 'Organisation should not exist in DB');
    }
}
