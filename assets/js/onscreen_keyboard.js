$(document).ready(function() {
	$(".edit-on-click").each(function(){
		if($(this).html() == '')
		{
			$(this).addClass("editable-empty");
		}
		
		if($(this).hasClass("editable-empty")) {
			var text;
			if($(this).data("emptytext").replace(/\s/g,"") == "")
			{
				$(this).data("emptytext", COMMON_NONE);
			}
	
			$(this).html($(this).data("emptytext"));
		}
	});
});

$(".edit-on-click").off("click");
$(".edit-on-click").on("click", function() {
	var $text = $(this);
	var $input = $('<input type="text" class="form-control input-sm keyboard" size="4" />');
	$input.data($text.data());
	$text.hide().after($input);
	
	if(!$text.hasClass("editable-empty"))
	{
		$input.val($text.data("value"));
	}
	
	$input.keyboard({
		type: $input.data("keyboard-type") == 'numpad' ? 'numpad' : '' ,
		trigger: 'manual',
		placement: $input.data("placement") != '' ? $input.data("placement") : 'bottom',
		before: function() {
		}
	});
	
	var enterKeyPress = function(e)
	{
		if(e.type == 'keypress' && e.which == 13)
		{
			$input.trigger("focusout");
			return false;
		}
	}

	var clickToEditSave = function(e) 
	{
		if($input.val() != $input.data("value"))
		{
			submitting = true;
			$.ajax({
			  type: "POST",
			  data: {
				  name: $input.data("name"),
			    value: $input.val()
			  },
			  url: $input.data("url"),
			  success: function(html, textStatus) {
					submitting = false;
					$("#register_container").html(html);
			  },
			  error: function (XMLHttpRequest, textStatus, errorThrown) {
					show_feedback('error','There was an Error Saving.', COMMON_ERROR);
			  }
			});
		}
		else 
		{
			$text.show();
			$input.popover("destroy").remove();
		}
	}
	
	$input.show().select()
    .on("focusout", clickToEditSave)
	.on("keypress", enterKeyPress);
	setTimeout(function() {
		$input.get(0).focus();
	    $input.get(0).select();
	}, 10);
	
	$input.popover("show");
});
	
$('#item').keyboard({
	trigger: 'manual',
	placement:'top',
	before: function() {
		$("html, body").animate({scrollTop: 0}, 'fast');
	}
});

if (SCREEN_WIDTH < 991)
{
	$('#comment').keyboard({
		placement:'top',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: $(this).offset().top - 300 }, 'fast');
		}
	});

	$('#customer').keyboard({
		placement:'top',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: $(this).offset().top - 300 }, 'fast');
		}
	});
	
	$('#supplier').keyboard({
		placement:'top',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: $(this).offset().top - 300 }, 'fast');
		}
	});
	
	$('#location').keyboard({
		placement:'top',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: $(this).offset().top - 300 }, 'fast');
		}
	});
	
	
	
} else {
	$('#comment').keyboard({
		placement:'left',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: $(this).offset().top - 500 }, 'fast');
		}
	});
	
	$('#customer').keyboard({
		placement:'left',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: 0 }, 'fast');
		}
	});

	$('#supplier').keyboard({
		placement:'left',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: 0 }, 'fast');
		}
	});
	
	$('#location').keyboard({
		placement:'left',
		trigger: 'manual',
		before: function() {
			$("html, body").animate({scrollTop: 0 }, 'fast');
		}
	});
	
	
	var done = false;
	//on tokenfield:initialize
	$( document ).on("tokenfield:initialize", "#coupons", function() {
		
		
		
		if(!done)
		{
			$("#coupons-tokenfield").data($("#coupons").data());
			
			$('#coupons-tokenfield').keyboard({
				title: "test",
				placement:'left',
				trigger: 'manual',
				before: function() {
					$("html, body").animate({scrollTop: 0 }, 'fast');
				}
			});
		}
		
		done = true;
		
	});
	
}

$('#amount_tendered').keyboard({
	type:'numpad',
	trigger: 'manual',
	before: function() {
		$("html, body").animate({scrollTop: $(this).offset().top - 300}, 'fast');
	}
});