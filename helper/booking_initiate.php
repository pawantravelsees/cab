<?php


function make_booking_initiate($bookingDetails,  $selectedCabResult, $request)
{
    if ($request['details'][0]['trip_type'] == 'o') {
        $outstationBookingDetails = make_booking_outstation($bookingDetails, $selectedCabResult, $request);
        return $outstationBookingDetails;
    }
    if ($request['details'][0]['trip_type'] == 'a') {
        $airportBookingDetails =  make_booking_airport($bookingDetails,  $selectedCabResult, $request);
        return $airportBookingDetails;
    }
    if ($request['details'][0]['trip_type'] == 'l') {
        $localBookingDetails =  make_booking_local($bookingDetails, $selectedCabResult, $request);
        return $localBookingDetails;
    }
}

function make_booking_outstation($bookingDetails, $selectedCabResult, $request)
{
    $isTollInclusive = $bookingDetails['payment_option'] == 'fullPay' ? true : false;
    foreach ($request['city'] as $city) {
        $itinerary[] = [
            'placeId' => $city['id'],
            'address' => $city['address']
        ];
    }
    $payload = [
        "tripType" => "outstation",
        "departureAt" => date('d-m-Y H:i', strtotime($request['details'][0]['departure_date'])) ?? "",   // DD/MM/YYYY HH:mm
        "isReturn" => $request['details'][0]['is_return'] ?? false,
        "arrivalAt" => ($request['details'][0]['is_return'] == true || sizeof($request['city']) > 2) ? date('d-m-Y', strtotime($request['details'][0]['arrival_date'])) : '',       // Required if round trip or >2 stops
        "itinerary" => $itinerary ?? [],       // From Get Prices ASPI
        "carId" => $selectedCabResult['car_id'] ?? "",
        "customerName" => $bookingDetails['customer_name'] ?? "",
        "customerCountryCode" => $bookingDetails['country_code'] ?? "",
        "customerPhone" => $bookingDetails['phone'] ?? "",
        "customerEmail" => $bookingDetails['email'] ?? "",
        "completePickupAddress" => $bookingDetails['pickup_live_location'] ?? "",
        "billingName" => $bookingDetails['billingName'] ?? "",
        "gstNumber" => $bookingDetails['gstNumber'] ?? "",
        "carrierRequired" => $bookingDetails['carrier'] ?? false,
        "petRequired" => $bookingDetails['pet_allowed'] ?? false,
        "newCarRequired" => $bookingDetails['car_model'] ?? false,
        "isTollInclusive" => $isTollInclusive

    ];
    return $payload;
}

function make_booking_airport($bookingDetails, $selectedCabResult, $request)
{
    // $isTollInclusive = $bookingDetails['payment_option'] == 'fullPay' ? true : false;

    $payload = [
        "tripType" => "airport",
        "departureAt" => date('d-m-Y H:i', strtotime($request['details'][0]['departureAt'])) ?? "",
        "carId" => $selectedCabResult['car_id'] ?? "",
        "customerName" => $bookingDetails['customer_name'] ?? "",
        "customerCountryCode" => $bookingDetails['country_code'] ?? "",
        "customerPhone" => $bookingDetails['phone'] ?? "",
        "customerEmail" => $bookingDetails['email'] ?? "",
        "completePickupAddress" => $bookingDetails['pickup_live_location'] ?? "",
        "billingName" => $bookingDetails['billingName'] ?? "",
        "gstNumber" => $bookingDetails['gstNumber'] ?? "",
        "carrierRequired" => $bookingDetails['carrier'] ?? false,
        "petRequired" => $bookingDetails['pet_allowed'] ?? false,
        "newCarRequired" => $bookingDetails['car_model'] ?? false,
        "isTollInclusive" => false
    ];

    return $payload;
}

function make_booking_local($bookingDetails, $selectedCabResult, $request)
{
    $payload = [
        "tripType" => "local",
        "departureAt" => date('d-m-Y H:i', strtotime($request['details'][0]['departureAt'])) ?? "",
        "carId" => $selectedCabResult['car_id'] ?? "",
        "customerName" => $bookingDetails['customer_name'] ?? "",
        "customerCountryCode" => $bookingDetails['country_code'] ?? "",
        "customerPhone" => $bookingDetails['phone'] ?? "",
        "customerEmail" => $bookingDetails['email'] ?? "",
        "completePickupAddress" => $bookingDetails['pickup_live_location'] ?? "",
        "billingName" => $bookingDetails['billingName'] ?? "",
        "gstNumber" => $bookingDetails['gstNumber'] ?? "",
        "carrierRequired" => $bookingDetails['carrier'] ?? false,
        "petRequired" => $bookingDetails['pet_allowed'] ?? false,
        "newCarRequired" => $bookingDetails['car_model'] ?? false,
        "isTollInclusive" => false
    ];

    return $payload;
}


function make_bookings_array($results , $sid)
{
    $booking_array = [
        'sid' => $sid,
        'booking_id' => $results['bookingId'],
        'status' => $results['status'],
        'payment_status' => $results['paymentStatus'],
        'created_at' => date('Y-m-d H:i:s', strtotime('now'))
    ];
    return $booking_array;
}
