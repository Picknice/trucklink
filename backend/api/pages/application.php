<?php
$user = user();
$application_id = (int) param('application_id');
$user_id = $user['user_id'];
$from = protectedData(param('from',''));
$to = protectedData(param('to', ''));
$status = (int) param('status');
$transport_type = (int) param('transport_type');
$loading_method = (int) param('loading_method');
$size = (int) param('size');
$method = (int) param('method');
$price_min = (float) param('price_min');
$price_max = (float) param('price_max');
$order_by = ['application_id', 'DESC'];
$begin_date = param('begin_date', false);
$end_date = param('end_date', false);
if($begin_date){
    $begin_date = protectedData($begin_date);
}
if($end_date){
    $end_date = protectedData($end_date);
}

$offset = intval(param('offset'));
$limit = intval(param('limit'));
if($limit < 10){
    $limit = 10;
}
$params = [
    'application_id' => $application_id,
    'user_id' => $user_id,
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
    $application['status_id'] = $application['status'];
    $application['status'] = parseDd('status', 'status_id', $application['status']);
    $application['size'] = parseDd('size', 'size_id', $application['size']);
    $application['price_value'] = intval($application['price']);
    $application['price'] = NormalizeView::checkPrice($application['price'], $application['method']);
    $application['count_unread_messages'] = count(Chat::getUnreadMessagesFor($application['application_id'], $application['user_id'], true));
    $application['map_exists'] = $application['order_id'] && $application['pickup_geo'] && $application['deliver_geo'];
    $data[] = $application;
}
return $data;