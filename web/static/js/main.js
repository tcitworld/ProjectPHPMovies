$('html').on('click','.editButton', function() {
	id = $(this).attr('data-id');
	$('.row-' + id + ' td:not(:has(button), .details-control)').attr('contenteditable','true');
	$(this).toggleClass('btn-success');
	$(this).toggleClass('btn-warning');
	$('.row-' + id).css('background','#ffeb8e');
	$(this).toggleClass('editButton');
	$(this).toggleClass('saveButton');
	$(this).text("Save");
});

$('html').on('click','.saveButton', function() {
	id = $(this).attr('data-id');
	titrefr = $('.row-' + id + ' td').eq(1).text();
	titrevo = $('.row-' + id + ' td').eq(2).text();
	couleur = $('.row-' + id + ' td').eq(3).text();
	pays = $('.row-' + id + ' td').eq(4).text();
	date = $('.row-' + id + ' td').eq(5).text();
	duree = $('.row-' + id + ' td').eq(6).text();
	real = $('.row-' + id + ' td').eq(7).attr('data-id');
	$.ajax({
		method: "POST",
		url: "edit/"+id,
		data: {titrefr: titrefr,
				titrevo: titrevo,
				couleur: couleur,
				pays: pays,
				date: date,
				duree: duree,
				real: real
			}
	}).success(function() {
		buttontd = $('.row-' + id + ' td').eq(8);
		buttontd.children('button').toggleClass('btn-success');
		buttontd.children('button').toggleClass('btn-warning');
		buttontd.children('button').toggleClass('saveButton');
		buttontd.children('button').toggleClass('editButton');
		buttontd.children('button').text("Edit");
		$('.row-' + id + ' td:not(:has(button), .details-control)').attr('contenteditable','false');
		$('.row-' + id).css('background','#eee');
	});
});
dt = $('#tbl').DataTable();

$('html').on('click','.deleteButton', function() {
	id = $(this).attr('data-id');
	if(confirm("Supprimer ?")) {
		$.ajax({
			method: "GET",
			url: "delete/"+id,
		}).success(function() {
			dt.row($('.row-' + id)).remove().draw();
		});
	}
});

$('.createM').click(function(){
	$('#newFilm').toggle();
	$('html, body').animate({
        scrollTop: $("#newFilm").offset().top
    }, 1000);
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
	real = $('#real').val();
	$.ajax({
		method: "POST",
		url: "create",
		data: {titrefr: titrefr,
				titrevo: titrevo,
				couleur: couleur,
				pays: pays,
				date: date,
				duree: duree,
				real: real
			}
	}).success(function() {
		form.hide();
		message.show().delay(2000).slideUp('slow');
		dt.row.add(['',titrefr, titrevo, couleur, pays, date, duree, real,'','']).draw();

	});
});

function format ( id ) {
	//id = $(this).attr('data-id');
	var title = '';
	var year = '';
	var poster = '';
	var rated = '';
	var released = ''
	var genres = [];
	var acteurs = [];
	var res = '';
	$.ajax({
		method: "GET",
		datatype: "json",
		url: "details/"+id,
	}).done(function(data) {
		for (var i = data["genres"].length; i >= 0; i--) {
			if (typeof(data["genres"][i]) != "undefined") {
				genres.push(" " + data["genres"][i]['nom_genre']);
			}
		}
		for (var i = data["acteurs"].length; i >= 0; i--) {
			if (typeof(data["acteurs"][i]) != "undefined") {
				acteurs.push(" " + data["acteurs"][i]['prenom'] + " " + data["acteurs"][i]['nom']);
			}
		}
		$('#titre').text(data["film"]["titre_original"]);
		$('#year').text(data["film"]["date"]);
		$('.detailGenre').text(genres.toString());
		$('.detailActeurs').text(acteurs.toString());

		$.ajax({
			method: "GET",
			url: "http://www.omdbapi.com/",
			data: { t: data["film"]["titre_original"], plot: "full", r: "json" }
		}).success(function(dataAPI) {
			if (dataAPI['Response']) {
				$('#titre').text(dataAPI['Title']);
				$('#year').text(dataAPI['Year']);
				$('#poster').attr('src', dataAPI['Poster']);
				$('#rated').text(dataAPI['Rated']);
				$('#released').text(dataAPI['Released']);
				$('#plot').text(dataAPI['Plot']);
			}
		});
	});
	res = '<img id="poster" src="' + poster + '" alt=""><h2 id="titre">' + title + '</h2><em>Année : <span id="year">' + year + '</span></em> <em>Rated : <span id="rated">' + rated + '</span></em> <em>Released : <span id="released">' + released + '</span></em><p id="plot"></p>Genres :<ul class="detailGenre">'+ genres.toString() + '</ul>Acteurs :	<ul class="detailActeurs">' + acteurs.toString() + '</ul>';
	return res;

}

$(document).ready(function() {
 // Array to track the ids of the details displayed rows
    var detailRows = [];
 
    $('#tbl tbody').on( 'click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dt.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
 
            // Remove from the 'open' array
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            row.child( format(tr.attr('data-id')) ).show();
 
            // Add to the 'open' array
            if ( idx === -1 ) {
                detailRows.push( tr.attr('id') );
            }
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    dt.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td.details-control').trigger( 'click' );
        } );
    } );
} );