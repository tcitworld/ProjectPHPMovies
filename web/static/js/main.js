$('.editButton').click(function() {
	id = $(this).attr('id');
	titrefr = $('.row-' + id + ' td').eq(0).text();
	titrevo = $('.row-' + id + ' td').eq(1).text();
	couleur = $('.row-' + id + ' td').eq(2).text();
	pays = $('.row-' + id + ' td').eq(3).text();
	date = $('.row-' + id + ' td').eq(4).text();
	duree = $('.row-' + id + ' td').eq(5).text();
	$.ajax({
		url: "edit/"+id;
		params = [titrefr, titrevo, couleur, pays, date, duree];
	})
});