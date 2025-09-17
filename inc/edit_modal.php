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
// print_r($selectedCity);
// echo "</pre>";
// die;
?>
<div class="bg-modify mb-2">
    <div class="container d-lg-block d-none p-1">
        <div class="row  mb-0 align-items-center m-2 my-lg-0 p-1 px-lg-0 no-gutters">
            <div class="col">
                <div class="d-flex align-items-center text-white">
                    <?php
                    if ($results['trip_info']['trip_type'] == "o") { ?>
                        <span class="material-symbols-outlined mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">location_on</span>
                        <span class="mr-2 font-weight-bold"><?php echo $results['cities'][0]['address'] ?>,</span>
                        <!-- <span class="material-symbols-outlined mx-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">date_range</span> -->
                        <span class="font-weight-bold mr-2"><?= date('D d, M h:i', strtotime($results['trip_info']['departure_date'])) ?> - </span>
                        <span class="material-symbols-outlined">
                            departure_board
                        </span>
                        <span class="font-weight-bold mr-3"><?php
                                                            if ($results['trip_info']['is_return'] != 0 || sizeof($results['cities']) > 2) {
                                                                echo date('D d, M', strtotime($results['trip_info']['arrival_date']));
                                                            } else {
                                                            }

                                                            ?></span>
                        <span class="material-symbols-outlined mr-1" style="font-variation-settings: 'FILL' 1; font-size: 20px;">directions_car</span>
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
                        // echo "<pre>";
                        // print_r($result);
                        // echo "</pre>";
                        $pickupAirport = $result['city'] . ", (" . $result['terminal'] . ") " . $result['lastPart'];
                        $pickupAt = "";
                        ($request['details'][0]['fareType'] == "from-airport") ? $pickupAt = "From" : $pickupAt = "To";
                    ?>
                        <p class="m-0 p-0 mr-2"><strong><?= $pickupAt ?></strong>
                            <span class="mr-2 font-weight-bold"><?php echo "- " . $pickupAirport ?></span>
                        </p>
                        <p class="m-0 p-0 mr-2"><strong><?= "Pickup At -" ?></strong>
                            <span class="font-weight-bold mr-2"><?= date('D d, M h:i', strtotime($request['details'][0]['departureAt'])) ?> </span>
                        </p>

                    <?php
                    }
                    ?>
                    <div class="ml-auto">
                        <button class="btn text-white bgshade-2 btn-lg px-3 py-2 edit round mr-0 text-center modifyBtn" style="border: 0; font-size: 16px;" data-toggle="modal" data-tripType="<?php echo $results['trip_info']['trip_type'] ?>"><span class="material-symbols-outlined edit">
                                edit
                            </span> Change</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php if ($results['trip_info']['trip_type'] == "o") { ?>
    <div class="container mt-0">
        <ul class="d-flex justify-content-start align-content-center result_your_itinerary m-0 p-0 mb-3">
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
<?php } ?>