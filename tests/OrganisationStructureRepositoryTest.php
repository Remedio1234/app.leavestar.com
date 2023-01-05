<?php

use App\Models\OrganisationStructure;
use App\Repositories\OrganisationStructureRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrganisationStructureRepositoryTest extends TestCase
{
    use MakeOrganisationStructureTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var OrganisationStructureRepository
     */
    protected $organisationStructureRepo;

    public function setUp()
    {
        parent::setUp();
        $this->organisationStructureRepo = App::make(OrganisationStructureRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateOrganisationStructure()
    {
        $organisationStructure = $this->fakeOrganisationStructureData();
        $createdOrganisationStructure = $this->organisationStructureRepo->create($organisationStructure);
        $createdOrganisationStructure = $createdOrganisationStructure->toArray();
        $this->assertArrayHasKey('id', $createdOrganisationStructure);
        $this->assertNotNull($createdOrganisationStructure['id'], 'Created OrganisationStructure must have id specified');
        $this->assertNotNull(OrganisationStructure::find($createdOrganisationStructure['id']), 'OrganisationStructure with given id must be in DB');
        $this->assertModelData($organisationStructure, $createdOrganisationStructure);
    }

    /**
     * @test read
     */
    public function testReadOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $dbOrganisationStructure = $this->organisationStructureRepo->find($organisationStructure->id);
        $dbOrganisationStructure = $dbOrganisationStructure->toArray();
        $this->assertModelData($organisationStructure->toArray(), $dbOrganisationStructure);
    }

    /**
     * @test update
     */
    public function testUpdateOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $fakeOrganisationStructure = $this->fakeOrganisationStructureData();
        $updatedOrganisationStructure = $this->organisationStructureRepo->update($fakeOrganisationStructure, $organisationStructure->id);
        $this->assertModelData($fakeOrganisationStructure, $updatedOrganisationStructure->toArray());
        $dbOrganisationStructure = $this->organisationStructureRepo->find($organisationStructure->id);
        $this->assertModelData($fakeOrganisationStructure, $dbOrganisationStructure->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteOrganisationStructure()
    {
        $organisationStructure = $this->makeOrganisationStructure();
        $resp = $this->organisationStructureRepo->delete($organisationStructure->id);
        $this->assertTrue($resp);
        $this->assertNull(OrganisationStructure::find($organisationStructure->id), 'OrganisationStructure should not exist in DB');
    }
}
