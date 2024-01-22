<?php
$user = user();
if($user['user_type_id'] == 1){
    return apiError('No rights');
}
$application_id = (int) $_POST['application_id'];
$user_id = (int) $_POST['user_id'];
$from = protectedData($_POST['from']);
$to = protectedData($_POST['to']);
$status = (int) $_POST['status'];
$transport_type = (int) $_POST['transport_type'];
$loading_method = (int) $_POST['loading_method'];
$size = (int) $_POST['size'];
$method = (int) $_POST['method'];
$price_min = (float) $_POST['price_min'];
$price_max = (float) $_POST['price_max'];
$order_by = ['application_id', 'DESC'];
$begin_date = isset($_POST['begin_date']) ? protectedData($_POST['begin_date']) : false;
$end_date = isset($_POST['end_date']) ? protectedData($_POST['end_date']) : false;

$offset = intval(param('offset'));
$limit = intval(param('limit'));
if($limit < 10){
    $limit = 10;
}

$statuses = db()->query("SELECT * FROM status")->fetch_all(MYSQLI_ASSOC);
$statuses = is_array($statuses) ? array_column($statuses, null, 'status_id') : [];
$params = [
    'application_id' => $application_id,
    'status' => $status,
    'transport_type' => $transport_type,
    'loading_method' => $loading_method,
    'size' => $size,
    'method' => $method,
    'is_deleted' => 0
];

$params_more = [
    'price' => $price_max
];

$params_less = [
    'price' => $price_min
];

$params_like = [
    'from' => $from,
    'to' => $to,
];

$where_params = whereParams($params, $params_more, $params_less, $params_like);
if($begin_date){
    $begin_date = intval(str_replace('-','', $begin_date));
    $where_params .= ($where_params ? ' AND' : ' WHERE') . " CONVERT(date, UNSIGNED INTEGER) >= '$begin_date' ";
    if($end_date){
        $end_date = intval(str_replace('-','', $end_date));
        $where_params .= " AND CONVERT(date, UNSIGNED INTEGER) <= '$end_date' ";
    }
}
if ($order_by) $where_params .= orderBy($order_by);

if ($limit) {
    $where_params .= " LIMIT $limit";

    if ($offset) {
        $where_params .= " OFFSET $offset";
    }
}
$applications = Application::get($where_params);
$data = [];

while ($application = $applications->fetch_assoc()) {
    $application['date'] = NormalizeView::date($application['date']);
    $application['transport_type'] = parseDd('transport_type', 'transport_type_id', $application['transport_type']);
    $application['status'] = parseDd('status', 'status_id', $application['status']);
    $application['size'] = parseDd('size', 'size_id', $application['size']);
    $application['price'] = NormalizeView::checkPrice($application['price'], $application['method']);
    $application['count_unread_messages'] = count(Chat::getUnreadMessagesFor($application['application_id'], $application['user_id'], false));
    $application['map_exists'] = $application['order_id'] && $application['pickup_geo'] && $application['deliver_geo'];
    $data[] = $application;
}
return [
    'statuses' => $statuses,
    'applications' => $data
];