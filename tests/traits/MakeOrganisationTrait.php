<?php

use Faker\Factory as Faker;
use App\Models\Organisation;
use App\Repositories\OrganisationRepository;

trait MakeOrganisationTrait
{
    /**
     * Create fake instance of Organisation and save it in database
     *
     * @param array $organisationFields
     * @return Organisation
     */
    public function makeOrganisation($organisationFields = [])
    {
        /** @var OrganisationRepository $organisationRepo */
        $organisationRepo = App::make(OrganisationRepository::class);
        $theme = $this->fakeOrganisationData($organisationFields);
        return $organisationRepo->create($theme);
    }

    /**
     * Get fake instance of Organisation
     *
     * @param array $organisationFields
     * @return Organisation
     */
    public function fakeOrganisation($organisationFields = [])
    {
        return new Organisation($this->fakeOrganisationData($organisationFields));
    }

    /**
     * Get fake data of Organisation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrganisationData($organisationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'name' => $fake->word,
            'parent_node' => $fake->randomDigitNotNull,
            'left_node' => $fake->randomDigitNotNull,
            'right_node' => $fake->randomDigitNotNull,
            'ansestor_node' => $fake->randomDigitNotNull,
            'level' => $fake->randomDigitNotNull,
            'setting_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $organisationFields);
    }
}
