class ShopCtProdGrid {

    constructor(wrapper){
        this.wrapper = wrapper;
        this.grid = wrapper.querySelector('.shop-ct-product-grid');
        this.fetching = false;

        if(this.wrapper.hasAttribute('data-category')){
            this.categoryId = this.wrapper.getAttribute('data-category');
        } else {
            this.categoryId = null;
        }

        this.paged = 1;
        this.totalPages = 1;
        if(this.grid.classList.contains('shop-ct-prod-grid-infinte-scroll')) {
            this.initInfiniteScroll();
        }

        this.sortingSelector = this.wrapper.querySelector('.shop_ct_sorting ');
        this.ratingFilters = [...this.wrapper.querySelectorAll('.shop-ct-filter-rating-link')];
        this.priceFilterButton = this.wrapper.querySelector('.shop-ct-filter-price-button');

        if(this.sortingSelector) {
            this.sortingSelector.addEventListener('change', this.applyFilters.bind(this));
        }

        if(this.ratingFilters.length) {
            this.ratingFilters.forEach(ratingFilter => {
                ratingFilter.addEventListener('click', (e) => {
                    e.preventDefault();
                    if(!e.target.classList.contains('shop-ct-filter-rating-active')) {
                        this.wrapper.querySelector('.shop-ct-filter-rating-active').classList.remove('shop-ct-filter-rating-active');
                        e.target.classList.add('shop-ct-filter-rating-active');
                        this.applyFilters();
                    }
                });
            })
        }

        if(this.priceFilterButton) {
            this.priceFilterButton.addEventListener('click', this.applyFilters.bind(this));
        }


        window.addEventListener('popstate', (event) => {
            if (history.state && history.state.action === 'shop_ct_load_products_html') {
                this.applyFilters(null, history.state);
            }
        }, false);
    }

    initInfiniteScroll() {

        this.paged = this.grid.getAttribute('data-paged');
        this.totalPages = this.grid.getAttribute('data-total');

        window.addEventListener('scroll', () => {
            //if (document.documentElement.scrollTop + window.innerHeight >= this.grid.offsetTop + this.grid.scrollHeight) {
            if(this.grid.scrollTop >= (this.grid.scrollHeight - this.grid.offsetHeight)) {
                if(!this.fetching) {
                    this.loadMore();
                }
            }
        });
    }

    loadMore() {
        if(this.paged >= this.totalPages) {
            return false;
        }

        this.fetching = true;
        this.paged++;
        this.grid.setAttribute('data-paged', this.paged);

        let data = this.getSortingAndFiltering();

        data['action'] = 'shop_ct_load_products_html';

        let browserUrl = new URL(window.location.href);
        Object.keys(data).forEach(key => {
            if(key !== 'prod_paged') {
                browserUrl.searchParams.set(key, data[key])
            }
        });


        let url = new URL(shopCTL10n.ajax_url);
        Object.keys(data).forEach(key => url.searchParams.set(key, data[key]));

        console.log('loading');

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(
                result => {
                    this.fetching = false;
                    if(result.success) {
                        this.grid.insertAdjacentHTML('beforeend', result.result);
                        window.history.pushState(data, document.title, browserUrl);
                    }
                }
            )
            .catch(errors => {
                this.fetching = false;
            });
    }

