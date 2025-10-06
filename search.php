<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$pageTitle = "Tripodeal Cabs - Fare ";
// require './inc/head.php';

require 'helper/db.php';
require 'helper/booking_initiate.php';
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
    // echo "<pre>";
    // print_r($results);
    // echo "</pre>";
    // die;
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
if (isset($_REQUEST['action']) && $_REQUEST['action'] === "booking_initate") {

    $carrier = (isset($_REQUEST['carrier']) ? 1 : 0);
    $carModel = (isset($_REQUEST['carModel']) ? 1 : 0);
    $driverLanguage = (isset($_REQUEST['driverLanguage']) ? 1 : 0);
    $petAllowed = (isset($_REQUEST['petAllowed']) ? 1 : 0);
    $refundable = (isset($_REQUEST['refundable']) ? 1 : 0);
    $billingAddressAsPickupAddredd = (isset($_REQUEST['billingAddressAsPickupAddredd']) ? 1 : 0);


    if (isset($_REQUEST['paymentOption']) && $_REQUEST['paymentOption'] === "fullPay") {
        $booking_array =
            [
                'sid' => $_REQUEST['sid'],
                'carrier' => 1,
                'car_model' => 1,
                'driver_language' => 1,
                'pet_allowed' => 1,
                'refundable' => 1,
                'pickup_live_location' => (isset($_REQUEST['pickupLiveLocation']) ? $_REQUEST['pickupLiveLocation'] : ""),
                'customer_name' => (isset($_REQUEST['customerName']) ? $_REQUEST['customerName'] : ""),
                'gender' => (isset($_REQUEST['gender'])) ? $_REQUEST['gender'] : "",
                'country_code' => (isset($_REQUEST['countryCode']) ? $_REQUEST['countryCode'] : ""),
                'phone' => (isset($_REQUEST['phone']) ? $_REQUEST['phone'] : ""),
                'email' => (isset($_REQUEST['email']) ? $_REQUEST['email'] : ""),
                'billing_addr_as_pickup_addr' => (isset($_REQUEST['billingAddressAsPickupAddredd']) ? 1 : 0),
                'payment_option' => (isset($_REQUEST['paymentOption']) ? $_REQUEST['paymentOption'] : "")
            ];
    } else {
        $booking_array = [
            'sid' => $_REQUEST['sid'],
            'carrier' => (isset($_REQUEST['carrier']) ? 1 : 0),
            'car_model' => (isset($_REQUEST['carModel']) ? 1 : 0),
            'driver_language' => (isset($_REQUEST['driverLanguage']) ? 1 : 0),
            'pet_allowed' => (isset($_REQUEST['petAllowed']) ? 1 : 0),
            'refundable' => (isset($_REQUEST['refundable']) ? 1 : 0),
            'pickup_live_location' => (isset($_REQUEST['pickupLiveLocation']) ? $_REQUEST['pickupLiveLocation'] : ""),
            'customer_name' => (isset($_REQUEST['customerName']) ? $_REQUEST['customerName'] : ""),
            'gender' => (isset($_REQUEST['gender'])) ? $_REQUEST['gender'] : "",
            'country_code' => (isset($_REQUEST['countryCode']) ? $_REQUEST['countryCode'] : ""),
            'phone' => (isset($_REQUEST['phone']) ? $_REQUEST['phone'] : ""),
            'email' => (isset($_REQUEST['email']) ? $_REQUEST['email'] : ""),
            'billing_addr_as_pickup_addr' => (isset($_REQUEST['billingAddressAsPickupAddredd']) ? 1 : 0),
            'payment_option' => (isset($_REQUEST['paymentOption']) ? $_REQUEST['paymentOption'] : ""),
        ];
    }
    $bid = $db->booking_details_insert($booking_array);
    header("Location: payment.php?bid=" . $bid);
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] === "make_booking") {
    $bid = $_REQUEST['bid'];
    $bookingDetails = $db->fetch_booking_details($bid);
    $sid = $bookingDetails['sid'];
    $selectedCab = $db->selected_cab($sid);
    $cid = $selectedCab['cid'];
    $selectedCabResult = $db->get_priculler_cab_against_cid($cid);
    $request = $db->get_search_request($sid);
    $results = make_booking_initiate($bookingDetails, $selectedCabResult, $request);
    $results = $cb->cab_booking_initiate($results, $sid);
    $booking_array = make_bookings_array($results, $sid);
    $bookingId = $db->insert_bookings_details($booking_array);
    echo json_encode($bookingId);
}
