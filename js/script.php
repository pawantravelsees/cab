<script>
    $(document).ready(function() {
        let yourItinerary = [];
        <?php if (isset($itinerary_list)) { ?>
            yourItinerary = JSON.parse('<?= $itinerary_list ?>');
            console.log(yourItinerary);
        <?php } ?>

        let pickupLocation = $('#pickupCity');
        let destinationLocation = $('#goingTo');

        const addItineraryList = () => {
            let your_itineraryUl = $('.your_itinerary');
            let addDestination = $('.adddestination');
            your_itineraryUl.empty();

            for (let i = 0; i < yourItinerary.length; i++) {
                your_itineraryUl.append(`
                <li class="border-0 itinerary_list" data-id="${i}">
                    ${yourItinerary[i].address}
                     <input type="text" 
                        name="locs[]" 
                        hidden readonly
                        value='{"address":"${yourItinerary[i].address}","id":"${yourItinerary[i].id}"}'>

                </li>
                <span class="material-symbols-outlined itinerary_arrow"> arrow_forward </span>
            `);
            }
            $('.itinerary_arrow:last-child').addClass('d-none');
            toggleReturnSection()
            if (yourItinerary.length > 1) {
                $('.destinationLocationRow').addClass('d-none');
                $("#goingTo").removeAttr('required', true);
                destinationLocation.removeAttr('disabled')
                addBtnEnable()
            } else if (yourItinerary.length == 1) {
                $('.yourItineraryRow').removeClass('d-none');
                $('.pickupLocationRow').addClass('d-none');
                $('.destinationLocationRow').removeClass('d-none');

                destinationLocation.removeAttr('disabled')
                destinationLocation.val("")
                addDestination.addClass('bg-light-gray no-drop-pointer')
                    .removeClass('bg-warning text-dark enabledAddBtn cursor-pointer');
            } else {
                $('.yourItineraryRow').addClass('d-none');
                $('.pickupLocationRow').removeClass('d-none');
                $('.destinationLocationRow').removeClass('d-none');
                addDestination.addClass('bg-light-gray no-drop-pointer')
                    .removeClass('bg-warning text-dark enabledAddBtn cursor-pointer');
                $('#pickupCity').val("");
                $('#goingTo').val("");
            }
        }

        const pickupCity = () => {
            if (pickupLocation.val() !== "" && !yourItinerary.includes(pickupLocation.val())) {
                $('.pickupLocationRow').addClass('d-none');
                yourItinerary.push(pickupLocation.val());
                addItineraryList();
            } else if (pickupLocation.val() === "") {
                $('.pickupLocationRow').removeClass('d-none');
            }
        }

        const destinationCity = () => {
            if (destinationLocation.val() !== "" && pickupLocation.val() !== "") {
                $('.destinationLocationRow').addClass('d-none');
                yourItinerary.push(destinationLocation.val());
                // pickupCity()
            } else {
                $('.destinationLocationRow').removeClass('d-none');
            }
        }

        const addBtnEnable = () => {
            let addDestination = $('.adddestination');
            addDestination.removeClass('bg-light-gray no-drop-pointer')
                .addClass('bg-warning text-dark enabledAddBtn cursor-pointer');
            addDestination.off('click').on('click', () => {
                $('.destinationLocationRow').removeClass('d-none');
                $('#goingTo').val("");
            });
        }

        const removeItineraryList = (element) => {
            let dataId = $(element).attr('data-id');
            yourItinerary.splice(dataId, 1);
            addItineraryList();
        }
        $(document).on('click', '#searchCar .itinerary_list', function(e) {
            // e.preventDefault();
            removeItineraryList(this);
        });

        function toggleReturnSection() {
            if ($('#isReturn').is(':checked') || yourItinerary.length > 2) {
                $('.arrivalAt').closest('.col-md-6').removeClass('d-none');
                $('.add-return-date').addClass('d-none');
            } else {
                $('.arrivalAt').closest('.col-md-6').addClass('d-none');
                $('.add-return-date').removeClass('d-none');
            }
        }

        $('input[name="roundTrip"]').on('change', function() {
            toggleReturnSection();
        });

        $('.add-return-date a').on('click', function() {
            $('#isReturn').prop('checked', true);
            toggleReturnSection();
        });

        $('.arrivalAt .date-close').on('click', function() {
            $('#oneWayTrip').prop('checked', true);
            toggleReturnSection()
        });



        const pickupInput = $('#pickupCity');
        const destinationInput = $('#goingTo');
        const suggestionBox = $('#pickupSuggestions');
        const destinationSuggestionBox = $('#destinationSuggestions');
        const locations = $('#locations');

        $(pickupInput).on('input focus', function() {

            let query = $(this).val().toLowerCase() ;
            if(query == null || query == ""){
            query = "del"
            }
            suggestionBox.empty();

            if (query.length === 0) {
                suggestionBox.addClass('d-none');
                return;
            }

            $.post('api.php', {
                action: "suggestion",
                query: query
            }, (data) => {
                if (data.length === 0) {
                    suggestionBox.addClass('d-none');
                    return;
                }
                data.forEach(city => {
                    suggestionBox.append(`<li data-id="${city.id}">${city.city_name}</li>`);
                });
                suggestionBox.removeClass('d-none');
            });
        });

        $(destinationInput).on('input focus', function() {
            
            let query = $(this).val().toLowerCase() ;
            if(query == null || query == ""){
            query = "a"
            }
            destinationSuggestionBox.empty();

            if (query.length === 0) {
                destinationSuggestionBox.addClass('d-none');
                return;
            }

            $.post('api.php', {
                action: "suggestion",
                query: query
            }, (data) => {
                if (data.length === 0) {
                    destinationSuggestionBox.addClass('d-none');
                    return;
                }
                data.forEach(city => {
                    destinationSuggestionBox.append(`<li data-id="${city.id}">${city.city_name}</li>`);


                });
                destinationSuggestionBox.removeClass('d-none');
            });
        })

        $(document).on('click', '#pickupSuggestions li', function() {
            pickupInput.val($(this).text());
            let city_name = $(this).text()
            let id = $(this).attr('data-id')
            yourItinerary.push({
                address: city_name,
                id: id
            })
            suggestionBox.empty().addClass('d-none');
            addItineraryList()
        });

        $(document).on('click', '#destinationSuggestions li', function() {
            destinationInput.val($(this).text());
            let city_name = $(this).text()
            let id = $(this).attr('data-id')
            yourItinerary.push({
                address: city_name,
                id: id
            })
            destinationSuggestionBox.empty().addClass('d-none');
            addItineraryList()
        });

        $(document).click(function(e) {
            if (!$(e.target).closest('.pickupLocationRow').length) {
                suggestionBox.empty().addClass('d-none');
            }
        });

        // $('#searchCar').on('submit', function (e) {
        // e.preventDefault();
        //     console.log(yourItinerary); 
        // })
        //     $('#searchCar button').attr('disabled', true);
        //     $('#searchCar button').html(`<span class="loader"></span>`)
        //     //     e.preventDefault();
        //     let departureAt = $('#departureAt').val();
        //     let isReturn = $('#isReturn').is(':checked');
        //     let arrivalAt = $('#arrivalAt').val();
        //     let requestData = {
        //         departureAt: departureAt,
        //         isReturn: isReturn,
        //         itinerary: yourItinerary,
        //     };

        //     if (isReturn === true || yourItinerary.length > 2) {
        //         requestData.arrivalAt = arrivalAt;
        //     } else {
        //         requestData.arrivalAt = ""
        //     }

        //     if (departureAt !== null && departureAt !== "" && departureAt !== undefined && requestData.itinerary.length > 1) {
        //         $.post("search.php", { action: "searchOutstation", formData: requestData }, (response) => {
        //             console.log(response);

        //             // location.href = "fare.php";
        //         })
        //     } else {
        //         console.log("Not ok");
        //     }
        // });

        addItineraryList()

    });

    $(document).ready(function() {
        $.post('api.php', {
            action: "localCity"
        }, (data) => {
            let localCity = data.localCity;
            let localCityPackage = data.localCityPackage;
            let airports = data.airports;

            // console.log(data);
            let selectedCity = "<?= isset($selectedCity['city_name']) ? $selectedCity['city_name'] : '' ?>";

            localCity.forEach(element => {
                let isSelected = (selectedCity === element.city_name) ? 'selected' : '';
                $('#selectCity').append(`
            <option value="${element.id}" ${isSelected}>${element.city_name}</option>
        `);
            });
            let selectedPackage = "<?= $request['details'][0]['fareType'] ?? '' ?>";

            localCityPackage.forEach((element) => {
                let packageType;
                switch (element.package_type) {
                    case "2_20":
                        packageType = "2h , 20KM";
                        break;
                    case "4_40":
                        packageType = "4h , 40KM";
                        break;
                    case "8_80":
                        packageType = "8h , 80KM";
                        break;
                    case "12_120":
                        packageType = "12h , 120KM";
                        break;
                    default:
                        packageType = element.package_type;
                        console.log("something went wrong");
                        break;
                }

                // check if this package should be selected
                let isSelected = (element.package_type === selectedPackage) ? "selected" : "";

                $('#selectPackage').append(`
                    <option value="${element.package_type}" ${isSelected}>${packageType}</option>
                    `);
            });

            let selectedAirport = "<?= $selectedCity['airport_id'] ?? '' ?>";

            airports.forEach((element) => {
                let isSelected = (element.airport_id === selectedAirport) ? "selected" : "";
                $('#selectAirport').append(`
            <option value="${element.id}" ${isSelected}>${element.airport_name}</option>
        `);
            });

        })
        $(".bookingType").on("click", function() {
            $(".bookingType").removeClass("active");
            $(this).addClass("active");
        });


        $('.outstation').on('click', function() {
            $('#localTrip').addClass('d-none').removeClass('d-block')
            $('#searchCar').removeClass('d-none').addClass('d-block')
            $('input[type="hidden"]').val("outstation")
            $('input[name="pickupCity"]').prop('required', true);
            $('input[name="goingTo"]').prop('required', true);
            $('#selectCity').removeAttr('required');
            $("#selectPackage").removeAttr('required');
            $('#selectRoute').removeAttr('required');
            $('#selectAirport').removeAttr('required');
            $('#selectDestinationcity').removeAttr('required');
        })
        $('.localTripType').on('click', function() {
            $('#localTrip').removeClass('d-none').addClass('d-block')
            $('#searchCar').addClass('d-none').removeClass('d-block')
            $('input[name="pickupCity"]').removeAttr('required')
            $('input[name="goingTo"]').removeAttr('required')
            $('input[type="hidden"]').val("local")
            $('#selectCity').prop('required', true);
            $("#selectPackage").prop('required', true);
            $('#selectRoute').removeAttr('required');
            $('#selectAirport').removeAttr('required');
            $('#selectDestinationcity').removeAttr('required');
            console.log("local");

            $('input[name="local-trip-type"]').on('change', function(e) {
                if ($(this).val() == "Local Rental") {
                    $("#localCity").addClass('d-block').removeClass("d-none")
                    $("#airport").addClass('d-none').removeClass("d-block")
                    $('input[type="hidden"]').val("local")
                    $('#selectCity').prop('required', true);
                    $("#selectPackage").prop('required', true);
                    $('#selectRoute').removeAttr('required');
                    $('#selectAirport').removeAttr('required');
                    $('#selectDestinationcity').removeAttr('required');
                    $('input[name="pickupCity"]').removeAttr('required')

                } else {
                    $("#airport").addClass('d-block').removeClass("d-none")
                    $("#localCity").addClass('d-none').removeClass("d-block")
                    $('input[type="hidden"]').val("airport")
                    $('#selectCity').removeAttr('required');
                    $("#selectPackage").removeAttr('required');
                    $('#selectRoute').prop('required', true);
                    $('#selectAirport').prop('required', true);
                    $('#selectDestinationcity').prop('required', true);
                    $('input[name="pickupCity"]').removeAttr('required')

                    $('#selectRoute').on('change', function() {
                        let airportRoute = $(this).val();
                        let sp = $('#selectAirport').on('change', function(e) {
                            $('.destinationCityRow').removeClass('d-none').addClass('d-block')
                            if (airportRoute === "from-airport") {
                                $('#selectDestinationcity option:first').text('Select destination city');
                            } else if (airportRoute === "to-airport") {
                                $('#selectDestinationcity option:first').text('Select pickup city');
                            }

                       
                                $.get('api.php', {
                                    action: "get_local_airport_cities",
                                    airportId: $(this).val(),
                                    fareType: airportRoute
                                }, (data) => {
                                    // console.log(data);
                                    // return;
                                    // let cities = JSON.parse(data);
                                    $('.showLocalCities').empty();
                                    $('.showLocalCities').append(`<option selected disabled value=""></option>`)
                                    if (airportRoute === "from-airport") {
                                        $('#selectDestinationcity option:first').text('Select destination city');
                                    } else if (airportRoute === "to-airport") {
                                        $('#selectDestinationcity option:first').text('Select pickup city');
                                    }
                                    data.forEach(city => {
                                        $('.showLocalCities').append(
                                            `<option value="${city}">${city}</option>`
                                        );
                                    });
                                })
                     
                            return

                        })


                        console.log(sp.val());

                        if (airportRoute === "from-airport") {
                            $('#selectDestinationcity option:first').text('Select destination city');
                        } else if (airportRoute === "to-airport") {
                            $('#selectDestinationcity option:first').text('Select pickup city');
                        }

                    })

                }
            });
        })

    });
</script>