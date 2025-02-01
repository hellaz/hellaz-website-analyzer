// Admin scripts
jQuery(document).ready(function($) {
    // Real-time URL validation
    $('#hellaz_test_url').on('input', function() {
        const url = $(this).val();
        const validation = $('#url_validation');
        
        try {
            new URL(url);
            validation.text(wp.i18n.__('Valid URL format', 'hellaz-website-analyzer'))
                     .removeClass('invalid').addClass('valid');
        } catch {
            validation.text(wp.i18n.__('Invalid URL format', 'hellaz-website-analyzer'))
                     .removeClass('valid').addClass('invalid');
        }
    });

    // Test URL button
    $('#hellaz_test_button').on('click', function(e) {
        e.preventDefault();
        const url = $('#hellaz_test_url').val();
        
        wp.ajax.post('hellaz_analyze_url', {
            url: url,
            _wpnonce: hellazAdmin.nonce
        }).done(function(response) {
            $('#hellaz_test_results').html(
                `<pre>${JSON.stringify(response, null, 2)}</pre>`
            );
        }).fail(function(error) {
            $('#hellaz_test_results').html(
                `<div class="error">${error}</div>`
            );
        });
    });

    // Toggle advanced settings
    $('.hellaz-advanced-toggle').on('click', function() {
        $(this).toggleClass('active')
               .next('.hellaz-advanced-settings').slideToggle();
    });
});
