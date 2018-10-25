$(document).ready(function(){

	$("#ebay-description").removeClass("nojs");

	$(".codisto-video").each(function() {

		try
		{
			var $Self = $(this);

			var youtubeEmbed = $("<ifra" + "me src=\"" + $Self.data("video-url") + "\" width=\"" + $Self.data("video-width") + "\" height=\"" + $Self.data("video-height") + "\" frameborder=\"0\" allowfullscreen></ifra" + "me>");

			this.parentNode.insertBefore(youtubeEmbed[0], this);
			this.parentNode.removeChild(this);
		}
		catch(e)
		{

		}

	});

	(function() {

		var postcodeTimer;
		var postcodeXhr;
		var postcodeVal;
		var postcodeSpinner;

		$(document).on("keyup", ".freightinput", function(e) {

			var $self = $(this);

			if(postcodeVal != $self.val())
			{
				if(postcodeTimer)
				{
					clearTimeout(postcodeTimer);
					postcodeTimer = null;
				}

				postcodeTimer = setTimeout(function() {

					postcodeVal = $self.val();

					if(postcodeVal.length > 3) {

						$(".freightprice").text("...");

						if(postcodeXhr)
						{
							postcodeXhr.abort();
							postcodeXhr = null;
						}

						if(postcodeSpinner)
						{
							clearInterval(postcodeSpinner);
						}

						postcodeSpinner = setInterval(function() {

							var currentVal = $(".freightprice").text();
							if(currentVal == ".")
								currentVal = "..";
							else if(currentVal == "..")
								currentVal = "...";
							else
								currentVal = ".";

							$(".freightprice").text(currentVal);

						}, 500);

						$(".price").show();
						$(".freightmessage").hide();

						postcodeXhr = $.ajax({
							type : "GET",
							url :  window.CodistoApp.MerchantURL + "/cart/quote/",
							dataType : "jsonp",
							data : "sku=" + encodeURIComponent(window.CodistoApp.ProductCode) + "&postalcode=" + encodeURIComponent(postcodeVal) + "&qty=" + ((window.CodistoApp.BundleQuantity)?window.CodistoApp.BundleQuantity:1) + "&jsonp",
							success : function(data) {

								if(postcodeSpinner)
								{
									clearInterval(postcodeSpinner);
									postcodeSpinner = null;
								}

								if(data.length > 0)
								{
									var FirstPrice = data[0].FreightCost;
									var FirstServiceName = data[0].ProductName;
									$(".freightprice").text(window.CodistoApp.CurrencyFormat.replace(/0\.\d+/, FirstPrice.toFixed(2)));
									$(".servicename").show().text(FirstServiceName);

									if(data.length > 1) {

										$(".seemore").show();
										$(".moreservices > div").html("");
										for (i = 1; i < data.length; i++) {

											var FreightPrice = data[i].FreightCost;
											var FreightInfo = data[i].ProductName + " - <strong>" + window.CodistoApp.CurrencyFormat.replace(/0\.\d+/, FreightPrice.toFixed(2)) + "</strong>";

											$(".moreservices > div").append("<p>" + FreightInfo.replace(/</g, "&lt;") + "</p>");
										}

									} else {

										$(".seemore").hide();
										$(".moreservices").hide();

									}
								}
								else
								{

									$(".price").hide();
									$(".freightmessage").show().text("No Quotes Available");
									$(".servicename").hide();

								}

							},
							complete : function()
							{
								if(postcodeSpinner)
								{
									clearInterval(postcodeSpinner);
									postcodeSpinner = null;
								}

								postcodeXhr = null;
							}
						});

					}

				}, 250);
			}

		});

	})();

	$(document).on("click", ".calculate", function(e) {
		e.preventDefault();
		e.stopPropagation();
	});

	$(document).on("click", ".seemore", function(e) {
		e.preventDefault();
		$(this).popover({
			html : true,
			placement : "right",
			title : "More Services",
			content : $(".moreservices").html()
		}).popover("show");
		$(document).one("click", function(e) {
			$(".seemore").popover("destroy");
			e.stopPropagation();
		});
	});

	$(document).on("click", ".seemoreflat", function(e) {
		e.preventDefault();
		$(this).popover({
			html : true,
			placement : "right",
			title : "More Services",
			content : $(".moreservicesflat").html()
		}).popover("show");
		$(document).one("click", function(e) {
			$(".seemoreflat").popover("destroy");
			e.stopPropagation();
		});
	});
});
