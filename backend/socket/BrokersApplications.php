<?php
use Workerman\Worker;
ini_set('memory_limit', '100M');
require_once __DIR__ . '/require.php';
$worker = new Worker();
$worker->onWorkerStart = function()
{
    $cargoApi = new CargoApi(CARGO_ETL_API_KEY);
    $limit = 1000;
    while(true){
        require __DIR__ . '/../include/connect.php';
        $time = time() - 60;
        $query = db()->query("SELECT application.*, transport_type.name as transport, size.name as size_name FROM application LEFT JOIN transport_type ON application.transport_type = transport_type.transport_type_id LEFT JOIN size ON application.size = size.size_id WHERE status >= 2 AND status < 9 AND order_update < ?", $time);
        $forCreated = [];
        $forStatus = [];
        while($application = $query->fetch_assoc()){
            if($application['status'] == 2 && !$application['order_id']){
                $application['size_name'] = $application['size'] == 4 ? $application['cargo_size'] : $application['size_name'];
                $forCreated[$application['application_id']] = $cargoApi->makePostLoad($application['user_fullname'], $application['user_email'], $application['user_telephone'], $application['from'], $application['to'], date('c', strtotime($application['date'])), $application['size_name'], $application['quantity'], $application['mass'], $application['transport'], $application['price'], mb_strlen($application['comment'] ) ? $application['comment'] : null);
            }else{
                $forStatus[$application['application_id']] = $application['order_id'];
            }
        }
        if(count($forCreated)){
            foreach(array_chunk($forCreated, $limit, true) as $part){
                $orders = $cargoApi->multiQuery($part);
                foreach($orders as $applicationId => $orderId){
                    if(is_array($orderId) && isset($orderId['code'])){
                        continue;
                    }
                    db()->query("UPDATE application SET status = 3, order_id = ?, order_update = ? WHERE application_id = ?", $orderId, time(), $applicationId);
                }
            }
        }
        if(count($forStatus)){
            foreach(array_chunk($forStatus, $limit, true) as $part){
                foreach($cargoApi->getLoads($part) as $id => $application){
                    $pickUpDate = isset($application['pickUpDate']) ? $application['pickUpDate'] : '';
                    $pickUpAtGeo = isset($application['pickUpAtGeo']) ? $application['pickUpAtGeo'] : '';
                    $deliverDate = isset($application['deliverDate']) ? $application['deliverDate'] : '';
                    $deliverToGeo = isset($application['deliverToGeo']) ? $application['deliverToGeo'] : '';
                    $miles = isset($application['miles']) ? $application['miles'] : false;
                    $pays = isset($application['pays']) ? $application['pays'] : false;
                    $deliveryStatus = false;
                    if(isset($application['delivery'])){
                        switch($application['delivery']['status']){
                            case 'assigned';
                                $deliveryStatus = 4;
                            break;
                            case 'drivingToPickup':
                                $deliveryStatus = 5;
                            break;
                            case 'arrivedToPickup':
                            case 'loading':
                                $deliveryStatus = 6;
                            break;
                            case 'inTransit':
                                $deliveryStatus = 7;
                            break;
                            case 'arrivedToDestination':
                            case 'unloading':
                                $deliveryStatus = 8;
                            break;
                            case 'completed':
                                $deliveryStatus = 9;
                            break;
                            case 'trash':
                                $deliveryStatus = 12;
                            break;
                        }
                    }
                    $deliveryGeo = isset($application['delivery']) && is_array($application['delivery']) && is_array($application['delivery']['geo']) ? implode(",", $application['delivery']['geo']) : '';
                    $updates = [
                        'order_update = ?' => time()
                    ];
                    if($pickUpAtGeo){
                        $updates['pickup_geo = ?'] = $pickUpAtGeo;
                    }
                    if($pickUpDate) {
                        $updates['pickup_date = ?'] = $pickUpDate;
                    }
                    if($deliverToGeo) {
                        $updates['deliver_geo = ?'] = $deliverToGeo;
                    }
                    if($deliverDate){
                        $updates['deliver_date = ?'] = $deliverDate;
                    }
                    if($miles) {
                        $updates['miles = ?'] = $miles;
                    }
                    if($deliveryGeo) {
                        $updates['current_geo = ?'] = $deliveryGeo;
                    }
                    if(is_array($application) && isset($application['code']) && $application['code'] == 404){
                        $deliveryStatus = 12;
                    }
                    if($pays !== false){
                        $updates['method = ?'] = 3;
                        $updates['price = ?'] = $pays;
                    }
                    if($deliveryStatus !== false){
                        $updates['status = ?'] = $deliveryStatus;
                    }
                    $sql = 'UPDATE application SET ' . implode(", ", array_keys($updates)) . ' WHERE application_id=?';
                    $updates = array_values($updates);
                    $updates[] = $id;
                    $arguments = array_merge([$sql], $updates);
                    call_user_func_array( [db(), 'query'], $arguments);
                }
            }
        }
        $truck = db()->query("SELECT * FROM trucks WHERE last_update > ? LIMIT 1", $time);
        if(is_object($truck) && !$truck->num_rows){
            $trucks = $cargoApi->getVehicles();
            if(is_array($trucks)){
                db()->query("UPDATE trucks SET enabled=0");
                foreach($trucks as $truck){
                    $name = $truck['make'] . ' ' . $truck['model'];
                    $brokerId = 2;
                    $truckId = $truck['id'];
                    $geo = is_array($truck['availableGeo']) && count($truck['availableGeo']) == 2 ? implode(",", $truck['availableGeo']) : '';
                    $enabled = $truck['status'] == 1 ? 1 : 0;
                    $findTruck = db()->query("SELECT * FROM trucks WHERE broker_id = ? AND truck_id = ?", $brokerId, $truckId)->fetch_assoc();
                    if($findTruck){
                        db()->query("UPDATE trucks SET name = ?, geo = ?, enabled = ?, last_update = ? WHERE id = ? ", $name, $geo, $enabled, time(), $findTruck['id']);
                    }else{
                        db()->query("INSERT INTO trucks (broker_id,truck_id,name,geo,enabled,last_update) VALUES (?,?,?,?,?,?)", $brokerId, $truckId, $name, $geo, $enabled, time());
                    }
                }
            }
        }
        db()->close();
        sleep(10);
    }
};
Worker::runAll();