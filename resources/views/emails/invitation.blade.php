
<?php
$org = \App\Models\OrganisationStructure::where(['id' => $from->org_str_id])->first();
$user = \App\User::where(['id' => $from->user_id])->first();
?>
<p>Hi <?= ' ' . $user_register->name ?>,</p>

<p>You have been invited to LeaveStar by <b><?= $user->name ?></b> from <b><?= $org->name ?></b>. Please redirect to http://app.leavestar.com/userRegisters/registerFromToken/<?= $user_register->token ?> to complete the registration.
    If you get any other question, or failed to complete the registration procedure, please contact our support team support@leavestar.com</p>

<p>Best Regards,</p>
<p>LeaveStar Support Team</p>


<img src="<?= URL::to('/') . '/images/leavestar-reverse.svg' ?>" alt="LEAVESTAR">


