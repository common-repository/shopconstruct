jQuery(document).ready(function () {
    var _body = jQuery("body");
console.log('a');
    _body.on('click','#shop_ct_bacs_accounts a.remove_rows',function(){
        jQuery('#shop_ct_bacs_accounts input[type="checkbox"]:checked:not(#cb-select-all)').closest('tr').remove();
        jQuery("#cb-select-all").prop('checked',false);
        return false;
    });
    _body.on( 'click', '#shop_ct_bacs_accounts a.add', function(){
        var size = jQuery('#shop_ct_bacs_accounts').find('tbody .account').size();

        jQuery('<tr class="account">\
									<td class="shop-ct-check-column"><input id="cb-select-'+size+'" type="checkbox" value="'+size+'"</td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][account_name]" /></td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][account_number]" /></td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][bank_name]" /></td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][sort_code]" /></td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][iban]" /></td>\
									<td><input type="text" name="shop_ct_bacs_accounts[' + size + '][bic]" /></td>\
								</tr>').appendTo('#shop_ct_bacs_accounts tbody');
        return false;
    });
});