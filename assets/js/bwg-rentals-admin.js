/**
 * BWG Rentals Admin JavaScript
 *
 * @package BWG_Rentals
 */

(function($) {
    'use strict';

    /**
     * Test API Connection
     */
    $('#bwg-test-connection').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $status = $('#bwg-connection-status');

        // Disable button and show loading
        $button.addClass('loading').prop('disabled', true);
        $status.removeClass('success error').addClass('loading')
            .text(bwgRentalsAdmin.strings.testing);

        $.ajax({
            url: bwgRentalsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bwg_test_connection',
                nonce: bwgRentalsAdmin.nonce
            },
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false);
                $status.removeClass('loading');

                if (response.success) {
                    $status.addClass('success').text(response.data.message);
                } else {
                    $status.addClass('error').text(response.data.message);
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                $status.removeClass('loading').addClass('error')
                    .text(bwgRentalsAdmin.strings.error);
            }
        });
    });

    /**
     * Clear Cache
     */
    $('#bwg-clear-cache').on('click', function(e) {
        e.preventDefault();

        var $button = $(this);
        var $status = $('#bwg-cache-status');

        // Disable button and show loading
        $button.addClass('loading').prop('disabled', true);
        $status.removeClass('success error').addClass('loading')
            .text(bwgRentalsAdmin.strings.clearing);

        $.ajax({
            url: bwgRentalsAdmin.ajaxUrl,
            type: 'POST',
            data: {
                action: 'bwg_clear_cache',
                nonce: bwgRentalsAdmin.nonce
            },
            success: function(response) {
                $button.removeClass('loading').prop('disabled', false);
                $status.removeClass('loading');

                if (response.success) {
                    $status.addClass('success').text(bwgRentalsAdmin.strings.cacheCleared);
                    // Reload page after short delay to update cache status display
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $status.addClass('error').text(response.data.message);
                }
            },
            error: function() {
                $button.removeClass('loading').prop('disabled', false);
                $status.removeClass('loading').addClass('error')
                    .text(bwgRentalsAdmin.strings.error);
            }
        });
    });

})(jQuery);
