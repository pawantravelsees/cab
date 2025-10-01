<?php
if (!isset($_GET['sid'])) {
    header("Location: index.php");
}
require './inc/head.php';
require './inc/header.php';
require 'helper/db.php';
$db = new db();

$sid = $_GET['sid'];
$request = $db->get_search_request($sid);
// echo "<pre>";
// print_r($request);
// echo "</pre>";
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

?>
<?php
include "./inc/edit_modal.php";
?>
<div class="bg-custom_gray pt-3 min-vh-100">
    <div class="container d-flex align-items-start gap-3 bg-light-gray">
        <div class="col-md-3 h-100 p-0" id="filter">
        </div>
        <div class="col-md-9 m-0 p-0">
            <div class="container d-flex justify-content-center align-items-end flex-column">
                <div class="w-100 imgSection">
                    <img src="./img/Anniversary-33.jpg" alt="" class="border object-fit-contain px-0 mb-3 img-fluid rounded d-none d-md-block">
                </div>
                <div class="col-md-12 d-flex justify-content-between">
                    <div class="mx-0 p-0">
                        <span class="small resultCount"><?php //echo $resultsummary[0]['total_result'] ?></span>
                    </div>
                    <div class="mb-3 col-md-2 mx-0 p-0">

                        <form id="recommendation">
                            <select id="sortOptions" name="short_by" class="custom-select custom-select-sm shadow-none">
                                <option value="" selected>Recommended</option>
                                <option value="low_high">Price (Low to High)</option>
                                <option value="high_low">Price (High to Low)</option>
                                <option value="duration">Duration (Less to More)</option>
                                <option value="gst">With GST</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            <div id="result" class="col-md-12">
            </div>
        </div>

        <div class="modal" id="cabBookingModal" tabindex="-1" aria-labelledby="cabBookingModalLabel" aria-hidden="true">
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="modal-content rounded-4 shadow overflow-hidden" style="width:400px; ">
                    <div class="modal-header px-3 py-3">
                        <h5 class="modal-title " id="cabBookingModalLabel">Edit Search</h5>
                        <button type="button"
                            class="btn-close btn btn-link p-0 border-0 shadow-none text-white d-flex align-items-center"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                            <span class="material-symbols-outlined fs-4">close</span>
                        </button>
                        </button>
                    </div>
                    <div class="modal-body" style="padding:10px 10px;">
                        <?php include('./inc/search-form.php') ?>
                    </div>
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
        let array = 6;
        for (let i = 0; i <= array; i++) {
            $("#result").append(
                `<div class="col-md-12 mb-3 h-auto w-auto p-2 d-flex border border-gray rounded items">
            <div class="col-md-3 p-0 m-0">
                <div class="animated-background" style="height: 150px; width:100%; border-radius:10px; margin-bottom:0px;"></div>
            </div>
            <div class="col-md-7 py-0 px-4">
                <div class="row">
                    <div class="animated-background" style="height: 20px; width:100px; border-radius:0px; margin-bottom:10px;"></div>
                </div>
                <div class="row mt-1">
                    <div class="animated-background" style="height: 30px; width:50%; border-radius:0px; margin-bottom:10px;"></div>
                </div>
                <div class="row mt-1">
                    <div class="col-md-10 m-0 p-0">
                        <ul class="list-unstyled p-0 m-0 inlcuded_list">
                            <li class="m-0 p-0">
                                <div class="animated-background" style="height: 15px; width:160px; border-radius:0px; margin-bottom:10px;"></div>

                            </li>
                            <li class="m-0 p-0 ">
                                <div class="animated-background" style="height: 15px; width:150px; border-radius:0px; margin-bottom:10px;"></div>

                            </li>
                            <li class="m-0 p-0 ">
                                <div class="animated-background" style="height: 15px; width:150px; border-radius:0px; margin-bottom:10px;"></div>

                            </li>
                            <li class="m-0 p-0 ">
                                <div class="animated-background" style="height: 15px; width:120px; border-radius:0px; margin-bottom:10px;"></div>

                            </li>
                            <li class="m-0 p-0 ">
                                <div class="animated-background" style="height: 15px;width:140px; border-radius:0px; margin-bottom:0px;"></div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-2 bg-light price_section d-flex flex-column align-items-end justify-content-center">
                <div class="animated-background" style="height: 30px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
                <div class="animated-background" style="height: 30px; width:117px; border-radius:5px; margin-bottom:10px;"></div>

            </div>
            </div>`
            );
        }

        $("#filter").append(
            `
            <div class="card shadow-sm p-3 mb-3 rounded ">
    <div class="mb-3 p-0 m-0">
        <div class="d-flex flex-column gap-1">
            
    
           <div class="animated-background" style="height: 20px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
           <div class="animated-background" style="height: 20px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
           <div class="animated-background" style="height: 20px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
           <div class="animated-background" style="height: 20px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
           <div class="animated-background" style="height: 20px; width:100px; border-radius:5px; margin-bottom:10px;"></div>
        </div>
    </div>

    <div class="mb-3">
        <div class="d-flex justify-content-between">
        <div class="animated-background" style="height: 20px; width:100%; border-radius:5px; margin-bottom:10px;"></div>
        </div>
    </div>

    <div>
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
            <div class="animated-background" style="height: 20px; width:70px; border-radius:5px; margin-bottom:0px;"></div>
            <div class="animated-background" style="height: 20px; width:70px; border-radius:5px; margin-bottom:0px;"></div>
            <div class="animated-background" style="height: 20px; width:70px; border-radius:5px; margin-bottom:0px;"></div>
            <div class="animated-background" style="height: 20px; width:70px; border-radius:5px; margin-bottom:0px;"></div>
        </div>
    </div>
    </div>`
        )
        let intervalTime = (parseInt("<?= $scp ?>") == 2) ? 100 : 4000;
        let intervalId = setInterval(function() {
            $.post("api.php", {
                action: "get_scp",
                "sid": <?= $sid ?>
            }, (data) => {
                if (data == 2) {
                    clearInterval(intervalId);
                    $.post("inc/filter-template.php", {
                        "sid": "<?= $sid ?>"
                    }, (data) => {
                        $("#filter").html(data);
                    })
                    $.post("inc/summary-template.php", {
                        "sid": "<?= $sid ?>"
                    }, (data) => {
                        $("#result").html(data);
                    })
                } else {}
            })
        }, intervalTime)
    })
    $(document).ready(function() {
        $(".modifyBtn").on("click", function(e) {
            e.preventDefault();
            let tripType = $(this).attr('data-tripType')
            if (tripType == "o") {
                $("#cabBookingModal").modal("show");
                $('#pickupCity').attr('required', true)
                $('.outstation').addClass('active')
                $('.localTripType').removeClass('active')
                $('#selectDestinationcity').removeAttr('required')
            } else if (tripType == "l") {
                $('.outstation').removeClass('active')
                $('.localTripType').addClass('active')
                $("#searchCar").removeClass('d-block')
                $("#searchCar").addClass('d-none')
                $('#pickupCity').removeAttr('required')
                $("#localTrip").addClass('d-block')
                $("#localTrip").removeClass('d-none')
                $("#airport").removeClass('d-block')
                $("#airport").addClass('d-none')
                $("#cabBookingModal").modal("show");
                $('#selectDestinationcity').removeAttr('required')
            } else {
                getLocalCitiesAgainstAirport()
                $('.outstation').removeClass('active')
                $('.localTripType').addClass('active')
                $('#pickupCity').removeAttr('required')
                $('#selectDestinationcity').attr('required', true)
                $("#searchCar").removeClass('d-block')
                $("#searchCar").addClass('d-none')
                $("#localTrip").addClass('d-block')
                $("#localTrip").removeClass('d-none')
                $("#localCity").addClass('d-none')
                $("#localCity").removeClass('d-block')
                $("#airport").removeClass('d-none')
                $("#airport").addClass('d-block')
                $("#cabBookingModal").modal("show");

            }
        })

        $(".btn-close").on("click", function() {
            $("#cabBookingModal").modal("hide");
        });
    })


    $(document).ready(function() {
        let today = new Date();
        let departTime = new Date(today.getTime() + 65 * 60 * 1000);
        let depDateStr = "<?= (!empty($results['trip_info']['departure_date']))
                                ? date('d-M-Y H:i', strtotime($results['trip_info']['departure_date']))
                                : date('d-M-Y H:i', strtotime('today')) ?>";
        let ariDateStr = "<?= (!empty($results['trip_info']['arrival_date']))
                                ? date('d-M-Y', strtotime($results['trip_info']['arrival_date']))
                                : date('d-M-Y', strtotime('today')) ?>";
        let localPickupAt = "<?= (!empty($request['details'][0]['departureAt']))
                                    ? date('d-M-Y H:i', strtotime($request['details'][0]['departureAt']))
                                    : date('d-M-Y H:i', strtotime('today')) ?>";

        let depDate = new Date(depDateStr);
        let ariDate = new Date(ariDateStr);
        let arrivalAt = $("#arrivalAt").flatpickr({
            enableTime: false,
            dateFormat: "d-M-Y",
            minDate: depDate,
            defaultDate: ariDate
        });

        let depTime = $("#departureAt").flatpickr({
            enableTime: true,
            dateFormat: "d-M-Y H:i",
            minDate: departTime,
            defaultDate: depDate,
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    // console.log(selectedDates);
                    let newDep = selectedDates[0];
                    arrivalAt.set("minDate", newDep);
                    if (arrivalAt.selectedDates.length === 0 || arrivalAt.selectedDates[0] <= newDep) {
                        arrivalAt.setDate(newDep, false);
                    }
                }
            }
        });
    });

    $(document).ready(function() {
        let today = new Date();
        let departTime = new Date(today.getTime() + 65 * 60 * 1000);
        let localDepartureAt = $("#localPickupAt").flatpickr({
            enableTime: true,
            dateFormat: "d-M-Y H:i",
            minDate: departTime,
            defaultDate: "<?= (isset($request['details'][0]['departureAt'])) ? date('d-M-Y H:i', strtotime($request['details'][0]['departureAt'] ?? 'now')) : '' ?>"
        });
    })

    function getLocalCitiesAgainstAirport() {
        let airportRoute = $('#selectRoute').val()
        $.get('api.php', {
            action: "get_local_airport_cities",
            airportId: $('#selectAirport').val(),
            fareType: airportRoute
        }, (data) => {
            $('.showLocalCities').empty();
            $('.showLocalCities').append(`<option selected disabled value=""></option>`)
            if (airportRoute === "from-airport") {
                $('#selectDestinationcity option:first').text('Select destination city');
            } else if (airportRoute === "to-airport") {
                $('#selectDestinationcity option:first').text('Select pickup city');
            }
            let selectedCity = "<?= isset($request['details'][0]['destinationCity']) ? $request['details'][0]['destinationCity'] : '' ?>";
            data.forEach(city => {
                let isSelected = (city == selectedCity) ? "selected" : "";
                $('#selectDestinationcity').append(
                    `<option value="${city}" ${isSelected}>${city}</option>`
                );
            });

        })
    }

  
</script>

<?php
require './inc/foot.php';
?>