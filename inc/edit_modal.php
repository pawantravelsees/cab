<?php
function formate_search_request($results)
{
    $searchRequest = $results['details'][0];
    $moreCities = json_decode($searchRequest['more_cities'] ?? '[]', true) ?: [];
    $cities = [];
    $cities[] = [
        "address" => $searchRequest['pickup'] ?? "",
        "id" => $searchRequest['pickup_id'] ?? ""
    ];

    foreach ($moreCities as $city) {
        $cities[] = [
            "address" => $city['address'],
            "id" => $city['id']
        ];
    }
    $cities[] = [
        "address" => $searchRequest['destination'] ?? "",
        "id" => $searchRequest['destination_id'] ?? ""
    ];

    if (isset($searchRequest['is_return']) && $searchRequest['is_return'] == true && end($cities)['address'] != $searchRequest['pickup']) {
        $cities[] = [
            "address" => $searchRequest['pickup'],
            "id" => $searchRequest['pickup_id']
        ];
    }
    $tripType = "";
    if (isset($searchRequest['is_return']) && $searchRequest['is_return'] == true) {
        $tripType = "Round Trip";
    } elseif (sizeof($cities) > 2 && $searchRequest['is_return'] == false) {
        $tripType = "One Way Multi-Stop Trip";
    } else {
        $tripType = "One Way Trip";
    }

    return [
        "trip_info" => [
            "trip_type"      => $results['details'][0]['trip_type'],
            "trip_way"      => $tripType,
            "is_return"      => $searchRequest['is_return'] ?? "",
            "departure_date" => $searchRequest['departure_date'] ?? "",
            "arrival_date"   => $searchRequest['arrival_date'] ?? "",
        ],
        "cities" => $cities,
        // "result" => $results['apiResponse'],
    ];
}
$results = formate_search_request($request);
// echo "<pre>";
// print_r($request);
// // print_r($selectedCity);
// echo "</pre>";
// die;
?>
<div class="bg-modify mb-0 ">
    <div class="container d-lg-block d-none p-1">
        <div class="row  mb-0 align-items-center m-2 my-lg-0 p-1 px-lg-0 no-gutters">
            <div class="col">
                <div class="d-flex align-items-center text-white">
                    <?php
                    if ($results['trip_info']['trip_type'] == "o") { ?>
                        <span class="material-symbols-outlined text-white mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">location_on</span>
                        <span class="mr-2 font-weight-bold"><?php echo $results['cities'][0]['address'] ?></span>
                        <!-- <span class="material-symbols-outlined mx-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">date_range</span> -->
                        <span class="font-weight-bold mr-2"><?= date('D d, M h:i', strtotime($results['trip_info']['departure_date'])) ?> </span>
                        <?php
                        if ($results['trip_info']['is_return'] != 0 || sizeof($results['cities']) > 2) { 
                            echo " - "
                            ?> 
                           <span class="material-symbols-outlined text-white ml-1">
                                departure_board
                            </span>
                        <?php
                        }
                        ?>
                        <span class="font-weight-bold mr-3"><?php
                                                            if ($results['trip_info']['is_return'] != 0 || sizeof($results['cities']) > 2) {
                                                                echo date('D d, M', strtotime($results['trip_info']['arrival_date']));
                                                            } else {
                                                            }
                                                            ?></span>
                        <span class="material-symbols-outlined text-white mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">directions_car</span>
                        <span class="mr-3"><?= $results['trip_info']['trip_way'] ?></span>
                    <?php
                    } elseif ($results['trip_info']['trip_type'] == "a") {

                        function extractDetails($string)
                        {
                            $parts = explode(' ', trim($string));
                            $firstWord = $parts[0] ?? '';
                            preg_match('/\((.*?)\)/', $string, $matches);
                            $firstBracket = $matches[1] ?? '';
                            $segments = explode('-', $string);
                            $lastPart = trim(end($segments)) . " Airport";

                            return [
                                'city'   => $firstWord,
                                'terminal' => $firstBracket,
                                'lastPart'    => $lastPart
                            ];
                        }

                        $text = $selectedCity['airport_name'];
                        $result = extractDetails($text);
                        $pickupAirport = $result['city'] . ", (" . $result['terminal'] . ") " . $result['lastPart'];
                        // echo "<pre>";
                        // print_r($result);
                        // echo "</pre>";
                        $location = "";
                        if ($request['details'][0]['fareType'] == "to-airport") {
                            $location = $request['details'][0]['destinationCity'] . " - " . $pickupAirport;
                        } else {
                            $location = $pickupAirport . " - " . $request['details'][0]['destinationCity'];
                        }

                        // $pickupAt = "";
                        // ($request['details'][0]['fareType'] == "from-airport") ? $pickupAt = "From" : $pickupAt = "To";
                    ?>
                        <p class="m-0 p-0 mr-1">
                            <span class="mr-0 font-weight-bold"><?php echo $location ?></span>,
                        </p>
                        <p class="m-0 p-0 mr-2">
                            <span class="font-weight-bold mx-2"><?= date('D d, M h:i', strtotime($request['details'][0]['departureAt'])) ?> </span>
                        </p>

                    <?php
                    } elseif ($results['trip_info']['trip_type'] == "l") {
                        $cityId = $request['details'][0]['cityId'];
                        $cityName = $db->get_local_city_details($cityId);
                        // echo "<pre>";
                        // // echo $cityName;
                        // print_r(($request));
                        // echo "</pre>";
                    ?>
                        <p class="m-0 p-0 mr-1">
                            <span class="mr-0 font-weight-bold"><?php echo $cityName['city_name'] ?></span>,
                        </p>
                        <p class="m-0 p-0 mr-1">
                            <span class="mr-0 font-weight-bold"><?php
                                                                $bookingType = $request['details'][0]['fareType'];
                                                                $localCabKM = "";
                                                                if (isset($bookingType)) {
                                                                    if ($bookingType == "2_20") {
                                                                        $localCabKM = "2 Hours , 20Km";
                                                                    } else if ($bookingType == "4_40") {
                                                                        $localCabKM = "4 Hours , 40Km";
                                                                    } else if ($bookingType == "8_80") {
                                                                        $localCabKM = "8 Hours , 80Km";
                                                                    } else if ($bookingType == "12_120") {
                                                                        $localCabKM = "12 Hours , 120Km";
                                                                    } else {
                                                                        $localCabKM = $bookingType . " KM";
                                                                    }
                                                                }
                                                                echo "($localCabKM)";
                                                                ?></span>,
                        </p>
                        <p class="m-0 p-0 mr-2">
                            <span class="font-weight-bold mx-2"><?= date('D d, M h:i', strtotime($request['details'][0]['departureAt'])) ?> </span>
                        </p>
                    <?php
                    }
                    ?>
                    <div class="ml-auto">
                        <?php
                        $btnText = "";
                        $btnIcon = "";
                        $pageUrl = "#";

                        if ($pagename == 'results.php') {
                            $btnText = "Change";
                            $btnIcon = "edit";
                        } elseif ($pagename == 'cab_details.php') {
                            $btnText = "Go Back";
                            $btnIcon = "chevron_left";
                            $pageUrl = "results.php?sid=" . $sid;
                        } elseif ($pagename == 'payment.php') {
                            $btnText = "Go Back";
                            $btnIcon = "chevron_left";
                            $pageUrl = "cab_details.php?cid=" . $cid . "&sid=" . $sid;
                        } elseif ($pagename == 'ticket.php') {
                            $btnText = "Go Back";
                            $btnIcon = "chevron_left";
                            $pageUrl = "payment.php?bid=" . $bid;
                        }
                        ?>
                        <a href="<?= $pageUrl ?>"
                            class="btn text-white bgshade-2 btn-lg px-3 py-2 edit round mr-0 text-center modifyBtn"
                            style="border: 0; font-size: 14px;"
                            <?php echo ($pagename == "results.php") ? 'data-toggle="modal"' : ""; ?>
                            data-tripType="<?= $results['trip_info']['trip_type'] ?>">
                            <span class="material-symbols-outlined text-white"><?= $btnIcon ?></span>
                            <?= $btnText ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php if (($results['trip_info']['trip_type'] == "o") && ($pagename == 'results.php') || ($results['trip_info']['trip_type'] == "o") && ($pagename == 'cab_details.php')) { ?>
    <div class="bg-custom_gray pt-2">
        <div class="container mt-0 ">
            <ul class="d-flex justify-content-start align-content-center result_your_itinerary m-0 p-0 pb-2">
                <?php
                foreach ($results['cities'] as $city) {
                ?>
                    <li class="border-0 itinerary_list"><?= $city['address'] ?></li>
                    <span class="material-symbols-outlined itinerary_arrow"> arrow_forward </span>
                <?php
                }
                ?>
            </ul>
        </div>
    </div>
<?php } ?>