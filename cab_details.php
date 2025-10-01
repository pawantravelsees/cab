<?php
if (!isset($_GET['sid'])) {
    header("Location: index.php");
}
require './inc/head.php';
require './inc/header.php';
require 'helper/db.php';

$db = new db();
$sid = $_GET['sid'];
$cid = $_GET['cid'];
$selectedCabId = $db->get_selected_cab($cid);
$selectedCabResult = $db->get_selected_cab_details($selectedCabId);
$request = $db->get_search_request($sid);
$scp = $request['details'][0]['scp'];
if (isset($request['details'][0]['cityId'])) {
    $selectedCity = $db->get_local_city_details($request['details'][0]['cityId']);
}
if (isset($request['details'][0]['airportId'])) {
    $selectedCity = $db->get_local_airport_details($request['details'][0]['airportId']);
}
if (isset($request['details']) && $request['details'][0]['trip_type'] == "o") {
    $itinerary_list = json_encode(array_merge([["id" => (string)$request['details'][0]['pickup_id'], "address" => $request['details'][0]['pickup']]], json_decode($request['details'][0]['more_cities'], 1), [["id" => (string)$request['details'][0]['destination_id'], "address" => $request['details'][0]['destination']]]));
}
$grandTotal = "";
// Calculate discounted price
$basePrice = ceil($selectedCabResult['price']);
$basegst = ceil($selectedCabResult['gst']);
$carrierChages = ceil($selectedCabResult['carrier_charge']);
$carrierGst = ceil($selectedCabResult['carrier_gst']);
$newCarCharges = ceil($selectedCabResult['new_car_charge']);
$newCarGst = ceil($selectedCabResult['new_car_gst']);
$petCharge = ceil($selectedCabResult['pet_charge']);
$petGst = ceil($selectedCabResult['pet_gst']);

$discountPrice = ($selectedCabResult['price'] > 30000) ? $selectedCabResult['price'] - 1500 : $selectedCabResult['price'] - 500;
$discountPriceWithGST = $discountPrice + $selectedCabResult['gst'];
$discountPercent = ($basePrice > 0 && $discountPrice < $basePrice) ? round((($basePrice - $discountPrice) / $basePrice) * 100) : 0;

