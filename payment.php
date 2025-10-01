<?php
if (!isset($_GET['bid'])) {
    header("Location: index.php");
}
require 'helper/booking_initiate.php';
require './inc/head.php';
require './inc/header.php';
require 'helper/db.php';
require 'helper/cabbazar.php';
$db = new db();
$cb = new cabbazar();
$bid = $_GET['bid'];
$bookingDetails = $db->fetch_booking_details($bid);
$sid = $bookingDetails['sid'];
$selectedCab = $db->selected_cab($sid);
$cid = $selectedCab['cid'];

$selectedCabResult = $db->get_priculler_cab_against_cid($cid);
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
include "./inc/edit_modal.php";
?>

<div class="bg-custom_gray pt-2 min-vh-100">
    <div class="container">
        <?php
        echo "<pre>";
        // print_r($selectedCabResult);
        // // print_r($selectedCab);
        // print_r($bookingDetails);
        // print_r($request);
        echo "</pre>";
        ?>
        <div class="row">
            <div class="col-md-8 p-0 rounded mt-2 mb-4">

                <div class="row p-0 m-0 gap-2 align-items-center">
                    <span class="material-symbols-outlined text-primary fs-4">
                        local_taxi
                    </span>
                    <h2 class="h4 p-0 m-0 fw-bold text-dark font-weight-bold">Review Cab & Details</h2>
                </div>
                <div class="row p-0 m-0 mt-2">
                    <div class="col-md-12 mb-3 p-0">
                        <div class="card shadow border-0 rounded mb-3">
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

                        <?php if (($results['trip_info']['trip_type'] == "o")) { ?>

                            <div class="card shadow border-0 rounded">
                                <div class="card-body p-3">
                                    <h6 class="font-weight-semibold  mb-3">Your Ininerary</h6>

                                    <div class="d-flex align-items-start justify-content-start gap-2">
                                        <div class="points d-flex flex-column align-items-center">
                                            <?php
                                            foreach ($results['cities'] as $city) {
                                            ?>
                                                <span class="point_items"></span>
                                                <div class="point_connectLine"></div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <div class="address d-flex flex-column align-items-start p-0 m-0">
                                            <?php
                                            foreach ($results['cities'] as $city) {
                                            ?>
                                                <span class="addressSection p-0"><?= $city['address'] ?></span>
                                                <span class="gap_section"></span>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>

                        <div class="card shadow border-0 rounded mt-3">
                            <div class="card shadow-sm border-0 rounded">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h6 class="font-weight-bold text-dark mb-3">Add On Service (Review)</h6>
                                        <h6 class="font-weight-bold text-dark mb-3">Seleted</h6>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Assured luggage space (either carrier or boot space)</span>
                                        <span class="font-weight-bold <?= ($bookingDetails['carrier'] == 1) ? "text-primary" : "text-danger" ?>">
                                            <?= ($bookingDetails['carrier'] == 1) ? "Yes" : "No" ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Confirmed Car Model 2022 or above</span>
                                        <span class="font-weight-bold <?= ($bookingDetails['car_model'] == 1) ? "text-primary" : "text-danger" ?>">
                                            <?= ($bookingDetails['car_model'] == 1) ? "Yes" : "No" ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Preferred Driver language</span>
                                        <span class="font-weight-bold <?= ($bookingDetails['driver_language'] == 1) ? "text-primary" : "text-danger" ?>">
                                            <?= ($bookingDetails['driver_language'] == 1) ? "Yes" : "No" ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Pet Allowed</span>
                                        <span class="font-weight-bold <?= ($bookingDetails['pet_allowed'] == 1) ? "text-primary" : "text-danger" ?>">
                                            <?= ($bookingDetails['pet_allowed'] == 1) ? "Yes" : "No" ?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between mb-0">
                                        <span>Refundable booking upgrade</span>
                                        <span class="font-weight-bold  <?= ($bookingDetails['refundable'] == 1) ? "text-primary" : "text-danger" ?>">
                                            <?= ($bookingDetails['refundable'] == 1) ? "Yes" : "No" ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="card shadow border-0 rounded mt-3">
                            <div class="card-body p-3">
                                <h6 class="font-weight-semibold  mb-3">Contact Detials</h6>

                                <!-- Pickup Details -->
                                <div class="mb-3">
                                    <input type="text" name="pickupLiveLocation" value="<?= !empty($bookingDetails['pickup_live_location'])
                                                                                            ? $bookingDetails['pickup_live_location']
                                                                                            : ($request['details'][0]['pickup'] ?? ''); ?>"
                                        class="form-control contactDetails" id="pickupLocation" readonly>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-8 mb-2">
                                        <input type="text" name="customerName" value="<?= $bookingDetails['customer_name'] ?>"
                                            class="form-control contactDetails" id="travellerName" readonly>
                                    </div>
                                    <div class="col-md-4 mb-2 pl-0">
                                        <select class="form-control contactDetails" name="gender" id="travellerGender" disabled>
                                            <option value="<?= $bookingDetails['gender'] ?>" selected><?= $bookingDetails['gender'] ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-2 mb-2">
                                        <select class="form-control contactDetails" name="countryCode" id="countryCode" disabled>
                                            <option value="<?= $bookingDetails['country_code'] ?>" selected><?= '+' . $bookingDetails['country_code'] ?></option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 mb-2 pl-0">
                                        <input type="tel" name="phone" value="<?= $bookingDetails['phone'] ?>"
                                            class="form-control contactDetails" id="travellerPhone"
                                            readonly>
                                    </div>
                                    <div class="col-md-5 mb-2 pl-0">
                                        <input type="email" name="email" value="<?= $bookingDetails['email'] ?>"
                                            class="form-control contactDetails" id="travellerEmail"
                                            readonly>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mt-3 sticky-top price_card">
                <div class="card shadow-sm border-0 rounded p-3">
                    <h5 class="font-weight-bold text-dark mb-3">Review & Payment</h5>
                    <div class="list-group list-group-flush mb-3">
                        <div class="">

                            <!-- Base Fare -->
                            <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                <div>
                                    <span class="font-weight-semibold">Base Fare</span>
                                </div>
                                <span class="font-weight-bold text-dark">
                                    ₹<?= number_format($basePrice, 2) ?> <br>
                                </span>
                            </div>

                            <?php
                            $addonTotal = 0;
                            $gstTotal   = $basegst;

                            // Carrier Charges
                            if (!empty($bookingDetails['carrier']) && $bookingDetails['carrier'] == 1) {
                                $addonTotal += $carrierChages;
                                $gstTotal   += $carrierGst;
                            ?>
                                <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                    <div>
                                        <span class="font-weight-semibold">Carrier Charge</span>
                                    </div>
                                    <span class="font-weight-bold text-dark">
                                        ₹<?= number_format($carrierChages, 2) ?> <br>
                                    </span>
                                </div>
                            <?php } ?>

                            <!-- Driver Language -->
                            <?php
                            if (!empty($bookingDetails['driver_language']) && $bookingDetails['driver_language'] == 1) {
                                $addonTotal += 500;
                                $gstTotal   += (500 * 18 / 100); // 18% GST
                            ?>
                                <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                    <div>
                                        <span class="font-weight-semibold">Driver Language</span>
                                    </div>
                                    <span class="font-weight-bold text-dark">
                                        ₹<?= number_format(500, 2) ?> <br>
                                    </span>
                                </div>
                            <?php } ?>

                            <!-- New Car -->
                            <?php
                            if (!empty($bookingDetails['car_model']) && $bookingDetails['car_model'] == 1) {
                                $addonTotal += $newCarCharges;
                                $gstTotal   += $newCarGst;
                            ?>
                                <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                    <div>
                                        <span class="font-weight-semibold">New Car</span>
                                    </div>
                                    <span class="font-weight-bold text-dark">
                                        ₹<?= number_format($newCarCharges, 2) ?> <br>
                                    </span>
                                </div>
                            <?php } ?>

                            <!-- pet charges -->
                            <?php
                            if (!empty($bookingDetails['pet_allowed']) && $bookingDetails['pet_allowed'] == 1) {
                                $addonTotal += $petCharge;
                                $gstTotal   += $petGst;
                            ?>
                                <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                    <div>
                                        <span class="font-weight-semibold">Pet Charges</span>
                                    </div>
                                    <span class="font-weight-bold text-dark">
                                        ₹<?= number_format($petCharge, 2) ?> <br>
                                    </span>
                                </div>
                            <?php } ?>

                            <!-- Refundable -->
                            <?php
                            if (!empty($bookingDetails['refundable']) && $bookingDetails['refundable'] == 1) {
                                $refundable = $basePrice / 10;
                                $addonTotal += $refundable;
                                $gstTotal   += ($refundable * 18 / 100);
                            ?>
                                <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                    <div>
                                        <span class="font-weight-semibold">Refundable Cost</span>
                                    </div>
                                    <span class="font-weight-bold text-dark">
                                        ₹<?= number_format($refundable, 2) ?> <br>
                                    </span>
                                </div>
                            <?php } ?>

                            <!-- Total GST -->
                            <div class="d-flex justify-content-between align-items-center px-0 py-1">
                                <div>
                                    <span class="font-weight-semibold">Total GST</span>
                                </div>
                                <span class="font-weight-bold text-dark">
                                    ₹<?= number_format($gstTotal, 2) ?> <br>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Final Price -->
                    <?php
                    $grandTotal = $basePrice + $addonTotal + $gstTotal;
                    ?>
                    <div class="border-top pt-2 mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="font-weight-bold h6">Final Price</span>
                                <div class="small text-muted">All services included</div>
                            </div>
                            <div class="text-right">
                                <span class="d-block mb-0 font-weight-bold h6">
                                    ₹<?= number_format($grandTotal, 2) ?>
                                </span>
                                <span class="text-muted small">incl. GST</span>
                            </div>
                        </div>
                    </div>

                    <button class="btn w-100 text-white fw-bold payNowBtn"
                        style="background: linear-gradient(to right, #1e90ff, #3a8dff); font-size: 1rem;">
                        Pay Now
                    </button>
                </div>
            </div>
        </div>


    </div>
    <!-- Confirm Modal -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Confirm Payment</h5>
                    <button type="button" class="btn-close cancelBtn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to proceed with payment?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary cancelBtn" id="cancelBtn">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmYesBtn">Yes, Pay Now</button>
                </div>
            </div>
        </div>
    </div>

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

        $('.payNowBtn').on('click', function(e) {
            e.preventDefault();
            $('#confirmModal').modal('show');
        });

        $('#confirmYesBtn').on('click', function() {
            $('#confirmModal').modal('hide');
            $.post('search.php', {
                action: "make_booking",
                bid: <?= $bid ?>
            }, function(data) {
                console.log(data);
                window.location.href = "ticket.php?bid=<?= $bid ?>";
            });
        });
        $('.cancelBtn').on('click', function() {
            $('#confirmModal').modal('hide');
        });

    })
</script>

<?php
require './inc/foot.php';
?>