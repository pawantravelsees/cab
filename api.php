<?php
require './helper/db.php';
require './helper/cabbazar.php';
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    $db = new db();
    $cb = new cabbazar();
    header('Content-Type: application/json');
    switch ($action) {
        case "request_cities":
            // $db->insert_cities();
            break;
        case "suggestion":
            $query = $_POST['query'];
            $data = $db->suggestion($query);
            echo json_encode($data);
            break;
        case "get_scp":
            $scp = $db->get_scp($_POST['sid']);
            echo json_encode($scp);
            break;
        case "localCity":
            $data = $db->get_local_cities();
            echo json_encode($data);
            break;
        case "get_local_airport_cities":
            $airport = $_REQUEST['airportId'];
            $airportId = $db->get_airport_id($airport);
            $requestCities = [
                "airportId" => $airportId['airport_id'],
                "fareType" => strtolower($_REQUEST['fareType'])
            ];
            $localCities = $cb->get_local_airport_cities($requestCities);
            echo json_encode($localCities);
            break;
        default:
            echo json_encode([
                "status" => false,
                "msg" => "somthing went wrong , please try again"
            ]);
            break;
    }
}
