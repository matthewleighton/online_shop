$(document).ready(function() {
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
	$(".div-select").click(function() {
		var addressId = $(this).find('p').text();
		$(this).parents("form:first").find("input:first").val(addressId);
		$(this).parents("form:first").submit();
	});
});