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
		}).success(function() {
			buttontd = $('.row-' + id + ' td').eq(7);
			buttontd.text('✔');
			buttontd.css('color','green');
			$('.row-' + id + ' td:not(:has(button))').attr('contenteditable','false');
			$('.row-' + id).css('background','#fff');
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
	}).success(function() {
		$('.row-' + id).slideUp();
	})
});
$('#tbl').DataTable();

$('.createM').click(function(){
	form = $('.form');
	form.toggleClass('hidden-ct');
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
	}).success(function() {
		form.hide();
		message.show().delay(2000).slideUp('slow');

	});
});

$(document).on("click",".detailsButton", function() {

	/*
	 * Reset text ids
	 */
	$("#titre").text('');
	$("#year").text('');
	$("#rated").text('');
	$("#released").text('');
	$("#plot").text('');
	$("#poster").attr('src','');

	$('html, body').animate({
        scrollTop: $("#details").offset().top
    }, 1000);


	id = $(this).attr('data-id');
	console.log(id);
	$.ajax({
		method: "GET",
		datatype: "json",
		url: "details/"+id,
	}).success(function(data) {
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

		// OMDB API
		$.ajax({
			method: "GET",
			url: "http://www.omdbapi.com/",
			data: { t: data["film"]["titre_original"], plot: "full", r: "json" }
		}).success(function(dataAPI) {
			if (dataAPI['Response']) {
				console.log(dataAPI);
				$("#titre").text(dataAPI['Title']);
				$("#year").text(dataAPI['Year']);
				$("#rated").text(dataAPI['Rated']).show();
				$("#released").text(dataAPI['Released']).show();
				$("#plot").text(dataAPI['Plot']).show();
				$("#poster").attr('src',dataAPI['Poster']);
			} else {
				$("#titre").text(data["film"]["titre_original"]);
				$("#year").text(data["film"]["date"]);
			}
		});
	});
});