var main = function() {
	/* Push the form down */
	$('.insert-grade').click(function() {
		$('#scroll_form_add_assignment').animate({
			top: "250px"
		}, 400);
		$('#lockoutImg', window.parent.document).toggle();
		$('#catID').attr("value", this.getAttribute("catid"));
		$('#cat_label').html("Add assignment to "+this.getAttribute("catName"));
	});
	
	/* Push the form up */
	$('#add-hideBtn').click(function() {
		$('#scroll_form_add_assignment').animate({
			top: "-400px"
		}, 400);
		$('#lockoutImg').toggle();
	});
	
	/* Push the form up */
	$('#edit-hideBtn').click(function() {
		$('#scroll_form_edit_grade').animate({
			top: "-400px"
		}, 400);
		$('#lockoutImg').toggle();
	});
};

$(document).ready(main);