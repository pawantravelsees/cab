<form class="" action="search.php" method="post">
    <?php
    // echo "<pre>";
    // print_r($results);
    // echo "</pre>";
    ?>
    <input type="hidden" name="action" value="<?php if (isset($results['trip_info']['trip_type'])) {
                                                    if ($results['trip_info']['trip_type'] == "o") {
                                                        echo "outstation";
                                                    } else if ($results['trip_info']['trip_type'] == "l") {
                                                        echo "local";
                                                    } else if ($results['trip_info']['trip_type'] == "a") {
                                                        echo "airport";
                                                    }
                                                } else {
                                                    echo "outstation";
                                                }
                                                ?>">
    <div id="searchCar" class="d-block">

        <div class="row">
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl h6 font-weight-normal cursor-pointer bg-light-gray round_trip">
                    <input type="radio" name="roundTrip" id="isReturn"
                        value="true" checked
                        <?php if (isset($results['trip_info']) && $results['trip_info']['is_return'] == 1) echo "checked"; ?>>
                    <label for="isReturn" class="mb-0">Round Trip</label>
                </div>
            </div>
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-start gap-2 rounded-xl h6 font-weight-normal cursor-pointer bg-light-gray round_trip">
                    <input type="radio" name="roundTrip" id="oneWayTrip" value="false"
                        <?= (isset($results['trip_info']) && $results['trip_info']['is_return'] == 0) ? "checked" : "" ?>>

                    <label for="oneWayTrip" class="mb-0">One Way Trip</label>
                </div>
            </div>

        </div>
        <!-- Your Itinerary -->
        <div class="row yourItineraryRow <?php echo (isset($results['cities']) && sizeof($results['cities']) < 0) ? "d-none" : "d-bock"; ?> ">
            <div class="col-md-12 mb-2">
                <p class="m-0 mt-1 mb-2 p-0">Your Itinerary</p>
                <ul class="your_itinerary m-0 p-0 mb-3">
                    <?php
                    if (isset($results['cities'])) {
                        foreach ($results['cities'] as $index => $city) {
                    ?>
                            <li class="border-0 itinerary_list" data-id="<?= $index ?>">
                                <?php echo $city['address'] ?>
                                <input type="text" name="locs[]" readonly="" value="<?php echo json_encode($city); ?>">
                            </li>
                            <span class="material-symbols-outlined itinerary_arrow"> arrow_forward </span>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
        <!-- Your Itinerary End -->
        <div class="row pickupLocationRow <?php echo (isset($results['cities']) && sizeof($results['cities']) > 1) ? "d-none" : "d-bock"; ?>">
            <div class="col-md-12 px-1 mb-2">
                <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray destination">
                    <input type="text" name="pickupCity" value="<?php echo (isset($results['cities'])) ? $results['cities'][0]['address'] : ""; ?>" id="pickupCity" class="bg-light-gray" placeholder="Enter pickup city" autocomplete="off" required>
                    <span class="material-symbols-outlined pickupLocationIcon">
                        navigation
                    </span>
                </div>
                <ul id="pickupSuggestions" class="suggestions-list d-none">

                </ul>
            </div>
        </div>

        <div class="row mb-2 destinationLocationRow">
            <div class="col-md-12 px-1">
                <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray destination">
                    <input type="text" name="goingTo" disabled <?php echo (isset($results['cities']) && sizeof($results['cities']) < 1) ? "disabled " : ""; ?> value="" id="goingTo" placeholder="Enter destination city" class="bg-light-gray" autocomplete="off" required>
                    <span class="material-symbols-outlined">
                        location_on
                    </span>
                </div>
                <ul id="destinationSuggestions" class="suggestions-list d-none"></ul>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-12 px-1">
                <div class="d-flex align-items-center justify-content-between px-3 gap-2 rounded-xl font-weight-normal cursor-pointer bg-light-gray adddestination no-drop-pointer">
                    <div class="d-flex align-items-center justify-content-start">
                        <span class="material-symbols-outlined">
                            add
                        </span>
                        <span class="addMoreCityBtn">Add More City</span>
                    </div>
                    <span class="material-symbols-outlined">
                        stop_circle
                    </span>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <!-- Departure DateTime -->
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-between px-3 py-3 gap-3 rounded-xl font-weight-normal cursor-pointer bg-light-gray">
                    <div class="d-flex align-items-center justify-content-start">
                        <input type="text" name="departureAt" id="departureAt"
                            class="bg-light-gray" value="<?= (isset($results['trip_info']['departure_date'])) ? $results['trip_info']['departure_date'] : '' ?>" autocomplete="off">
                    </div>
                </div>
            </div>

            <!-- Arrival Date (Return trip) -->
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-between px-3 py-3 gap-3 rounded-xl font-weight-normal cursor-pointer bg-light-gray arrivalAt">
                    <div class="d-flex align-items-center justify-content-start">
                        <span class="material-symbols-outlined">date_range</span>
                        <input type="text" name="arrivalAt" value="<?= (isset($results['trip_info']['departure_date'])) ? date('d-M-Y', strtotime($results['trip_info']['arrival_date'])) : '' ?>" id="arrivalAt"
                            class="bg-light-gray" autocomplete="off">
                        <span class="material-symbols-outlined date-close cursor-pointer">close</span>
                    </div>
                </div>
            </div>

            <!-- Add Return Date (for One Way only) -->
            <div class="col-md-6 px-1 d-none add-return-date">
                <div class="d-flex align-items-center justify-content-between px-3 py-3 gap-3 rounded-xl font-weight-normal cursor-pointer">
                    <div class="d-flex align-items-center justify-content-start">
                        <a href="javascript:void(0)" class="d-flex align-items-center gap-1">
                            <span class="material-symbols-outlined">date_range</span>
                            Add Return Date
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Local city form start  -->
    <div class="local d-none" id="localTrip">
        <?php
        // echo "<pre>";
        // print_r($selectedCity);
        // print_r($request['details'][0]);
        // echo "</pre>";
        ?>
        <div class="row">
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl h6 font-weight-normal cursor-pointer bg-light-gray round_trip">
                    <input type="radio" name="local-trip-type" id="localRental"
                        value="Local Rental" checked <?= (isset($request['details'][0]) && $request['details'][0]['trip_type'] == "l") ? "checked" : "" ?>>
                    <label for="localRental" class="mb-0">Local Rental</label>
                </div>
            </div>
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-start gap-2 rounded-xl h6 font-weight-normal cursor-pointer bg-light-gray round_trip">
                    <input type="radio" name="local-trip-type" id="airportTransfer" value="Airport Transfer" <?= (isset($request['details'][0]) && $request['details'][0]['trip_type'] == "a") ? "checked" : "" ?>>

                    <label for="airportTransfer" class="mb-0">Airport Transfer</label>
                </div>
            </div>

        </div>

        <div id="localCity" class="">
            <div class="row mb-2 sekectCityRow">
                <div class="col-md-12 px-1">
                    <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray localCity">
                        <select name="localCity" id="selectCity" class="form-control bg-light-gray border-0 shadow-none outline-none">
                            <option value="" disabled selected>Select City</option>
                        </select>
                        <span class="material-symbols-outlined">
                            location_on
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-2 selecepackageRow">
                <div class="col-md-12 px-1">
                    <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray selectPackage">
                        <select name="selectPackage" id="selectPackage" class="form-control bg-light-gray border-0 shadow-none outline-none">
                            <option value="" disabled selected>Select Package</option>
                        </select>
                        <span class="material-symbols-outlined">
                            location_on
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div id="airport" class="d-none">
            <div class="row mb-2 sekectCityRow">
                <div class="col-md-12 px-1">
                    <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray localCity">
                        <select name="airportRoute" id="selectRoute" class="form-control bg-light-gray border-0 shadow-none outline-none">
                            <option value="" disabled <?= !isset($request['details'][0]['fareType']) ? 'selected' : '' ?>>From Airport / To Airport</option>
                            <option value="from-airport" <?= (isset($request['details'][0]['fareType']) && $request['details'][0]['fareType'] == 'from-airport') ? 'selected' : '' ?>>From Airport</option>
                            <option value="to-airport" <?= (isset($request['details'][0]['fareType']) && $request['details'][0]['fareType'] == 'to-airport') ? 'selected' : '' ?>>To Airport</option>
                        </select>
                        <span class="material-symbols-outlined">
                            arrow_drop_down
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-2 selecepackageRow">
                <div class="col-md-12 px-1">
                    <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray selectPackage">
                        <select name="airport" id="selectAirport" class="form-control bg-light-gray border-0 shadow-none outline-none overflow-scroll">
                            <option value="" disabled selected>Select Airport</option>
                        </select>
                        <span class="material-symbols-outlined">
                            travel
                        </span>
                    </div>
                </div>
            </div>

            <div class="row mb-2 destinationCityRow ">
                <div class="col-md-12 px-1">
                    <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded-xl font-weight-normal cursor-pointer bg-light-gray selectPackage">
                        <select name="airportdestinationCity" id="selectDestinationcity" class="form-control bg-light-gray border-0 shadow-none outline-none overflow-scroll showLocalCities">
                            <option value="" disabled selected>Select Destination City</option>
                            
                        </select>
                        <span class="material-symbols-outlined">
                            location_on
                        </span>
                    </div>
                </div>
            </div>

        </div>
        <!-- Departure DateTime -->
        <div class="row mb-2">
            <div class="col-md-6 px-1">
                <div class="d-flex align-items-center justify-content-between px-3 py-3 gap-3 rounded-xl font-weight-normal cursor-pointer bg-light-gray localPickupAt">
                    <div class="d-flex align-items-center justify-content-start">
                        <input type="text" name="localPickupAt" id="localPickupAt"
                            class="bg-light-gray" value="<?= (isset($request['details'][0]['departureAt'])) ? date('d-M-Y H:i', strtotime($request['details'][0]['departureAt'] ?? 'now')) : '' ?>" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row mb-2">
        <div class="col-md-12 px-2">

            <button class="bg-warning hero_submit_button">Check Price & Book Cab</button>
        </div>
    </div>

    <!-- Local city form end  -->
</form>