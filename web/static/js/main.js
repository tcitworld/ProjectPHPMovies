$('.editButton').click(function() {
	id = $(this).attr('data-id');
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
		alert('saved');
	});
});

$('.deleteButton').click(function() {
	id = $(this).attr('data-id');
	$.ajax({
		method: "GET",
		url: "delete/"+id,
	}).done(function() {
		alert('deleted');
	})
});