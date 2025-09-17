<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$pageTitle = "Tripodeal Cabs - Fare ";
// require './inc/head.php';

require 'helper/db.php';
require 'helper/cabbazar.php';
require 'helper/result_helper.php';
$db = new db();
$cb  = new cabbazar();
$pickupAddress;
$pickupLocationPlaceId;
$destinationAddress;
$destinationLocationPlaceId;
$moreCities = [];
$searchRequest = [];

if (isset($_REQUEST['action']) && $_REQUEST['action'] === "outstation") {
    $isReturn = ($_REQUEST['roundTrip'] == "true") ? 1 : 0;
    $total = count($_REQUEST['locs']);
    $pickupLocation = json_decode($_REQUEST['locs'][0], true);
    $destinationLocation = json_decode($_REQUEST['locs'][$total - 1], true);

    if ($total > 2) {
        for ($i = 1; $i < $total - 1; $i++) {
            $loc = json_decode($_REQUEST['locs'][$i], true);
            $moreCities[] = [
                "id"      => $loc['id'],
                "address" => $loc['address']
            ];
        }
    }
    $pickupAddress = $pickupLocation['address'];
    $pickupLocationPlaceId = $pickupLocation['id'];

    $destinationAddress = $destinationLocation['address'];
    $destinationLocationPlaceId = $destinationLocation['id'];

    if (($isReturn == 1) || (sizeof($moreCities) > 0)) {
        $date = strtotime($_REQUEST['arrivalAt']);
        $arrivalAt = date('Y-m-d', $date);
    } else {
        $arrivalAt = "";
    }

    $departureAt = $_REQUEST['departureAt'];
    $searchRequest = [
        "trip_type" => "o",
        "is_return" => $isReturn,
        "departure_date" =>  date('Y-m-d H:i', strtotime($departureAt)),
        "arrival_date" =>  $arrivalAt,
        "pickup" => $pickupAddress,
        "destination" => $destinationAddress,
        "more_cities" => json_encode($moreCities),
        "pickup_id" => $pickupLocationPlaceId,
        "destination_id" => $destinationLocationPlaceId,
        "scp" => 4
    ];
    $endpoint = "/fare";
    $sid = $db->insert_search_request($searchRequest);
    $results = $cb->search($sid, $endpoint);
    $db->update_scp($sid, 3);
    $results = process_CBZ_cabs($results, $sid);

    $filter = cab_filters($results);
    $db->insert_cab_filters($filter, $sid);
    $db->insert_booking_details($results);
    $db->update_scp($sid, 2);
    header("Location: results.php?sid=" . $sid);
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] === "local") {
    $departureAt = $_REQUEST['localPickupAt'];
    $endpoint = '/fare/local';
    $searchRequest = [
        "trip_type" => "l",
        "departure_date" =>  date('Y-m-d H:i', strtotime($departureAt)),
        "pickup_id" => $_REQUEST['localCity'],
        "local_city_package" => $_REQUEST['selectPackage'],
        "scp" => 4
    ];
    $sid = $db->insert_search_request($searchRequest);
    $results = $cb->search($sid, $endpoint);
    $db->update_scp($sid, 3);
    $results = process_CBZ_cabs($results, $sid);
    $filter = cab_filters($results);
    $db->insert_cab_filters($filter, $sid);
    $db->insert_booking_details($results);
    $db->update_scp($sid, 2);
    header("Location: results.php?sid=" . $sid);
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] === "airport") {
    $departureAt = $_REQUEST['localPickupAt'];
    $endpoint = '/fare/airport';
    $searchRequest = [
        "trip_type"      => "a",
        "departure_date" => date('Y-m-d H:i', strtotime($departureAt)),
        "airport_from_to" => ($_REQUEST['airportRoute'] == "from-airport") ? 2 : 1,
        "pickup_id"      => $_REQUEST['airport'],
        "scp"            => 4
    ];

    if ($_REQUEST['airportRoute'] == "from-airport") {
        $searchRequest["destination"] = $_REQUEST['airportdestinationCity'];
        $searchRequest['destination_id'] = $_REQUEST['airport'];
    } else {
        $searchRequest["pickup"] = $_REQUEST['airportdestinationCity'];
        $searchRequest['pickup_id'] = $_REQUEST['airport'];
    }

    $sid = $db->insert_search_request($searchRequest);
    $results = $cb->search($sid, $endpoint);
    $db->update_scp($sid, 3);
    $results = process_CBZ_cabs($results, $sid);
    $filter = cab_filters($results);
    $db->insert_cab_filters($filter, $sid);
    $db->insert_booking_details($results);
    $db->update_scp($sid, 2);
    header("Location: results.php?sid=" . $sid);
}
