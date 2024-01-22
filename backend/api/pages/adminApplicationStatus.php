<?php
$user = user();
if($user['user_type_id'] == 1){
    return apiError('No rights');
}
$applicationId = intval(param('application_id'));
$statusId = intval(param('status_id'));
$application = db()->query("SELECT * FROM application WHERE application_id=?", $applicationId)->fetch_assoc();
if(!$application){
    return apiError('No application');
}
$status = db()->query("SELECT * FROM status WHERE status_id=?", $statusId)->fetch_assoc();
if(!$status){
    return apiError('No status');
}
if($application['status'] == $status['status_id']){
    return apiResult(true);
}
return db()->query("UPDATE application SET status=? WHERE application_id=?", $status['status_id'], $application['application_id']);