<?php

use Faker\Factory as Faker;
use App\Models\OrganisationStructure;
use App\Repositories\OrganisationStructureRepository;

trait MakeOrganisationStructureTrait
{
    /**
     * Create fake instance of OrganisationStructure and save it in database
     *
     * @param array $organisationStructureFields
     * @return OrganisationStructure
     */
    public function makeOrganisationStructure($organisationStructureFields = [])
    {
        /** @var OrganisationStructureRepository $organisationStructureRepo */
        $organisationStructureRepo = App::make(OrganisationStructureRepository::class);
        $theme = $this->fakeOrganisationStructureData($organisationStructureFields);
        return $organisationStructureRepo->create($theme);
    }

    /**
     * Get fake instance of OrganisationStructure
     *
     * @param array $organisationStructureFields
     * @return OrganisationStructure
     */
    public function fakeOrganisationStructure($organisationStructureFields = [])
    {
        return new OrganisationStructure($this->fakeOrganisationStructureData($organisationStructureFields));
    }

    /**
     * Get fake data of OrganisationStructure
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrganisationStructureData($organisationStructureFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            '_lft' => $fake->randomDigitNotNull,
            '_rgt' => $fake->randomDigitNotNull,
            'parent_id' => $fake->randomDigitNotNull,
            'setting_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $organisationStructureFields);
    }
}
