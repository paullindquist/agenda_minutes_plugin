(function( $ ) {
	'use strict';

	var file_frame;

	$.fn.uploadMediaFile = function( button, preview_media ) {
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			var file_name_input;
			var file_id_input;
			var attachment = file_frame.state().get('selection').first().toJSON();

			file_name_input = button.parent().next().find('input[type="text"]');
			file_name_input.show();
			file_name_input.prop('disabled', true);
			file_name_input.val(attachment.filename);

			file_id_input = button.parent().find('input[type="hidden"]');
			file_id_input.val(attachment.id);

			file_frame = false;
		});

		// Finally, open the modal
		file_frame.open();
	}

	$(document).on('ready', function() {

		$('[name="new_agenda_date"]')[0].valueAsDate = new Date();

		$('#new_agenda').click(function() {
			var agenda;
			var minutes;
			var agenda_date;

			agenda_date = $('#new_agenda_date').val();
			agenda = $('#new_agenda_file_id').val();
			minutes = $('#new_minutes_file_id').val();

			$.post( {
				url: agendas.ajaxurl,
				data: {
					action: 'add_agenda',
					agenda: agenda,
					agenda_date: agenda_date,
					minutes: minutes,
				},
				success: function(data) {
					window.location.reload();
				}
			});
		});


		$('.agenda_upload_button').click(function() {
			$.fn.uploadMediaFile( $(this), true );
		});

		$('.delete_agenda').click(function() {
			var agenda_id = $(this).parent().find('input[name="agenda_id"]').val();

			$(this).parents('tr').hide();
			$.post( {
				url: agendas.ajaxurl,
				data: {
					action: 'delete_agenda',
					agenda_id: agenda_id
				},
				success: function(data) {
					console.log(data);
				}
			});
		});
	});
})( jQuery );
