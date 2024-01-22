<?php
class CargoApi
{
    private $api, $key;
    public function __construct($key)
    {
        $this->api = 'https://cnu.cargoetl.com/jsonrpc/brokerage';
        $this->key = $key;
    }
    public function makePostLoad($name, $email, $phone, $from, $to, $date, $size, $count, $weight, $transport, $price = null, $comment = null)
    {
        $size = array_map(function($item){
            return intval($item);
        }, explode("X", $size));
        $params = [
            'name' => $name,
            'email' => $email,
            'phone' => (mb_strpos($phone, '+') === false ? '+' : '') . $phone,
            'ASAP' => false,
            'pickUpAt' => $from,
            'deliverTo' => $to,
            'pickUpDate' => $date,
            'direct' => true,
            'dims' => $size,
            'pieces' => intval($count),
            'weight' => intval($weight),
            'hazardous' => false,
            'stackable' => true,
            'dockLevel' => true,
            'vehicleType' => $transport
        ];
        if($price !== null){
            $params['pays'] = intval($price);
        }
        if($comment !== null){
            $params['note'] = $comment;
        }
        $params['method'] = 'postLoad';
        return $params;
    }
    public function getLoads($ids)
    {
        if(!is_array($ids)){
            return $this->query('getLoad', [
                'id' => $ids
            ]);
        }
        $queries = [];
        foreach($ids as $k => $id){
            $queries[$k] = [
                'method' => 'getLoad',
                'id' => $id
            ];
        }
        return $this->multiQuery($queries);
    }
    public function getVehicles()
    {
        return $this->query('getVehicles');
    }
    public function query($method, array $params = [])
    {
        $params['method'] = $method;
        $result = $this->multiQuery([
            $params
        ]);
        return $result && is_array($result) ? $result[0] : false;
    }
    public function multiQuery(array $queries)
    {
        $requests = [];
        foreach($queries as $requestId => $params){
            $method = isset($params['method']) ? $params['method'] : '';
            unset($params['method']);
            $request = [
                'jsonrpc' => '2.0',
                'id' => $requestId === 0 ? strval($requestId) : $requestId,
                'method' => $method
            ];
            if(count($params)){
                $request['params'] = $params;
            }
            $requests[] = $request;
        }
        $curl = curl_init($this->api);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . base64_encode($this->key)
        ]);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($requests));
        $queries = curl_exec($curl);
        $results = [];
        if($queries){
            $queries = @json_decode($queries, true);
            if(is_array($queries)){
                foreach($queries as $query){
                    if(is_array($query)){
                        $results[$query['id']==0 ? intval($query['id']) : $query['id']] = isset($query['result']) ? $query['result'] : $query['error'];
                    }
                }
            }
        }
        return $results;
    }
}