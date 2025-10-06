
<?php
$pageTitle = "Tripodeal Cabs - Home Page";
require './inc/head.php';
require './inc/header.php';
?>
<!-- Page content start -->
<!-- hero section start-->
<div class="container hero_section p-0 ">
    <div class="container" style="height: auto;">
        <div class="row ">
            <div class="my-3">
                <h5 class="bold text-light p-2 rounded m-0" style="background-color: rgba(0, 0, 0, 0.5); display:inline-block;">Find Cabs</h5>
            </div>
        </div>
        <div class="row mt-0 gap-2 flex-nowrap align-items-stretch">
            <!-- Left Column -->
            <div class="col-md-4 bg-light rounded overflow-hidden p-0 mr-3" style="min-height: 350px;">
                <div class="row mx-2 align-items-start mt-3">
                    <?php include "./inc/search-form.php"; ?>
                </div>
            </div>

            <!-- Right Column -->
            <div class="col-md-8 p-0 m-0 rounded overflow-hidden border d-flex" style="min-height: 350px;">
                <img src="./img/Refund-VSF.jpg"
                    alt="festival Sell"
                    class="rounded img-fluid object-fit-cover w-100 h-100">
            </div>
        </div>
    </div>
    <div class="wave-header">
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7"></use>
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)"></use>
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)"></use>
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff"></use>
            </g>
        </svg>
    </div>
    <div class="container ">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 mt-2 mb-4 mt-sm-12 text-center"><br>
                <img src="/img/footer-trustlogo_new.png?dd" class="img-fluid d-none d-md-inline" style="max-width:800px;">
                <img src="/img/footer-trustlogo_new.png?dd" class="img-fluid d-inline d-md-none">
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