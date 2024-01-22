<?php
$user = user();
if($user['user_type_id'] == 1){
    return apiError('No rights');
}
$applicationId = intval(param('application_id'));
$application = db()->query("SELECT * FROM application WHERE application_id=?", $applicationId)->fetch_assoc();
if(!$application){
    return apiError('No application');
}
return db()->query("DELETE FROM application WHERE application_id=?", $application['application_id']);