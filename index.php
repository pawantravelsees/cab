<?php
$pageTitle = "Tripodeal Cabs - Home Page";
require './inc/head.php';
require './inc/header.php';
?>
<!-- loader -->
<!-- Page Loader -->

<!-- loader -->
<!-- Page content start -->
<!-- hero section start-->
<div class="container hero_section p-4">
    <div class="container">
        <div class="row">
            <div class="col-md-5 bg-light rounded-pill overflow-hidden ">
                <div class="row rounded justify-content-center">
                    <div class="col-md-6 bg-warning ">
                        <a href="#" class="d-flex nav-link flex-column text-center text-dark p-0 mt-1">
                            <span class="material-symbols-outlined" style="vertical-align: middle;font-size:35px;margin-bottom:-5px;">local_taxi</span>
                            <span>Cabs</span>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a class="d-flex nav-link flex-column text-center text-dark p-0 mt-1" href="#">
                            <span class="material-symbols-outlined" style="vertical-align: middle;font-size:35px;margin-bottom:-5px;">directions_bus</span>
                            <span class="mb-0">Bus Tickets</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-5 bg-light rounded-xl overflow-hidden p-0 pb-2" style="min-height: 400px; ">
                <div class="top bg-warning w-full p-0">
                    <p class="text-center py-1 h5">All India Cab Service</p>
                </div>
                <div class="row mx-3 align-items-center mt-4">
                    <div class="col-md-12">
                        <div class="row align-items-center flex-md-nowrap justify-content-between">
                            <div class="col-md-6 px-1">
                                <p class=" text-center text-lg py-2-5 rounded-xl cursor-pointer  hover-yellow-box mb-2 bookingType active outstation">Outstation</p>
                            </div>
                            <div class="col-md-6 px-1">
                                <p class=" text-center text-lg py-2-5 rounded-xl cursor-pointer hover-yellow-box mb-2 bookingType localTripType"> Local / Airport</p>
                            </div>
                        </div>
                    </div>

                    <!-- outestanding form -->
                    <div class="col-md-12">
                        <?php
                        include "./inc/search-form.php";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- hero section end -->
<!-- Page content End -->
<?php
require './inc/footer.php';
?>
<script>

</script>
<script>
    $(document).ready(function() {
        let today = new Date();
        let depTime = new Date(today.getTime() + 65 * 60 * 1000);
        let arrivalAt = $("#arrivalAt").flatpickr({
            dateFormat: "d-M-Y",
            minDate: depTime,
            defaultDate: depTime
        });
        let depPicker = $("#departureAt").flatpickr({
            enableTime: true,
            dateFormat: "d-M-Y H:i",
            minDate: depTime,
            defaultDate: depTime,
            onChange: function(selectedDates) {
                if (selectedDates.length > 0) {
                    let newDep = selectedDates[0];
                    arrivalAt.set("minDate", newDep);
                    if (
                        arrivalAt.selectedDates.length === 0 ||
                        arrivalAt.selectedDates[0] < newDep
                    ) {
                        arrivalAt.setDate(newDep, true);
                    }
                }
            }
        });
    });

    $(document).ready(function() {
        let today = new Date();
        let depTime = new Date(today.getTime() + 65 * 60 * 1000);
        let localPickup = $("#localPickupAt").flatpickr({
            enableTime: true,
            dateFormat: "d-M-Y H:i",
            minDate: depTime,
            defaultDate: depTime,
        });
    })



    
</script>

<?php
require './inc/foot.php';
?>