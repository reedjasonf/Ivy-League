var main = function() {
	/* Push the form down */
	$('.edit-grade').click(function() {
		$('#scroll_form_edit_grade', window.parent.document).animate({
			top: "250px"
		}, 400);
		$('#lockoutImg', window.parent.document).toggle();
		var formAttrs = this.getAttribute("val");
		var attrsArray = formAttrs.split(";");
		$('#editFormDesc', window.parent.document).attr("value", attrsArray[2]);
		$('#editFormEarned', window.parent.document).attr("value", attrsArray[0]);
		$('#editFormMax', window.parent.document).attr("value", attrsArray[1]);
		$('#hiddenID', window.parent.document).attr("value", attrsArray[3]);
	});
	
	/* Push the form up */
	$('#hideBtn', window.parent.document).click(function() {
		$('#scroll_form_edit_grade', window.parent.document).animate({
			top: "-400px"
		}, 400);
		$('#lockoutImg', window.parent.document).toggle();
	});
};

$(document).ready(main);