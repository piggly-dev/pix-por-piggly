jQuery(document).ready(function(){

	jQuery('.wpgly-pix-button').on('click', function(e) {
		e.preventDefault();

		current = jQuery(e.target);

		var dados_envio = {
			'security': wpgly_pix_payload.security,
			'action': 'wpgly_pix_update_order',
			'oid': current.data('oid'),
			'aid': current.data('aid')
		}

		console.log(dados_envio);
		console.log(wpgly_pix_payload);

		jQuery.ajax({
			url: wpgly_pix_payload.xhr_url,
			type: 'POST',
			data: dados_envio,
			dataType: 'JSON',
			success: function(response) {
				if ( response.success === false )
				{ alert('Erro: '+response.data); }
				else
				{ location.reload(); }
			}
		});
	});

});
 