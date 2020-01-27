$("#selectTime").change(function() {
			if ($(this).val() == "hoursOrDays") {
				$('#hoursOrDays').show();
			} else {
				$('#hoursOrDays').hide();				
			}
		});
		$("selectTime").trigger("change");
		