    applyFilters(e = null, data = null) {
        if(e) {
            e.preventDefault();
        }


        if(this.fetching)
            return false;

        this.fetching = true;

        this.paged = 1;
        this.grid.setAttribute('data-paged', '1');

        if(!data) {
            data = this.getSortingAndFiltering();
        } else {
            data['prod_paged'] = 1;
            this.paged = 1;
            this.grid.setAttribute('data-paged', '1');
            this.updateSortingAndFilteringElements(data);
        }


        let browserUrl = new URL(window.location.href);
        Object.keys(data).forEach(key => {
            if(key !== 'prod_paged') {
                browserUrl.searchParams.set(key, data[key])
            }
        });

        data['action'] = 'shop_ct_load_products_html';

        let url = new URL(shopCTL10n.ajax_url);
        Object.keys(data).forEach(key => url.searchParams.set(key, data[key]));


        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
            },
        })
            .then(response => response.json())
            .then(
                result => {
                    this.fetching = false;
                    if(result.success) {
                        this.grid.innerHTML = result.result;
                        window.history.pushState(data, document.title, browserUrl);
                        this.paged = result.paged;
                        this.totalPages = result.totalPages;
                        this.grid.setAttribute('data-paged', this.paged);
                        this.grid.setAttribute('data-total', this.totalPages);
                        // this.grid.insertAdjacentElement('beforeend', result);
                    }
                }
            )
            .catch(errors => {
                this.fetching = false;
            });

    }


    getSortingAndFiltering() {
        let data = {};

        data['prod_paged'] = this.paged;

        if(this.categoryId) {
            data['prod_category'] = this.categoryId;
        }

        if(this.sortingSelector){
            let sortingValue = this.sortingSelector.value.split(',');

            data['products_orderby'] = sortingValue[0];
            data['products_ordering'] = sortingValue[1];
        }

        if(this.ratingFilters.length){
            data['prod_min_rating']=this.wrapper.querySelector('.shop-ct-filter-rating-active').getAttribute('data-rating');
        }

        if(this.priceFilterButton) {
            data['prod_min_price'] = this.wrapper.querySelector('input[name="prod_min_price"]').value;

            data['prod_max_price'] = this.wrapper.querySelector('input[name="prod_max_price"]').value;
        }

        return data;
    }

    updateSortingAndFilteringElements(data) {
        this.sortingSelector.value = data['products_orderby']+','+data['products_ordering'];

        this.wrapper.querySelector('.shop-ct-filter-rating-active').classList.remove('shop-ct-filter-rating-active');
        this.wrapper.querySelector('.shop-ct-filter-rating-link[data-rating="'+data['prod_min_rating']+'"]').classList.add('shop-ct-filter-rating-active');

        this.wrapper.querySelector('input[name="prod_min_price"]').value  =  data['prod_min_price'];

        this.wrapper.querySelector('input[name="prod_max_price"]').value = data['prod_max_price'] ;
    }
}


