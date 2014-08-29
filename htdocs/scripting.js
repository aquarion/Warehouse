
$("document").ready(function(){

	$('nav a').click(function(){
		if ($(this).hasClass("selected")){
			return;
		}
		var showme = $("#"+$(this).attr("rel"));
		$(".displayitem").slideUp("slow");
		showme.slideDown("slow");
		$('nav a').removeClass("selected");
		$(this).addClass("selected");

	});

	$('.vote').click(function(){
		if ($(this).hasClass("voteup")){
			vote = "up";
		} else {
			vote = "down";
		}

		uid = $(this).attr("rel");

		$.ajax({
			url: "vote.php",
			data: {uid: uid, vote: vote},
			type: 'POST'
		}).done(function(data) { 
			$("#voted").html(data);
		});

	});

	if ($('#item_added')){
		$('#item_added').fadeIn();
		setTimeout("hide_added()", 3000);
	}

	if($('#error').length){
		$(".displayitem").hide();
		$("#add").slideDown();
	}
});

function hide_added(){
	$('#item_added').fadeOut('slow');
}

