var main = function() {
	/* Push the form down */
	$('.edit-grade').click(function() {
		$('#scroll_form_edit_grade', window.parent.document).animate({
			top: "250px"
		}, 400);
	});
};

$(document).ready(main);