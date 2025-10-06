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
$bookings = $db->get_bookings($sid);
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
<?php
// echo "<pre>";
// print_r($bookings);
// print_r($request);
// echo "</pre>";
?>
<?php if (!empty($bookings)) { ?>
    <div class="bg-custom_gray pt-2 min-vh-100 ">
        <div class="container pb-4">
            <div class="ticket-container ">
                <div class="ticket-header">
                    <div>
                        <h2>Tripodeal</h2>
                        <small>Booking Date & Time: <?= date('d M Y H:i', strtotime($bookings['created_at'])) ?></small>
                    </div>
                    <div class="text-right">
                        <p class="ticketStatus">SastaSafar Booking ID: <strong>
                                <?php
                                $prefix = "SSFC";
                                $id = $bookings['booking_id'];
                                $paddedNumber = str_pad($id, 8, "0", STR_PAD_LEFT);
                                $bookingId = $prefix . $paddedNumber;
                                echo $bookingId;
                                ?>

                            </strong></p>
                        <p>Status: <span class="badge badge-warning badge-status"><?= ucfirst($bookings['payment_status']) ?></span></p>
                    </div>
                </div>

                <div class="card-custom">
                    <h5 class="section-title">Trip Information</h5>
                    <p class="d-flex justify-content-between"><strong>Trip Type:</strong> <?php
                                                                                            if ($results['trip_info']['trip_type'] == "o") {
                                                                                                echo "Outstation";
                                                                                            } elseif ($results['trip_info']['trip_type'] == "a") {
                                                                                                echo "Airport";
                                                                                            } else {
                                                                                                echo "Local";
                                                                                            }
                                                                                            ?></p>
                    <p class="d-flex justify-content-between"><strong>Car Type:</strong><?= $selectedCabResult['car_type'] ?> </p>
                    <p class="d-flex justify-content-between"><strong>Departure:</strong> <?php if ($results['trip_info']['trip_type'] == "o") {
                                                                                                echo date('d-M-Y H:i', strtotime($request['details'][0]['departure_date']));
                                                                                            } else {
                                                                                                echo date('d-M-Y H:i', strtotime($request['details'][0]['departureAt']));
                                                                                            }
                                                                                            ?></p>

                    <?php if (isset($request['details'][0]['arrival_date']) && $request['details'][0]['arrival_date']) { ?>

                        <p class="d-flex justify-content-between"><strong>Arrival:</strong> <?= date('d-M-Y', strtotime($request['details'][0]['arrival_date'])) ?></p>
                    <?php
                    }
                    ?>
                    <p class="d-flex justify-content-between"><strong>Pickup Address:</strong> <?= !empty($bookingDetails['pickup_live_location'])
                                                                                                    ? $bookingDetails['pickup_live_location']
                                                                                                    : ($request['details'][0]['pickup'] ?? ''); ?></p>
                </div>
                <div class="border rounded mb-3">

                    <?php if (($results['trip_info']['trip_type'] == "o")) { ?>

                        <div class="card shadow border-0 rounded">
                            <div class="card-body p-3">
                                <h5 class="section-title">Your Ininerary</h5>

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
                </div>
                <div class="card-custom">
                    <h5 class="section-title">Fare Summary</h5>
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
                    <div class="border-top pt-2">
                        <div class="d-flex justify-content-between">
                            <div>
                                <span class="font-weight-bold h6">Final Price</span>
                                <!-- <div class="small text-muted">All services included</div> -->
                            </div>
                            <div class="text-right">
                                <span class="d-block mb-0 font-weight-bold h6">
                                    ₹<?= number_format($grandTotal, 2) ?>
                                </span>
                                <!-- <span class="text-muted small">incl. GST</span> -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-custom">
                    <h5 class="section-title">Customer Details</h5>
                    <p><strong>Name: </strong> <?= $bookingDetails['customer_name'] ?></p>
                    <p><strong>Phone: </strong><?= $bookingDetails['phone'] ?></p>
                    <p><strong>Email: </strong> <?= $bookingDetails['email'] ?></p>
                </div>

                <div class="text-center small-text">
                    This ticket is system-generated and valid only after confirmation & payment.
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="bg-custom_gray pt-2 py-4">
        <div class="container">
            <h4>Somthing went wrong Please try again</h4>
        </div>
    </div>
<?php } ?>
<?php
require './inc/footer.php';
?>
<?php
require './inc/foot.php';
?>