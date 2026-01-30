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

    // Initialize on document ready
    $(document).ready(function() {
        initFormValidation();
    });

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
