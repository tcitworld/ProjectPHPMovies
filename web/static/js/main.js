$('.editButton').click(function() {
	id = $(this).attr('data-id');
	if ($(this).hasClass('saveButton')) {
		titrefr = $('.row-' + id + ' td').eq(0).text();
		titrevo = $('.row-' + id + ' td').eq(1).text();
		couleur = $('.row-' + id + ' td').eq(2).text();
		pays = $('.row-' + id + ' td').eq(3).text();
		date = $('.row-' + id + ' td').eq(4).text();
		duree = $('.row-' + id + ' td').eq(5).text();
		$.ajax({
			method: "POST",
			url: "edit/"+id,
			data: {titrefr: titrefr,
					titrevo: titrevo,
					couleur: couleur,
					pays: pays,
					date: date,
					duree: duree
				}
		}).done(function() {
			buttontd = $('.row-' + id + ' td').eq(6);
			buttontd.text('✔');
			buttontd.css('color','green');
		});
	} else {
		$('.row-' + id + ' td:not(:has(button))').attr('contenteditable','true');
		$('.row-' + id).css('background','#ffeb8e');
		$(this).toggleClass('saveButton');
		$(this).toggleClass('editButton');
		$(this).text("Save");
	}
});

$('.deleteButton').click(function() {
	id = $(this).attr('data-id');
	$.ajax({
		method: "GET",
		url: "delete/"+id,
	}).done(function() {
		$('.row-' + id).slideUp();
	})
});
$('#tbl').DataTable();