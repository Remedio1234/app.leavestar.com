<?php

use Faker\Factory as Faker;
use App\Models\OrganisationUser;
use App\Repositories\OrganisationUserRepository;

trait MakeOrganisationUserTrait
{
    /**
     * Create fake instance of OrganisationUser and save it in database
     *
     * @param array $organisationUserFields
     * @return OrganisationUser
     */
    public function makeOrganisationUser($organisationUserFields = [])
    {
        /** @var OrganisationUserRepository $organisationUserRepo */
        $organisationUserRepo = App::make(OrganisationUserRepository::class);
        $theme = $this->fakeOrganisationUserData($organisationUserFields);
        return $organisationUserRepo->create($theme);
    }

    /**
     * Get fake instance of OrganisationUser
     *
     * @param array $organisationUserFields
     * @return OrganisationUser
     */
    public function fakeOrganisationUser($organisationUserFields = [])
    {
        return new OrganisationUser($this->fakeOrganisationUserData($organisationUserFields));
    }

    /**
     * Get fake data of OrganisationUser
     *
     * @param array $postFields
     * @return array
     */
    public function fakeOrganisationUserData($organisationUserFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'org_str_id' => $fake->randomDigitNotNull,
            'user_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $organisationUserFields);
    }
}
