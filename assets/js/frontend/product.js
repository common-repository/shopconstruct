jQuery(document).ready(function () {




    var _body = jQuery('body'),
        zoomConfig = {
            cursor: 'crosshair',
            scrollZoom: true
        };

    var zoomImage = jQuery(".shop_ct_product_main_image img");

    /** ElevateZoom */
    if (jQuery('.shop_ct_product_images').data('has-zoom') === 'yes') {
        if (jQuery("#shop_ct_product_secondary_images img").length > 1) {
            zoomConfig.gallery = 'shop_ct_product_secondary_images';
            zoomConfig.galleryActiveClass = 'active';
        }



        zoomImage.elevateZoom(zoomConfig);
    }


    var product_sale_date_to = jQuery(".shop_ct_product_prices").data("sale_date_to");
    var product_sale_date_from = jQuery(".shop_ct_product_prices").data("sale_date_from");

    var date_from = product_sale_date_from.length ? new Date(product_sale_date_from) : new Date(2016, 1, 1);
    var date_to = new Date(product_sale_date_to);
    var date_now = shopCTDate(shopCTL10n.gmt_offset);


    if (product_sale_date_to.length || product_sale_date_from.length) {
        shopCTProductCountDown(date_from, date_to, date_now);
    }


    function shopCTProductCountDown(date_from, date_to, date_now) {
        var regular_price = jQuery("body").find(".shop_ct_product_prices").data("regular_price");
        if (date_now < date_to || date_now > date_from) {
            jQuery('.shop_ct_product_sale_date').countdown(date_to)
                .on('update.countdown', function (event) {
                    var format = '%H:%M:%S';
                    if (event.offset.days > 0) {
                        if (event.offset.hours > 0) {
                            format = '%-d day%!d %-H hours'
                        } else {
                            format = '%-d day%!d'
                        }
                    }
                    if (event.offset.weeks > 0) {
                        if (event.offset.days > 0) {
                            format = '%-w week%!w %-d day%!d';
                        } else {
                            format = '%-w week%!w';
                        }
                    }
                    if (event.offset.months > 0) {
                        if (event.offset.days > 0) {
                            format = '%-m month%!m %-n day%!n ';
                        } else {
                            format = '%-m month%!m'
                        }
                    }
                    jQuery(this).html(event.strftime(format));
                })
                .on('finish.countdown', function (event) {
                    jQuery(".shop_ct_product_prices").html("<span class=\"shop_ct_product_regular_price\">" + regular_price + "</span>")
                });
        }
    }


    _body.find(".shop_ct_product_secondary_image").on("click", function () {
        if (jQuery('.shop_ct_product_images').data('has-zoom') === 'yes') {
            jQuery('.zoomWrapper img.zoomed').unwrap();
            jQuery('.zoomContainer').remove();

            zoomImage.removeData('elevateZoom');

        }

        jQuery("body").find(".shop_ct_product_secondary_image").removeClass("active");
        jQuery(this).addClass("active");


            zoomImage.attr("src", jQuery(this).find("img").data('img'));
        if (jQuery('.shop_ct_product_images').data('has-zoom') === 'yes') {
            zoomImage.data("zoom-image", jQuery(this).find("img").data("zoom"));

            setTimeout(function () {

                zoomImage.elevateZoom(zoomConfig);

            }, 200)


        }
        return false;

    });


    _body.on("click", ".shop_ct_product_info_nav li", function () {

        _body.find(".shop_ct_product_info_nav li").removeClass("active");
        jQuery(this).addClass("active");

        var sectionToShow = jQuery(this).attr("rel");

        _body.find(".shop_ct_product_info section").removeClass("active");
        _body.find("#" + sectionToShow).addClass("active");

    });


    jQuery("#product_order_qty").on("blur", function () {
        var _this = jQuery(this);

        if (_this.val() == "" || _this.val() <= 0) {
            _this.val("1");
        }
        var max = jQuery("#product_stock_amount").html();

        if (parseInt(_this.val()) > parseInt(max)) {
            _this.val(max);
        }
    });


    jQuery('.comment-reply').on('click', function () {
        var id = jQuery(this).closest('article').attr('id').replace(/[^0-9]/g, ''),
            post_id = jQuery('.shop_ct_product_the_container').data('product_id');

        jQuery('.review_stars_wrapper').hide();
        return addComment.moveForm('div-comment-' + id, id, 'respond', post_id)
    });

    //todo: Review
    jQuery('#cancel-comment-reply-link').on('click', function () {
        jQuery('.review_stars_wrapper').show();
    });

    _body.on("click", "#shop_ct_product_reviews label.review_star", function () {

        checked_star_number = jQuery(this).attr("for").substr(12);
        rating_name_space = jQuery("#single_product_rating_word");

        if (checked_star_number == "1") {
            rating_name_space.html(shopCTL10n.star1);
        } else if (checked_star_number == "2") {
            rating_name_space.html(shopCTL10n.star2)
        } else if (checked_star_number == "3") {
            rating_name_space.html(shopCTL10n.star3)
        } else if (checked_star_number == "4") {
            rating_name_space.html(shopCTL10n.star4)
        } else if (checked_star_number == "5") {
            rating_name_space.html(shopCTL10n.star5)
        }

    });
});

function shopCTDate(offset, date) {
    var d;
    // create Date object for current location
    if (date) {
        d = new Date(date);
    } else {
        d = new Date();
    }
    /*offset = moment.tz(offset).format('Z');
    console.log(offset);
    if(isNaN(offset)){

    }*/

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);
    // create new Date object for different city
    // using supplied offset
    // return time as a string
    return new Date(utc + (3600000 * offset));
}
