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

$('.createM').click(function(){
	form = $('.form');
	$('#details').hide();
	form.toggle();
});

$('.createButton').click(function() {
	form = $('.form');
	message = $('.saved');
	titrefr = $('input[name=titrefr]').val();
	titrevo = $('input[name=titrevo]').val();
	couleur = $('input[name=couleur]').val();
	pays = $('input[name=pays]').val();
	date = $('input[name=date]').val();
	duree = $('input[name=duree]').val();
	$.ajax({
		method: "POST",
		url: "create",
		data: {titrefr: titrefr,
				titrevo: titrevo,
				couleur: couleur,
				pays: pays,
				date: date,
				duree: duree
			}
	}).done(function() {
		form.hide();
		message.show().delay(2000).slideUp('slow');

	});
});

$('.detailsButton').click(function() {
	id = $(this).attr('data-id');
	$.ajax({
		method: "GET",
		url: "details/"+id,
	}).done(function(data) {
		$('#details').show();
		$(".detailGenre").html('');
		for (var i = data["genres"].length; i >= 0; i--) {
			if (typeof(data["genres"][i]) != "undefined") {
				$(".detailGenre").append('<li>' + data["genres"][i]['nom_genre'] + '</li>')
			}
		}
		$(".detailActeurs").html('');
		for (var i = data["acteurs"].length; i >= 0; i--) {
			if (typeof(data["acteurs"][i]) != "undefined") {
				$(".detailActeurs").append('<li>' + data["acteurs"][i]['nom'] + " " + data["acteurs"][i]['prenom'] + '</li>')
			}
		}
	});
});