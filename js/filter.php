<script>
    $(document).ready(function() {
        $('#filterForm , #recommendation').on('change', function() {
            let formData = $('#filterForm, #recommendation').serialize();
            // console.log(formData);
            $.ajax({
                url: "./inc/summary-template.php?" + formData,
                method: 'get',
                data: {
                    'action': 'filter_call',
                    'sid': '<?= $sid; ?>'
                },
                success: function(data) {
                    $('#result').html(data);
                }
            })
        });

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
            resetFilter("#recommendation"); // form with the <select>
        });



    });
</script>