/**
 * BWG Rentals Admin JavaScript
 *
 * @package BWG_Rentals
 */

(function($) {
    'use strict';

    /**
     * Form Validation
     */
    var validationRules = {
        'api-key': {
            validate: function(value) {
                if (value === '') return { valid: true }; // Optional field
                if (value.length < 8) {
                    return { valid: false, message: bwgRentalsAdmin.strings.apiKeyTooShort || 'API Key must be at least 8 characters.' };
                }
                if (!/^[a-zA-Z0-9_\-]+$/.test(value)) {
                    return { valid: false, message: bwgRentalsAdmin.strings.apiKeyInvalidChars || 'API Key can only contain letters, numbers, dashes, and underscores.' };
                }
                return { valid: true };
            }
        },
        'org-id': {
            validate: function(value) {
                if (value === '') return { valid: true }; // Optional field
                if (value.length < 2) {
                    return { valid: false, message: bwgRentalsAdmin.strings.orgIdTooShort || 'Organization ID must be at least 2 characters.' };
                }
                if (!/^[a-zA-Z0-9_\-]+$/.test(value)) {
                    return { valid: false, message: bwgRentalsAdmin.strings.orgIdInvalidChars || 'Organization ID can only contain letters, numbers, dashes, and underscores.' };
                }
                return { valid: true };
            }
        },
        'cache-duration': {
            validate: function(value) {
                var num = parseInt(value, 10);
                if (isNaN(num) || value === '') {
                    return { valid: false, message: bwgRentalsAdmin.strings.cacheDurationRequired || 'Cache duration is required.' };
                }
                if (num < 1) {
                    return { valid: false, message: bwgRentalsAdmin.strings.cacheDurationTooLow || 'Cache duration must be at least 1 hour.' };
                }
                if (num > 168) {
                    return { valid: false, message: bwgRentalsAdmin.strings.cacheDurationTooHigh || 'Cache duration cannot exceed 168 hours.' };
                }
                return { valid: true };
            }
        },
        'button-text': {
            validate: function(value) {
                if (value.length > 50) {
                    return { valid: false, message: bwgRentalsAdmin.strings.buttonTextTooLong || 'Button text cannot exceed 50 characters.' };
                }
                return { valid: true };
            }
        }
    };

    /**
     * Show field error
     */
    function showFieldError($field, message) {
        var $error = $('#' + $field.attr('id') + '-error');
        $field.addClass('bwg-field-invalid');
        $error.text(message).addClass('visible');
    }

    /**
     * Clear field error
     */
    function clearFieldError($field) {
        var $error = $('#' + $field.attr('id') + '-error');
        $field.removeClass('bwg-field-invalid');
        $error.text('').removeClass('visible');
    }

    /**
     * Validate a single field
     */
    function validateField($field) {
        var validationType = $field.data('validate');
        if (!validationType || !validationRules[validationType]) {
            return true;
        }

        var result = validationRules[validationType].validate($field.val());

        if (result.valid) {
            clearFieldError($field);
            return true;
        } else {
            showFieldError($field, result.message);
            return false;
        }
    }

    /**
     * Initialize form validation
     */
    function initFormValidation() {
        var $form = $('#bwg-settings-form');
        if (!$form.length) return;

        // Validate on blur
        $form.find('[data-validate]').on('blur', function() {
            validateField($(this));
        });

        // Clear error on input
        $form.find('[data-validate]').on('input', function() {
            clearFieldError($(this));
        });

        // Validate all fields on submit
        $form.on('submit', function(e) {
            var isValid = true;
            var $firstInvalid = null;

            $form.find('[data-validate]').each(function() {
                var $field = $(this);
                if (!validateField($field)) {
                    isValid = false;
                    if (!$firstInvalid) {
                        $firstInvalid = $field;
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
                if ($firstInvalid) {
                    $firstInvalid.focus();
                }
                return false;
            }
        });
    }

    /**
     * Initialize Test API Connection handler
     */
    function initTestConnection() {
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
    }

    /**
     * Initialize Clear Cache handler
     */
    function initClearCache() {
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
    }

    /**
     * Initialize Data Sync handler
     * Handles both #bwg-sync-data and #bwg-sync-now button IDs
     */
    function initDataSync() {
        $('#bwg-sync-data, #bwg-sync-now').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var $status = $('#bwg-sync-result');
            var $statusMessage = $('#bwg-sync-status-message');
            var $propertyCount = $('#bwg-sync-property-count');
            var $lastSync = $('#bwg-sync-last-sync');
            var $spinner = $button.find('.bwg-sync-spinner, .spinner');
            var $buttonText = $button.find('.bwg-sync-text');

            // Disable button and show loading
            $button.addClass('loading').prop('disabled', true);
            if ($spinner.length) {
                $spinner.addClass('is-active').show();
            }
            if ($buttonText.length) {
                $buttonText.text(bwgRentalsAdmin.strings.syncing || 'Syncing...');
            }
            $status.removeClass('success error').addClass('loading')
                .text(bwgRentalsAdmin.strings.syncing || 'Syncing...');
            $statusMessage.text(bwgRentalsAdmin.strings.syncing || 'Syncing...');

            $.ajax({
                url: bwgRentalsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bwg_sync_data',
                    nonce: bwgRentalsAdmin.nonce
                },
                timeout: 120000, // 2 minute timeout for large syncs
                success: function(response) {
                    $button.removeClass('loading').prop('disabled', false);
                    if ($spinner.length) {
                        $spinner.removeClass('is-active').hide();
                    }
                    if ($buttonText.length) {
                        $buttonText.text('Sync Now');
                    }
                    $status.removeClass('loading');

                    if (response.success) {
                        $status.addClass('success').text(response.data.message);
                        $statusMessage.text(bwgRentalsAdmin.strings.syncComplete || 'Complete');

                        // Update property count if returned
                        if (response.data.count !== undefined) {
                            $propertyCount.text(response.data.count);
                        }

                        // Update last sync time
                        var now = new Date();
                        $lastSync.text(now.toLocaleString());
                    } else {
                        $status.addClass('error').text(response.data.message);
                        $statusMessage.text('Failed - ' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    $button.removeClass('loading').prop('disabled', false);
                    if ($spinner.length) {
                        $spinner.removeClass('is-active').hide();
                    }
                    if ($buttonText.length) {
                        $buttonText.text('Sync Now');
                    }
                    $status.removeClass('loading').addClass('error');

                    if (status === 'timeout') {
                        $status.text('Sync timed out. Try again or check server logs.');
                        $statusMessage.text('Failed - Timeout');
                    } else {
                        $status.text(bwgRentalsAdmin.strings.error || 'An error occurred');
                        $statusMessage.text('Failed');
                    }
                }
            });
        });
    }

    /**
     * Initialize Auto-Sync toggle handler
     */
    function initAutoSyncToggle() {
        $('#bwg_auto_sync_enabled').on('change', function() {
            var $checkbox = $(this);
            var enabled = $checkbox.is(':checked');

            $.ajax({
                url: bwgRentalsAdmin.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bwg_toggle_auto_sync',
                    nonce: bwgRentalsAdmin.nonce,
                    enabled: enabled ? 'true' : 'false'
                },
                success: function(response) {
                    if (response.success) {
                        // Update the next scheduled sync display
                        var $nextScheduled = $('#bwg-sync-next-scheduled');
                        if ($nextScheduled.length) {
                            if (enabled) {
                                // Reload to get the new scheduled time
                                location.reload();
                            } else {
                                $nextScheduled.text('Not scheduled');
                            }
                        }
                    } else {
                        // Revert checkbox on failure
                        $checkbox.prop('checked', !enabled);
                        alert(response.data.message || 'Failed to update auto-sync setting.');
                    }
                },
                error: function() {
                    // Revert checkbox on error
                    $checkbox.prop('checked', !enabled);
                    alert('An error occurred while updating auto-sync setting.');
                }
            });
        });
    }

    /**
     * Initialize all functionality
     * Using both $(document).ready() and direct call for WordPress footer scripts
     */
    function init() {
        initFormValidation();
        initTestConnection();
        initClearCache();
        initDataSync();
        initAutoSyncToggle();
    }

    // Initialize when DOM is ready
    // Use both methods to ensure it runs regardless of when script loads
    if (document.readyState === 'loading') {
        $(document).ready(init);
    } else {
        // DOM is already ready, run immediately
        init();
    }

})(jQuery);
