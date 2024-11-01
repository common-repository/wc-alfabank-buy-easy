(function ($) {

    // TODO - check
    /**
     * Cart
     */
    // Change Alfa Bank quantity and price when quantity on cart page is changed
    function changeQuantityOnCart() {
        let cart_items = $('.woocommerce-cart-form .cart_item');

        cart_items.each(function (i) {
            $(this).find('.qty').on('input', function () {

                let qty = $(this),
                    accordQtyInput = 'itemQuantity_' + i,
                    accordPriceInput = 'itemPrice_' + i,
                    inputSum = $('input[name="sum"]'),
                    inputQty = $('input[name="' + accordQtyInput + '"]'),
                    inputPrice = $('input[name="' + accordPriceInput + '"]'),
                    oldSum = +inputQty.val() * +inputPrice.val(),
                    newSum = +qty.val() * +inputPrice.val(),
                    difference = +newSum - +oldSum,
                    totalSum = +inputSum.val() + +difference;

                inputQty.val(qty.val());
                inputSum.val(totalSum.toFixed(2));
            });
        });
    }

    changeQuantityOnCart();

    // Change Alfabank quantity and price on cart page when cart is updated
    $(document.body).on('updated_cart_totals', changeQuantityOnCart);


    /**
     * Coupon AJAX handler
     */
    // wc_cart_params is required to continue, ensure the object exists
    if (typeof wc_cart_params === 'undefined') {
        return false;
    }

    $('.coupon .button').click(function (e) {

        let data = {
            action: 'alfabank_buy_easy_when_coupon_apply',
            coupon_code: $('#coupon_code').val(),
            security: wc_cart_params.apply_coupon_nonce
        };

        $.post(woocommerce_params.ajax_url, data, function (response) {
            if (response) {
                var alfabank_products_list = $('#alfabank_products_list');

                if (response !== 0) {
                    alfabank_products_list.empty();
                    alfabank_products_list.html(response);
                }
            }
        });
    });
})(jQuery);