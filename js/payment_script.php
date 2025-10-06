<script>
    $(document).ready(function() {
        const mainFunction = () => {
            $('[name="paymentOption"]').on('change', function(e) {
                e.preventDefault();
                let paymentOption = getPaymentOptionValue();
                toggleAddons(paymentOption);

            });
        }

        function getPaymentOptionValue() {
            let selected = document.querySelector('[name="paymentOption"]:checked');
            return selected ? selected.value : null;
        }

        function toggleAddons(paymentOption) {
            const addons = $('[name="carrier"], [name="carModel"], [name="driverLanguage"], [name="petAllowed"], [name="refundable"]');

            if (paymentOption === "fullPay") {
                addons.prop('checked', true);
            }
            setFareBreakDrown(paymentOption);
        }
        $('[name="carrier"], [name="carModel"], [name="driverLanguage"], [name="petAllowed"], [name="refundable"]').on('change', function() {
            const addons = $('[name="carrier"], [name="carModel"], [name="driverLanguage"], [name="petAllowed"], [name="refundable"]');
            const totalAddons = addons.length;
            const checkedAddons = addons.filter(':checked').length;

            if (checkedAddons === totalAddons) {
                $('[name="paymentOption"][value="fullPay"]').prop('checked', true).trigger('change');
            } else {
                $('[name="paymentOption"][value="payWithoutGst"]').prop('checked', true).trigger('change');
            }
        });
        $('[name="paymentOption"]').on('change', function() {
            toggleAddons($(this).val());
        });
        toggleAddons($('[name="paymentOption"]:checked').val());

        function setFareBreakDrown(paymentOption) {
            let breakdownHtml = '';

            if (paymentOption === 'fullPay') {
                $('.finalPrice').text(`   ₹<?= number_format($grandTotal, 2) ?>`)
                $('.offerAmount strong').text('₹<?= number_format($selectedCabResult['price'] - $discountPrice, 2) ?>')
                breakdownHtml = `
                <div class="fare-box p-2 rounded bg-custom_gray">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Base Fare</span>
                        <span class="font-weight-bold">₹<?= number_format($basePrice, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Carrier Charge</span>
                        <span class="font-weight-bold">₹${Number(545).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Car Model Charge</span>
                        <span class="font-weight-bold">₹${Number(958).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Pet Charge</span>
                        <span class="font-weight-bold">₹${Number(600).toLocaleString('en-IN', { minimumFractionDigits: 2 })}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold h6 mb-0">
                        <span class="font-weight-bold">Total</span>
                        <span class="font-weight-bold">₹<?= number_format($grandTotal, 2) ?></span>
                    </div>
                </div>
            `;
            } else {
                $('.finalPrice').text(`₹<?= number_format($discountPriceWithGST, 2) ?>`)
                $('.offerAmount strong').text('₹<?= number_format($selectedCabResult['price'] - $discountPrice, 2) ?>')
                breakdownHtml = `
                <div class="fare-box p-2 rounded bg-custom_gray">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Base Fare</span>
                        <span class="font-weight-bold">₹<?= number_format($basePrice, 2) ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Driver Charges</span>
                        <span class="font-weight-bold">₹570.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Taxes & Fees</span>
                        <span class="font-weight-bold">₹814.00</span>
                    </div>
                    <hr>
                        <div class="d-flex justify-content-between fw-bold h6 mb-0">
                        <span class="font-weight-bold">Total</span>
                        <span class="font-weight-bold">₹<?= number_format($selectedCabResult['price'] , 2) ?></span>
                    </div>
                </div>
            `;
            }
            $("#fareBreakup").html(breakdownHtml);
        }
        mainFunction();
        let initialOption = getPaymentOptionValue();
        toggleAddons(initialOption);
    });
</script>