document.addEventListener('DOMContentLoaded', function() {
    // Real-time URL validation
    document.querySelectorAll('.hellaz-url-input').forEach(input => {
        input.addEventListener('input', function(e) {
            const url = e.target.value;
            const validationEl = document.getElementById(`${e.target.id}-validation`);
            
            if (isValidUrl(url)) {
                validationEl.textContent = wp.i18n.__('Valid URL', 'hellaz-website-analyzer');
                validationEl.className = 'hellaz-valid';
            } else {
                validationEl.textContent = wp.i18n.__('Invalid URL format', 'hellaz-website-analyzer');
                validationEl.className = 'hellaz-invalid';
            }
        });
    });

    function isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }
});
