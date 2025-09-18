<script>
    function submit_filter_form(page=1){
        let formData = $('#filterForm, #recommendation').serialize();
            // console.log(formData);
            $.ajax({
                url: "./inc/summary-template.php?" + formData,
                method: 'get',
                data: {
                    'action': 'filter_call',
                    'sid': '<?= $sid; ?>',
                    "page":page
                },
                success: function(data) {
                    $('#result').html(data);
                }
            })
    }
    $(document).ready(function() {
        var page=1;
        $('#filterForm , #recommendation').on('change', function() {
            submit_filter_form(page);
        });
        $(document).off("click", ".page-link").on("click", ".page-link", function(e){
            e.preventDefault();
            page = $(this).attr("page_no");
            submit_filter_form(page);
        })

        function resetFilter(selector) {
            $(`${selector} input[type=checkbox], ${selector} input[type=radio]`).prop("checked", false);
            $(`${selector} input[type=text], ${selector} input[type=number]`).val("");
            $(`${selector} select`).prop("selectedIndex", 0);
            let slider = $(`${selector} .js-range-slider`).data("ionRangeSlider");
            if (slider) {
                slider.update({
                    from: slider.options.min,
                    to: slider.options.max
                });
            }

            let formData = $('#filterForm, #recommendation').serialize();
            $.get("./inc/summary-template.php?" + formData, {
                action: 'filter_call',
                sid: '<?= $sid; ?>'
            }, function(data) {
                $('#result').html(data);
            });
        }

        $(document).on('click', '#carTypeFiterReset , #carType', function(e) {
            e.preventDefault();
            resetFilter(".carTypeFilter");
        });

        $(document).on('click', '#priceRangeReset ,#priceRange', function(e) {
            e.preventDefault();
            resetFilter(".priceRangeFilter")
        });

        $(document).on('click', '#carCapacityReset , #seatCapacity', function(e) {
            e.preventDefault();
            resetFilter(".seatingCapacityFilter");
        });

        $(document).on('click', '#sortReset', function(e) {
            e.preventDefault();
            resetFilter("#recommendation");
        });

        $(document).on('click', '#resetAllFilter', function(e) {
            setTimeout(() => {
                resetFilter(".priceRangeFilter")
            }, 100)
            setTimeout(() => {
                resetFilter(".carTypeFilter")
            }, 200)
            setTimeout(() => {
                resetFilter(".seatingCapacityFilter")
            }, 300)
            setTimeout(() => {
                resetFilter("#recommendation")
            }, 400)
        });



    });
</script>