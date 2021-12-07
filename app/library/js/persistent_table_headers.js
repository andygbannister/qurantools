	function UpdateTableHeaders() {

	  $(".persist-area").each(function () {

	    var el = $(this),
	      offset = el.offset(),
	      scrollTop = $(window).scrollTop(),
	      floatingTableHeader = $(".floatingTableHeader", this)

	    if ((scrollTop > offset.top) && (scrollTop < offset.top + el.height())) {
	      floatingTableHeader.css({
	        "visibility": "visible"
	      });
	    } else {
	      floatingTableHeader.css({
	        "visibility": "hidden"
	      });
	    };
	  });
	}

	// DOM Ready      
	$(function () {

	  var clonedHeaderRow;

	  $(".persist-area").each(function () {
	    clonedHeaderRow = $(".persist-header", this);
	    clonedHeaderRow
	      .before(clonedHeaderRow.clone())
	      .css("width", clonedHeaderRow.width() + 1)
	      .css("z-index", 9)
	      .addClass("floatingTableHeader");

	  });

	  $(window)
	    .scroll(UpdateTableHeaders)
	    .trigger("scroll");

	});