if ($selectedCabResult['total_price'] <= 0) {
    $grandTotal = $selectedCabResult['price'] + $selectedCabResult['gst'];
    $grandGST   = $selectedCabResult['gst'];
} else {
    $grandTotal = $selectedCabResult['total_price'] + $selectedCabResult['total_gst'];
    $grandGST   = $selectedCabResult['total_gst'];
}
$advancePay = $grandTotal * 0.18;
?>
<?php
echo "<pre>";
// print_r($selectedCabResult);
// print_r($selectedCab);
// print_r($bookingDetails);
// print_r($request);
echo "</pre>";
include "./inc/edit_modal.php";
?>
<div class="bg-custom_gray pt-2 min-vh-100">
    <form action="search.php" method="POST" id="detailsReview">
        <input type="hidden" name="action" value="booking_initate">
        <input type="hidden" name="sid" value="<?= $sid ?>">
        <div class="container d-flex align-items-start bg-light-gray rounded" style="gap:20px;">
            <div class="col-md-8 p-0 rounded mt-2 mb-4">
                <div class="d-flex justify-content-between align-items-center p-2 rounded border border-success bg-success bg-opacity-10 mb-3">
                    <!-- Left: Success Message -->
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined fs-2 text-success">thumb_up</span>
                        <div>
                            <h5 class="fw-bold text-success mb-1">You got the best price available!</h5>
                            <p class="mb-0 text-success offerAmount">Congratulations! You saved <strong></strong>.</p>
                        </div>
                    </div>
                    <!-- Right: Final Price -->
                    <div class="text-end">
                        <div class="small text-muted">Final Price</div>
                        <div class="font-weight-bold text-success finalPrice">
                            ?></div>
                    </div>
                </div>
                <div class="row p-0 m-0 gap-2 align-items-center">
                    <span class="material-symbols-outlined text-primary fs-4">
                        local_taxi
                    </span>
                    <h2 class="h4 p-0 m-0 fw-bold text-dark font-weight-bold">Review Booking</h2>
                </div>
                <div class="row p-0 m-0 mt-2">
                    <div class="col-md-12 mb-3 p-0">
                        <div class="card shadow border-0 rounded">
                            <div class="card-body p-3">
                                <h5 class="mb-1 fw-bold text-dark">
                                    <?php
                                    if ($selectedCabResult['car_type'] == "hatchback") {
                                        echo "Swift, WagonR or Similar";
                                    } elseif ($selectedCabResult['car_type'] == "sedan") {
                                        echo "Dzire, Etios or Similar";
                                    } else {
                                        echo "Innova, Ertiga, Marazzo or Similar";
                                    }
                                    ?>
                                </h5>
                                <p class="mb-2 text-muted small">Sedan A/C (<?= $selectedCabResult['car_capacity'] ?> Seater)</p>
                                <div class="row align-items-center justify-content-around px-3 py-0 border-bottom">
                                    <div class="col-md-3 text-start">
                                        <img class="ml-4" src=" <?php
                                                                if ($selectedCabResult['car_type'] == "hatchback") {
                                                                    echo "./img/hatchback.png";
                                                                } elseif ($selectedCabResult['car_type'] == "sedan") {
                                                                    echo "./img/sedan.png";
                                                                } else {
                                                                    echo "./img/suv.png";
                                                                }
                                                                ?>" alt=" Cab" style="width: 85px; object-fit:cover;" />
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex justify-content-between text-center align-items-center">
                                            <?php
                                            if ($request['details'][0]['trip_type'] == 'o') {
                                                $departure = new DateTime($request['details'][0]['departure_date']);
                                                $arrival   = new DateTime($request['details'][0]['arrival_date']);

                                                // Ensure arrival is not earlier than departure
                                                if ($arrival < $departure) {
                                                    $arrival = clone $departure; // set arrival same as departure to avoid negative
                                                }

                                                $interval = $departure->diff($arrival);

                                                // Format the difference
                                                $duration = '';
                                                if ($interval->m > 0) {
                                                    $duration .= $interval->m . 'm ';
                                                }
                                                if ($interval->d > 0) {
                                                    $duration .= $interval->d . 'd ';
                                                }
                                                if ($interval->h > 0) {
                                                    $duration .= $interval->h . 'h ';
                                                }
                                                if ($interval->i > 0) {
                                                    $duration .= $interval->i . 'm';
                                                }

                                                // If duration is still empty, set it to 0h 0m
                                                if ($duration === '') {
                                                    $duration = '0h 0m';
                                                }

                                            ?>
                                                <div>
                                                    <h5 class="fw-bold text-primary mb-0">
                                                        <?php
                                                        echo date('D d, M', strtotime($request['details'][0]['departure_date'])) . '<br>' .
                                                            date('h:i A', strtotime($request['details'][0]['departure_date']));
                                                        ?>
                                                    </h5>
                                                    <small class="text-muted"><?= $request['details'][0]['pickup'] ?></small>
                                                </div>
                                                <div>
                                                    <i class="bi bi-clock-history text-warning"></i>
                                                    <p class="fw-semibold mb-0"><?php echo $duration; ?></p>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold text-primary mb-0">
                                                        <?php
                                                        if (($request['details'][0]['is_return'] == true) ||  sizeof($request['city']) > 2) {
                                                            echo date('D d, M', strtotime($request['details'][0]['arrival_date'])) . '<br>' .
                                                                date('h:i A', strtotime($request['details'][0]['arrival_date']));
                                                        } else {
                                                            echo "<p>You have seleted On way trip</p>";
                                                        }
                                                        ?>
                                                    </h5>
                                                    <small class="text-muted"><?= $request['details'][0]['pickup'] ?></small>
                                                </div>
                                            <?php } else if ($request['details'][0]['trip_type'] == 'l') {

                                                $cityId   = $request['details'][0]['cityId'];
                                                $cityName = $db->get_local_city_details($cityId);
                                                $package = str_replace('_', ' Hours , ', $request['details'][0]['fareType']) . 'Km';
                                                $tripDate = date('D d, M h:i A', strtotime($request['details'][0]['departureAt']));
                                            ?>
                                                <h5 class="fw-bold text-primary mb-0">
                                                    <?= $cityName['city_name'] ?>, <?= $package ?>, <?= $tripDate ?>
                                                </h5>


                                            <?php } else {

                                                $text = $selectedCity['airport_name'];
                                                $result = extractDetails($text);
                                                $pickupAirport = $result['city'] . ", (" . $result['terminal'] . ") " . $result['lastPart'];
                                                $location = "";
                                                if ($request['details'][0]['fareType'] == "to-airport") {
                                                    $location = $request['details'][0]['destinationCity'] . " - " . $pickupAirport;
                                                } else {
                                                    $location = $pickupAirport . " - " . $request['details'][0]['destinationCity'];
                                                }
                                            ?>
                                                <div class="d-flex gap-3">

                                                    <div>
                                                        <p class="fw-bold text-primary mb-0">
                                                            <?php echo $location ?>
                                                        </p>
                                                    </div>,

                                                    <p class="fw-bold text-primary mb-0">
                                                        <?php
                                                        echo date('D d, M', strtotime($request['details'][0]['departureAt'])) . " ," .
                                                            date('h:i A', strtotime($request['details'][0]['departureAt']));
                                                        ?>
                                                    </p>
                                                </div>
                                                <!-- <p class="m-0 p-0 mr-2">
                                                <span class="font-weight-bold mx-2"><?= date('D d, M h:i', strtotime($request['details'][0]['departureAt'])) ?> </span>
                                            </p> -->

                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-around text-muted small mt-2">
                                    <span><img src="./img/shield.png" style="width: 14px;" alt=""> Safety Verified</span>
                                    <span><img src="./img/air-conditioning.png" style="width: 14px;" alt=""> A/C Available</span>
                                    <span><img src="./img/credit-cards-payment.png" style="width: 14px;" alt=""> Pay Online</span>
                                    <span><img src="./img/chauffeur.png" style="width: 14px;" alt=""> Driver Info</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-0 m-0 mt-1">
                    <div class="col-md-12 mb-3 p-0">
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-secondary mb-3">INCLUSIONS</h6>
                                <ul class="list-unstyled mb-3">
                                    <li class="d-flex align-items-start mb-3">
                                        <img src="./img/speedometer.png" alt="" style="width:24px; margin-right:10px; margin-top:8px;">
                                        <div>
                                            <strong><?php
                                                    if ($request['details'][0]['trip_type'] == 'l') {
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
                                                        echo "$localCabKM";
                                                    } else {
                                                        echo $selectedCabResult['inc_distance'] ?> Km included
                                                <?php } ?>
                                            </strong>
                                            <div class="text-muted small">₹ <?= $selectedCabResult['extra_price'] ?>/km will apply beyond the included kms</div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start mb-3">
                                        <img src="./img/chauffeur.png" alt="" style="width:24px; margin-right:10px; margin-top:8px;">
                                        <div>
                                            <strong>Driver allowance</strong>
                                            <div class="text-muted small">Driver food and accommodation(stay) charges are included</div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start mb-3">
                                        <img src="./img/time-left.png" alt="" style="width:24px; margin-right:10px; margin-top:8px;">
                                        <div>
                                            <strong>Waiting time upto 60 mins for pickup</strong>
                                            <div class="text-muted small">₹ 100/30 mins post 60 mins</div>
                                        </div>
                                    </li>
                                    <li class="d-flex align-items-start">
                                        <img src="./img/time-left.png" alt="" style="width:24px; margin-right:10px; margin-top:8px;">
                                        <div>
                                            <strong>Sightseeing included</strong>
                                            <div class="text-muted small">Sightseeing included in Mumbai</div>
                                        </div>
                                    </li>
                                </ul>
                                <h6 class="fw-bold text-secondary mb-3">EXCLUSIONS</h6>

                                <ul class="list-unstyled">
                                    <li class="d-flex align-items-start">
                                        <img src="./img/toll.png" alt="" style="width:24px; margin-right:10px;margin-top:8px;">
                                        <div>
                                            <strong>Toll and tax charges</strong>
                                            <div class="text-muted small">Toll, State tax charges are not included (To be paid as per original receipt)</div>
                                        </div>
                                    </li>
                                </ul>
                                <a href="#" class="text-primary fw-semibold mt-1 d-inline-block">Policies &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-0 m-0 mt-1">
                    <div class="col-md-12 mb-3 p-0">
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-3">Add On Service (Optional)</h6>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" name="carrier" type="checkbox" value="carrier" id="luggageSpace">
                                    <label class="form-check-label" for="luggageSpace">
                                        Assured luggage space (either carrier or boot space) for
                                        <span class="fw-semibold">₹<?= number_format($selectedCabResult['carrier_charge'], 2) ?></span>
                                        <small class="text-muted">(GST ₹<?= number_format($selectedCabResult['carrier_gst'], 2) ?>)</small>
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" name="carModel" type="checkbox" value="carModel" id="carModel">
                                    <label class="form-check-label" for="carModel">
                                        Confirmed Car Model 2022 or above for
                                        <span class="fw-semibold">₹<?= number_format($selectedCabResult['new_car_charge'], 2) ?></span>
                                        <small class="text-muted">(GST ₹<?= number_format($selectedCabResult['new_car_gst'], 2) ?>)</small>
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" name="driverLanguage" type="checkbox" value="driverLanguage" id="driverLanguage">
                                    <label class="form-check-label" for="driverLanguage">
                                        Preferred Driver language for
                                        <span class="fw-semibold">₹840.00</span>
                                        <small class="text-muted">(GST ₹<?= number_format(0, 2) ?>)</small>
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" name="petAllowed" type="checkbox" value="petAllowed" id="petAllowed">
                                    <label class="form-check-label" for="petAllowed">
                                        Pet Allowed for
                                        <span class="fw-semibold">₹<?= number_format($selectedCabResult['pet_charge'], 2) ?></span>
                                        <small class="text-muted">(GST ₹<?= number_format($selectedCabResult['pet_gst'], 2) ?>)</small>
                                    </label>
                                </div>

                                <div class="form-check mb-0">
                                    <input class="form-check-input" name="refundable" type="checkbox" value="refundable" id="refundableBooking">
                                    <label class="form-check-label" for="refundableBooking">
                                        Upgrade to Refundable booking (100% refund for cancellation before 6 hours of departure time) for
                                        <span class="fw-semibold">₹2859.00</span>
                                        <small class="text-muted">(GST ₹<?= number_format(0, 2) ?>)</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row p-0 m-0 mt-1">
                    <div class="col-md-12 mb-3 p-0">
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-dark mb-3">
                                    <span class="material-symbols-outlined fs-5 align-middle me-2">
                                        location_on
                                    </span>
                                    Traveller Details
                                </h6>

                                <!-- Pickup Details -->
                                <div class="mb-3">
                                    <label class="font-weight-bold text-muted ">Pickup Details</label>
                                    <input type="text" name="pickupLiveLocation" value="" class="form-control contactDetails" id="pickupLocation" placeholder="Enter Pickup Location">
                                </div>

                                <!-- Traveller Contact Details -->
                                <label class="font-weight-bold text-muted ">Traveller Contact Details</label>
                                <div class="row mb-3">
                                    <div class="col-md-8 mb-2 ">
                                        <input type="text" name="customerName" value="" class="form-control contactDetails" id="travellerName" placeholder="Full Name" required>
                                    </div>
                                    <div class="col-md-4 mb-2 pl-0 ">
                                        <select class="form-control contactDetails" name="gender" id="travellerGender" required>
                                            <option value="" selected disabled>Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2 mb-2 ">
                                        <select class="form-control contactDetails" name="countryCode" id="countryCode" required>
                                            <option value="91" selected>+91</option>
                                            <option value="1">+1</option>
                                            <option value="44">+44</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-2 pl-0">
                                        <input type="tel" name="phone" value="" class="form-control contactDetails" id="travellerPhone" placeholder="Mobile No." required>
                                    </div>
                                    <div class="col-md-5 mb-2 pl-0 ">
                                        <input type="email" name="email" value="" class="form-control contactDetails" id="travellerEmail" placeholder="Email ID" required>
                                    </div>
                                </div>

                                <!-- Existing account -->
                                <div class="mb-3">
                                    <a href="#" class="text-primary fw-semibold small">
                                        <span class="material-symbols-outlined fs-6 align-middle">login</span>
                                        Log into existing account
                                    </a>
                                </div>

                                <!-- Checkbox -->
                                <div class="form-check">
                                    <input class="form-check-input" name="billingAddressAsPickupAddredd" type="checkbox" id="billingAddress" checked required>
                                    <label class="form-check-label small text-muted" for="billingAddress">
                                        Use pickup location as billing address
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p class="text-center text-muted mt-3 mb-0" style="font-size: 0.85rem;">
                    By selecting pay now, I agree to the <a href="#" class="text-decoration-none">Booking Terms and Conditions</a> and <a href="#" class="text-decoration-none">Privacy Policy</a>
                </p>
            </div>

            <div class="col-md-4 mt-3 sticky-top price_card">
                <div class="card shadow-sm border-0 rounded p-3">
                    <span class="badge badge-success position-absolute shimmer" style="font-size: 14px !important; top: -2px; right: -5px;">
                        <i class="text-white"><?php echo $discountPercent; ?>% off</i>
                    </span>
                    <h6 class="font-weight-bold text-dark mb-3 h4">Payment Options</h6>

                    <div class="list-group list-group-flush mb-2 ">
                        <!-- Part Pay -->

                        <div class="list-group list-group-flush mb-0">
                            <!-- Part Pay -->
                            <label class="list-group-item d-flex justify-content-between align-items-start mb-2 border-bottom  px-0 py-1">
                                <div class="px-2 py-1 d-flex align-items-center gap-3">
                                    <!-- Custom radio -->
                                    <label class="custom-radio me-2">
                                        <input type="radio" name="paymentOption" value="partPay" id="partPay">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div>
                                        <span class="font-weight-bold">Part Pay</span>
                                        <div class="small text-muted">Pay rest to the driver</div>
                                    </div>
                                </div>
                                <span class="font-weight-bold text-dark text-right">
                                    ₹<?= number_format($advancePay, 2) ?> <br>
                                    <small class="text-muted">(incl. GST ₹<?= number_format($grandGST, 2) ?>)</small>
                                </span>
                            </label>
                        </div>
                        <div class="list-group list-group-flush mb-0">
                            <!-- Without Any Service -->
                            <label class="list-group-item d-flex justify-content-between align-items-start mb-2 border-bottom px-0 py-1">
                                <div class="px-2 py-1 d-flex align-items-center gap-3">
                                    <!-- Custom radio -->
                                    <label class="custom-radio me-2">
                                        <input type="radio" name="paymentOption" value="payWithoutGst" id="withoutService" checked>
                                        <span class="checkmark"></span>
                                    </label>
                                    <div>
                                        <span class="font-weight-bold">Without Any Service</span>
                                        <div class="small text-muted">Base fare only</div>
                                    </div>
                                </div>
                                <span class="font-weight-bold text-dark text-right">
                                    ₹<?= number_format($discountPriceWithGST, 2) ?> <br>
                                    <small class="text-muted">(incl. GST ₹<?= number_format($selectedCabResult['gst'], 2) ?>)</small>
                                </span>
                            </label>
                        </div>


                        <div class="list-group list-group-flush mb-0">
                            <!-- Full Pay (Premium Service) -->
                            <label class="list-group-item d-flex justify-content-between align-items-start mb-2 border-bottom px-0 py-1">
                                <div class="px-2 py-1 d-flex align-items-center gap-3">
                                    <!-- Custom radio -->
                                    <label class="custom-radio me-2">
                                        <input type="radio" name="paymentOption" value="fullPay" id="fullPay">
                                        <span class="checkmark"></span>
                                    </label>
                                    <div>
                                        <!-- Title with Premium Badge and Tooltip -->
                                        <span class="font-weight-bold d-flex align-items-center gap-1">
                                            Full Pay
                                            <span class="badge bg-warning text-dark ms-2" style="font-size: 0.7rem;">
                                                Premium
                                            </span>
                                            <span class="tooltip-custom material-symbols-outlined">
                                                info
                                                <span class="tooltip-text">
                                                    Premium Service: Includes driver assistance, tolls, parking, and all convenience charges.
                                                </span>
                                            </span>
                                        </span>

                                        <!-- Subtitle -->
                                        <div class="small text-muted">All services included</div>
                                    </div>
                                </div>

                                <!-- Price -->
                                <span class="font-weight-bold text-dark text-right">
                                    ₹<?= number_format($grandTotal, 2) ?> <br>
                                    <small class="text-muted">(incl. GST ₹<?= number_format($grandGST, 2) ?>)</small>
                                </span>
                            </label>
                        </div>


                    </div>

                    <!-- Pay Button -->
                    <button class="btn w-100 text-white fw-bold" style="background: linear-gradient(to right, #1e90ff, #3a8dff); font-size: 1rem;">
                        Proceed to Payment
                    </button>

                    <div class="border-top-2 mt-3">
                        <!-- Fare Breakup -->
                        <a href="#fareBreakup"
                            class="small d-flex justify-content-between font-weight-bold text-primary d-block mt-2 text-decoration-none"
                            data-toggle="collapse"
                            role="button"
                            aria-expanded="false"
                            aria-controls="fareBreakup">
                            View Fare Break up
                            <span class="material-symbols-outlined align-middle toggle-icon">expand_more</span>
                        </a>
                    </div>

                    <div class="collapse mt-2" id="fareBreakup">
                        <div class="fare-box p-3 rounded bg-custom_gray">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Base Fare</span>
                                <span class="font-weight-bold"><?= number_format($discountPriceWithGST - $selectedCabResult['gst'], 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Driver Charges</span>
                                <span class="font-weight-bold">₹570</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Taxes &amp; Fees</span>
                                <span class="font-weight-bold">₹814</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between font-weight-bold h6 mb-0">
                                <span>Total</span>
                                <span> ₹<?= number_format($grandTotal, 2) ?></span>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>

    </form>
</div>
<?php
require './inc/footer.php';
?>
<script>
    $(document).ready(function() {
        $('#fareBreakup').on('show.bs.collapse', function() {
            $(this).prev().find('.toggle-icon').text('expand_less');
        });

        $('#fareBreakup').on('hide.bs.collapse', function() {
            $(this).prev().find('.toggle-icon').text('expand_more');
        });

        $('#detailsReview').on('submit', function(e) {
            let isValid = true;
            $(this).find('.invalid-feedback').remove();
            $(this).find('.is-invalid').removeClass('is-invalid border-danger');
            $(this).find('input[required], select[required], textarea[required]').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('is-invalid border-danger');
                    $(this).after('<div class="invalid-feedback d-block">This field is required.</div>');
                }
            });
            if (!isValid) {
                e.preventDefault();
            }
        });


    })
</script>

<?php
require './inc/foot.php';
?>