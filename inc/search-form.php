   <div class="col-md-12">
       <div class="row align-items-center flex-md-nowrap justify-content-between">
           <div class="col-md-6 px-1">
               <p class=" text-center  py-2 rounded cursor-pointer  hover-yellow-box mb-2 bookingType border-dark-satel border active <?php if (isset($results['trip_info']['trip_type']) && $results['trip_info']['trip_type'] == "o") {
                                                                                                                                            echo "active";
                                                                                                                                        }
                                                                                                                                        ?> outstation">Outstation</p>
           </div>
           <div class="col-md-6 px-1">
               <p class=" text-center py-2 rounded cursor-pointer hover-yellow-box mb-2 bookingType border-dark-satel border <?php if (isset($results['trip_info']['trip_type']) && $results['trip_info']['trip_type'] == "a" || $results['trip_info']['trip_type'] == "l") {
                                                                                                                                    echo "active";
                                                                                                                                }
                                                                                                                                ?> localTripType"> Local / Airport</p>
           </div>
       </div>
   </div>

   <!-- outestanding form -->
   <div class="col-md-12">
       <form class="" action="search.php" method="post" id="search">
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
                       <div class=" d-flex align-items-center justify-content-start gap-2 text-start rounded h6 font-weight-normal cursor-pointer bg-white round_trip border-dark-satel border">
                           <input type="radio" name="roundTrip" id="isReturn"
                               value="true" checked
                               <?php if (isset($results['trip_info']) && $results['trip_info']['is_return'] == 1) echo "checked"; ?>>
                           <label for="isReturn" class="mb-0">Round Trip</label>
                       </div>
                   </div>
                   <div class="col-md-6 px-1">
                       <div class="d-flex align-items-center justify-content-start gap-2 rounded h6 font-weight-normal cursor-pointer bg-white round_trip  border-dark-satel border">
                           <input type="radio" name="roundTrip" id="oneWayTrip" value="false"
                               <?= (isset($results['trip_info']) && $results['trip_info']['is_return'] == 0) ? "checked" : "" ?>>

                           <label for="oneWayTrip" class="mb-0">One Way Trip</label>
                       </div>
                   </div>
               </div>
               <!-- Your Itinerary -->
               <div class="row yourItineraryRow <?php echo (isset($results['cities']) && sizeof($results['cities']) < 0) ? "d-none" : "d-bock"; ?> ">
                   <div class="col-md-12 mb-2">
                       <p class="m-0 mt-1 mb-1 p-0">Your Itinerary</p>
                       <ul class="your_itinerary m-0 p-0 mb-1">
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
                       <div class="form-group position-relative d-flex align-items-center justify-content-start gap-2 text-start rounded font-weight-normal cursor-pointer destination border-dark-satel border bg-white px-2 mb-0">
                           <span class="material-symbols-outlined pickupLocationIcon">
                               navigation
                           </span>
                           <input type="text" name="pickupCity" value="<?php echo (isset($results['cities'])) ? $results['cities'][0]['address'] : ""; ?>" id="pickupCity" class="shadow-none form-control px-0" placeholder=" " autocomplete="off" required>
                           <label for="pickupCity" class="floating-label">Pickup City</label>
                       </div>
                       <ul id="pickupSuggestions" class="suggestions-list d-none">

                       </ul>
                   </div>
               </div>
               <!-- <div class="form-group position-relative">
                   <input type="text" class="form-control" id="fromCity" placeholder=" " required>
                   <label for="fromCity" class="floating-label">From</label>
               </div> -->

               <div class="row mb-2 destinationLocationRow">
                   <div class="col-md-12 px-1">
                       <div class="form-group position-relative d-flex align-items-center justify-content-start gap-2 text-start rounded font-weight-normal cursor-pointer bg-white destination border-dark-satel border bg-white px-2 mb-0">
                           <span class="material-symbols-outlined ">
                               location_on
                           </span>
                           <input type="text" name="goingTo" disabled <?php echo (isset($results['cities']) && sizeof($results['cities']) < 1) ? "disabled " : ""; ?> value="" id="goingTo" placeholder=" " class="shadow-none form-control px-1 bg-white" autocomplete="off" required>
                           <label for="goingTo" class="floating-label">Destination City</label>
                       </div>
                       <ul id="destinationSuggestions" class="suggestions-list d-none"></ul>
                   </div>
               </div>

               <div class="row mb-2">
                   <div class="col-md-12 px-1">
                       <div class="form-group position-relative d-flex align-items-center justify-content-between px-1 gap-2 rounded font-weight-normal cursor-pointer adddestination no-drop-pointer bg-white destination border-dark-satel border px-2 mb-0">
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
                       <div class="form-group position-relative d-flex align-items-center justify-content-between px-2 py-1 gap-3 rounded font-weight-normal cursor-pointer bg-white border-dark-satel border  mb-0">
                           <div class="d-flex align-items-center justify-content-start py-2">
                               <span class="material-symbols-outlined">date_range</span>
                               <input type="text" name="departureAt" id="departureAt"
                                   class="bg-white" value="<?= (!empty($results['trip_info']['departure_date']))
                                                                ? date('d-M-Y H:i', strtotime($results['trip_info']['departure_date']))
                                                                : date('d-M-Y H:i', strtotime('+65 minutes')) ?>"
                                   autocomplete="off">
                           </div>
                       </div>
                   </div>

                   <!-- Arrival Date (Return trip) -->
                   <div class="col-md-6 px-1">
                       <div class="form-group position-relative d-flex align-items-center justify-content-between px-2 py-1 gap-3 rounded font-weight-normal cursor-pointer bg-white arrivalAt bg-white border-dark-satel border  mb-0">
                           <div class="d-flex align-items-center justify-content-start py-2">
                               <span class="material-symbols-outlined">date_range</span>
                               <input type="text" name="arrivalAt" value="<?= (!empty($results['trip_info']['arrival_date']))
                                                                                ? date('d-M-Y', strtotime($results['trip_info']['arrival_date']))
                                                                                : date('d-M-Y', strtotime('today')) ?>" id="arrivalAt"
                                   class="bg-white" autocomplete="off">
                               <span class="material-symbols-outlined date-close cursor-pointer">close</span>
                           </div>
                       </div>
                   </div>

                   <!-- Add Return Date (for One Way only) -->
                   <div class="col-md-6 px-1 d-none add-return-date">
                       <div class="d-flex align-items-center justify-content-between px-3 py-2 gap-3 rounded-xl font-weight-normal cursor-pointer">
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
               <div class="row">
                   <div class="col-md-6 px-1">
                       <div class="d-flex align-items-center justify-content-start gap-2 text-start rounded h6 font-weight-normal cursor-pointer bg-white round_trip border-dark-satel border">
                           <input type="radio" name="local-trip-type" id="localRental"
                               value="Local Rental" checked <?= (isset($request['details'][0]) && $request['details'][0]['trip_type'] == "l") ? "checked" : "" ?>>
                           <label for="localRental" class="mb-0">Local Rental</label>
                       </div>
                   </div>
                   <div class="col-md-6 px-1">
                       <div class="d-flex align-items-center justify-content-start gap-2 rounded h6 font-weight-normal cursor-pointer bg-white round_trip border-dark-satel border">
                           <input type="radio" name="local-trip-type" id="airportTransfer" value="Airport Transfer" <?= (isset($request['details'][0]) && $request['details'][0]['trip_type'] == "a") ? "checked" : "" ?>>

                           <label for="airportTransfer" class="mb-0">Airport Transfer</label>
                       </div>
                   </div>

               </div>

               <div id="localCity" class="">
                   <div class="row mb-2 sekectCityRow">
                       <div class="col-md-12 px-1">
                           <div class="form-group position-relative d-flex align-items-center justify-content-start gap-0 text-start rounded font-weight-normal cursor-pointer border-dark-satel border bg-white px-2 mb-0 localCity">
                               <span class="material-symbols-outlined">
                                   location_on
                               </span>
                               <select name="localCity" id="selectCity" class="form-control bg-white border-0 shadow-none outline-none px-1">
                                   <option value="" disabled selected>Select City</option>
                               </select>
                           </div>
                       </div>
                   </div>

                   <div class="row mb-2 selecepackageRow">
                       <div class="col-md-12 px-1">
                           <div class="form-group position-relative d-flex align-items-center justify-content-start gap-0 text-start rounded font-weight-normal cursor-pointer border-dark-satel border bg-white px-2 mb-0 selectPackage">
                               <span class="material-symbols-outlined">
                                   location_on
                               </span>
                               <select name="selectPackage" id="selectPackage" class="form-control bg-white border-0 shadow-none outline-none px-1">
                                   <option value="" disabled selected>Select Package</option>
                               </select>
                           </div>
                       </div>
                   </div>
               </div>

               <div id="airport" class="d-none">
                   <div class="row mb-2 sekectCityRow">
                       <div class="col-md-12 px-1">
                           <div class="form-group position-relative d-flex align-items-center justify-content-start gap-2 text-start rounded font-weight-normal cursor-pointer border-dark-satel border bg-white px-2 mb-0 localCity">
                               <select name="airportRoute" id="selectRoute" class="form-control bg-white border-0 shadow-none outline-none">
                                   <option value="" disabled selected>From Airport / To Airport</option>
                                   <option value="to-airport" <?= (isset($request['details'][0]['fareType']) && $request['details'][0]['fareType'] == 'to-airport') ? 'selected' : '' ?>>To Airport</option>
                                   <option value="from-airport" <?= (isset($request['details'][0]['fareType']) && $request['details'][0]['fareType'] == 'from-airport') ? 'selected' : '' ?>>From Airport</option>
                               </select>
                               <span class="material-symbols-outlined">
                                   arrow_drop_down
                               </span>
                           </div>
                       </div>
                   </div>

                   <div class="row mb-2 selecepackageRow">
                       <div class="col-md-12 px-1">
                           <div class="form-group position-relative d-flex align-items-center justify-content-start gap-2 text-start rounded font-weight-normal cursor-pointer border-dark-satel border bg-white px-2 mb-0 selectPackage">
                               <select name="airport" id="selectAirport" <?= isset($request['details'][0]) ? "onchange='getLocalCitiesAgainstAirport()'" : "" ?>
                                   class="form-control bg-white border-0 shadow-none outline-none overflow-scroll">
                                   <option value="" disabled selected>Select Airport</option>
                               </select>
                               <span class="material-symbols-outlined">
                                   travel
                               </span>
                           </div>
                       </div>
                   </div>

                   <div class="row mb-2 destinationCityRow <?= (isset($request['details'][0]['destinationCity'])) ? 'd-block' : 'd-none' ?>">
                       <div class="col-md-12 px-1">
                           <div class="form-group position-relative d-flex align-items-center justify-content-start gap-2 text-start rounded font-weight-normal cursor-pointer border-dark-satel border bg-white px-2 mb-0 selectPackage">
                               <select name="airportdestinationCity" id="selectDestinationcity" class="form-control bg-white border-0 shadow-none outline-none overflow-scroll showLocalCities">
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
                       <div class="form-group position-relative d-flex align-items-center justify-content-between px-2 py-1 gap-3 rounded font-weight-normal cursor-pointer bg-white localPickupAt border-dark-satel border  mb-0">
                           <div class="d-flex align-items-center justify-content-start py-2">
                               <span class="material-symbols-outlined">date_range</span>
                               <input type="text" name="localPickupAt" id="localPickupAt"
                                   class="bg-white" value="<?= (!empty($results['trip_info']['departureAt']))
                                                                ? date('d-M-Y H:i', strtotime($results['trip_info']['departureAt']))
                                                                : date('d-M-Y H:i', strtotime('+65 minutes')) ?>" autocomplete="off">
                           </div>
                       </div>
                   </div>
               </div>

           </div>
           <div class="row mb-2">
               <div class="col-md-12 px-1">

                   <button class="hero_submit_button">Check Price & Book Cab</button>
               </div>
           </div>

           <!-- Local city form end  -->
       </form>
   </div>