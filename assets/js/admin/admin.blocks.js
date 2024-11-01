(function (blocks, i18n, element, components) {
    var SelectControl = components.SelectControl;
    console.log(SelectControl);
    var el = element.createElement; // The wp.element.createElement() function to create elements.

    blocks.registerBlockType('shop-ct/product', {
        title: 'Product',
        icon: 'products',
        category: 'shop-ct',
        attributes: {
            product_id: {type: 'string'}
        },
        edit: function (props) {
            var focus = props.focus;
            props.attributes.product_id =  props.attributes.product_id &&  props.attributes.product_id != '0' ?  props.attributes.product_id : false;
            return [
                !focus && el(
                    SelectControl,
                    {
                        label: 'Select Product',
                        value: props.attributes.product_id ? parseInt(props.attributes.product_id) : 0,
                        instanceId: 'shop-ct-product-selector',
                        onChange: function (value) {
                            props.setAttributes({product_id: value});
                        },
                        options: shopCTBlockI10n.products,
                    }
                ),
                el('div',{}, props.attributes.product_id ? 'Product: ' + shopCTBlockI10n.productMetas[props.attributes.product_id].title : 'Select Product')
            ];
        },
        save: function (props) {
            return el('p', {}, '[ShopConstruct_product id="'+props.attributes.product_id+'"]');
        },
    });

    blocks.registerBlockType('shop-ct/category', {
        title: 'Category',
        icon: 'category',
        category: 'shop-ct',
        attributes: {
            category_id: {type: 'string'}
        },
        edit: function (props) {
            var focus = props.focus;
            props.attributes.category_id =  props.attributes.category_id &&  props.attributes.category_id != '0' ?  props.attributes.category_id : false;
            return [
                !focus && el(
                    SelectControl,
                    {
                        label: 'Select Category',
                        value: props.attributes.category_id ? parseInt(props.attributes.category_id) : 0,
                        instanceId: 'shop-ct-category-selector',
                        onChange: function (value) {
                            props.setAttributes({category_id: value});
                        },
                        options: shopCTBlockI10n.categories,
                    }
                ),
                el('div',{}, props.attributes.category_id ? 'Category: ' + shopCTBlockI10n.categoryMetas[props.attributes.category_id].title : 'Select Category')
            ];
        },
        save: function (props) {
            return el('p', {}, '[ShopConstruct_category id="'+props.attributes.category_id+'"]');
        },
    });

    blocks.registerBlockType('shop-ct/catalog', {
        title: 'Catalog',
        icon: 'screenoptions',
        category: 'shop-ct',

        edit: function () {
            return el('div',{}, 'ShopConstruct Catalog') ;
        },
        save: function () {
            return el('p', {}, '[ShopConstruct_catalog]');
        },
    });

    blocks.registerBlockType('shop-ct/cart-button', {
        title: 'Cart Button',
        icon: 'cart',
        category: 'shop-ct',

        edit: function () {
            return el('div',{}, 'ShopConstruct Cart Button') ;
        },
        save: function () {
            return el('p', {}, '[ShopConstruct_cart_button]');
        },
    });
})(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.element,
    window.wp.components
);