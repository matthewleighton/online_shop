$(document).ready(function() {
	
// -- Product search -- //
	

// -- Product show page -- //
	
	$("#product-description-more").click(function() {
		$("#product-description-more").css("display", "none");
		$("#product-description-less").css("display", "inline");
		$("#product-description-text").css("height", "100%");
	});

	$("#product-description-less").click(function() {
		$("#product-description-more").css("display", "inline");
		$("#product-description-less").css("display", "none");
		$("#product-description-text").css("height", "150px");
	});

// -- Checkout -- //

	$("#card-creation-existing-address-btn").click(function() {
		$("#card-creation-existing-address-form").css("display", "block");
		$("#card-creation-new-address-form").css("display", "none");
		$("#card-creation-existing-address-btn").css("background-color", "yellow");
		$("#card-creation-new-address-btn").css("background-color", "white");
	});

	$("#card-creation-new-address-btn").click(function() {
		$("#card-creation-new-address-form").css("display", "block");
		$("#card-creation-existing-address-form").css("display", "none");
		$("#card-creation-new-address-btn").css("background-color", "yellow");
		$("#card-creation-existing-address-btn").css("background-color", "white");
	});

	// Confirms whether or not a new address is being submitted alongside a new payment method.
	$("#new-payment-method").submit(function() {
		var $newAddress = $(this).find("input[name=include_new_address]");
		if ($("#card-creation-new-address-form").css("display") == "block") {
			$newAddress.val("1");
		} else {
			$newAddress.val("0");
		}
	});

	// Submits the form to select the clicked address/payment method
	$(".js-select").click(function() {
		var value = $(this).find('p:first').text();
		$(this).parents("form:first").find("input:first").val(value);
		$(this).parents("form:first").submit();
	});

// -- New Product Entry -- //

	// Product type buttons display different forms
	var productType;
	$('.pc-general .div-btn').click(function(){
		if (typeof productType !== 'undefined') {
			$('#' + productType + '-form').css('display', 'none');
			$('#' + productType + '-btn').css('background-color', '#FFFFFF');
		};

		productType = $(this).find('p:first').text();
		$('#' + productType + '-form').css('display', 'block');
		$('#' + productType + '-btn').css('background-color', '#DADADA');
		$('#product-submit-btn').css('display', 'block');
		$('#submitted-product-type').val(productType);
	});
	
	// Release date field becomes date type when selected.
	// And turns back to text with "Release date" placeholder if value is null when unfocused.
	// Not supported on IE
	$('#product-date-input').focus(function() {
		var ua = window.navigator.userAgent;
		var msie = ua.indexOf('MSIE ');

		if (!msie > 0 || !navigator.userAgent.match(/Trident.*rv\:11\./)) {
			$(this).removeAttr('type');
			$(this).prop('type', 'date');
			$(this).select();

			$(this).focusout(function() {
				if (this.value == '') {
					$(this).removeAttr('type');
					$(this).prop('type', 'text');	
				}
			});	
		}
	});

	// Add £ sign to price input on focus
	/*
	$('#product-price-input').focus(function() {
		if (this.value == '') {
			$(this).val('£');
			$(this).focusout(function() {
				if (this.value == '£') {
					$(this).val('');
				}
			});	
		}
	});
	*/

});