jQuery(document).ready(function () {

    let productGrids = [...document.querySelectorAll('.shop-ct-product-grid-wrap')];
    let productGridInstances = [];

    if(productGrids.length){
        productGrids.forEach(gridElement => {
            productGridInstances.push(new ShopCtProdGrid(gridElement));
        });
    }


    /*let sorting = [...document.querySelectorAll('.shop_ct_sorting')];


    if(sorting.length) {
        sorting.addEventListener('change', function(e){
            e.preventDefault();
            window.location = this.options[this.selectedIndex].getAttribute('data-location');
        });
    }


    var grid = document.querySelector('.shop-ct-prod-grid-infinte-scroll');
    var loadingGridItems = false;

    window.addEventListener('scroll', function () {
        if (document.documentElement.scrollTop + window.innerHeight >= grid.offsetTop + grid.scrollHeight) {
            if(!loadingGridItems) {
                shopCTInfiniteLoad(grid);
            }
        }
    });*/


    function shopCTInfiniteLoad(grid) {
        let paged = grid.getAttribute('data-paged');
        let totalPages = grid.getAttribute('data-total');

        
    }


    var _body = jQuery('body');

    _body.on('click', '.shop-ct-open-cart', function () {
        var data = {action: 'shop_ct_show_cart'};
        if (typeof window.shopCTCartCookie !== 'undefined') {
            data.shop_ct_current_hash = window.shopCTCartCookie;
        }

        shop_ct_popup.show('shop_ct_cart', {
            row_content: '<p class="shop-ct-loading">Loading</p>',
            css: {
                height: 'auto',
                width: 'auto',
                maxWidth: 500,
                maxHeight: 500,
                textAlign: 'center',
            }
        });

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            data: data,
            type: 'get',
        }).done(function (data) {
            jQuery('#shop_ct_popup_window-shop_ct_cart .shop-ct-loading').replaceWith(data);
        });
        return false;
    });

    _body.on('click', '.shop-ct-cart-close', function () {
        shop_ct_popup.remove('shop_ct_cart');
        return false;
    });

    _body.on('click', '.shop-ct-add-to-cart', function () {
        var productId = jQuery(this).data('product-id'),
            quantity = jQuery(this).closest('form').find('input[name=shop-ct-add-to-cart-count]').val(),
            btn = jQuery(this),
            data = {
                action: 'shop_ct_add_to_cart',
                product_id: productId,
                quantity: quantity,
                nonce: shopCTL10n.shop_ct_nonce
            };

        if (typeof window.shopCTCartCookie !== 'undefined') {
            data.shop_ct_current_hash = window.shopCTCartCookie;
        }

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            data: data,
            dataType: 'json',
            type: 'post',
            beforeSend: function () {
                btn.find('i').css('display', 'none');
                btn.find('.shop-ct-spinner').css('display', 'block');
                btn.attr('disabled', 'disabled');
            }
        }).always(function () {
            btn.removeAttr('disabled');
            btn.find('i').css('display', 'block');
            btn.find('.shop-ct-spinner').css('display', 'none');
        }).done(function (data) {
            if (data.hasOwnProperty('count')) {
                if (data.count) {
                    if(jQuery('.shop-ct-cart-count').length){
                        jQuery('.shop-ct-cart-count').html(data.count);
                    }

                    shop_ct_popup.showToastr(shopCTL10n.cartUpdated);
                }
            } else if (data.hasOwnProperty('errorMessage')) {
                shop_ct_popup.showToastr(data.errorMessage, 'error');
            }
        });

        return false;
    });

    _body.on('click', '.shop-ct-cart-delete-product', function () {
        var row = jQuery(this).closest('tr'),
            productId = row.data('product-id'),
            data = {
                action: 'shop_ct_remove_from_cart',
                product_id: productId,
                nonce: shopCTL10n.shop_ct_nonce
            };

        if (typeof window.shopCTCartCookie !== 'undefined') {
            data.shop_ct_current_hash = window.shopCTCartCookie;
        }

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            data: data,
            dataType: 'json',
            type: 'post',
        }).done(function (data) {
            row.remove();
            if (data.total) {
                if (jQuery('.shop-ct-cart-count').length) {
                    jQuery('.shop-ct-cart-count').html(data.count);
                }
                if (jQuery('.shop-ct-cart-total-value').length) {
                    jQuery('.shop-ct-cart-total-value').html(data.total);
                }
                if (jQuery('.shop-ct-cart-count-n').length) {
                    jQuery('.shop-ct-cart-count-n').html(data.count_n);
                }
                if (data.count === 0) {
                    jQuery('.shop-ct-cart-items').replaceWith('<h2>' + shopCTL10n.emptyCart + '</h2>');
                    jQuery('.shop-ct-cart-totals').remove();
                    jQuery('.shop-ct-cart-checkout.shop-ct-button').addClass('shop-ct-disabled-link');
                }

                shop_ct_popup.showToastr(shopCTL10n.cartUpdated);
            }
        });
    });

    _body.on('change', '.shop-ct-cart-product-qty', function () {
        var row = jQuery(this).closest('tr'),
            productId = row.data('product-id'),
            qty = jQuery(this).val(),
            data = {
                action: 'shop_ct_change_cart_qty',
                product_id: productId,
                quantity: qty,
                nonce: shopCTL10n.shop_ct_nonce
            };

        if (typeof window.shopCTCartCookie !== 'undefined') {
            data.shop_ct_current_hash = window.shopCTCartCookie;
        }

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            data: data,
            dataType: 'json',
            type: 'post',
        }).done(function (data) {
            if (data.total) {
                if (jQuery('.shop-ct-cart-count').length) {
                    jQuery('.shop-ct-cart-count').html(data.count);
                }
                if (jQuery('.shop-ct-cart-total-value').length) {
                    jQuery('.shop-ct-cart-total-value').html(data.total);
                }
                if (jQuery('.shop-ct-cart-count-n').length) {
                    jQuery('.shop-ct-cart-count-n').html(data.count_n);
                }
                if (data.count === 0) {
                    jQuery('.shop-ct-cart-items').replaceWith('<h2>' + shopCTL10n.emptyCart + '</h2>');
                    jQuery('.shop-ct-cart-totals').remove();
                    jQuery('.shop-ct-cart-checkout.shop-ct-button').addClass('shop-ct-disabled-link');
                }
                shop_ct_popup.showToastr(shopCTL10n.cartUpdated);
            }
        });
        return false;
    });
});
