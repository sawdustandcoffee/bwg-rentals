/**
 * BWG Rentals Public JavaScript
 *
 * Handles gallery slider, lightbox, and calendar navigation.
 *
 * @package BWG_Rentals
 */

(function($) {
    'use strict';

    /**
     * Gallery Slider
     */
    var BWGSlider = {
        init: function() {
            var $sliders = $('.bwg-property-gallery__slider');

            $sliders.each(function() {
                var $slider = $(this);
                var $slides = $slider.find('.bwg-property-gallery__slides');
                var $slideItems = $slides.children();
                var currentIndex = 0;
                var totalSlides = $slideItems.length;

                // Navigation handlers
                $slider.find('.bwg-property-gallery__nav--prev').on('click', function() {
                    currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                    updateSlider();
                });

                $slider.find('.bwg-property-gallery__nav--next').on('click', function() {
                    currentIndex = (currentIndex + 1) % totalSlides;
                    updateSlider();
                });

                function updateSlider() {
                    $slides.css('transform', 'translateX(-' + (currentIndex * 100) + '%)');
                }
            });
        }
    };

    /**
     * Gallery Lightbox
     */
    var BWGLightbox = {
        $lightbox: null,

        init: function() {
            var self = this;

            // Create lightbox element if it doesn't exist
            if ($('#bwg-lightbox').length === 0) {
                $('body').append(
                    '<div id="bwg-lightbox" class="bwg-lightbox">' +
                        '<button class="bwg-lightbox__close" aria-label="Close">&times;</button>' +
                        '<img class="bwg-lightbox__image" src="" alt="" />' +
                    '</div>'
                );
            }

            self.$lightbox = $('#bwg-lightbox');

            // Open lightbox on gallery grid image click
            $(document).on('click', '.bwg-property-gallery--grid img', function() {
                var src = $(this).attr('src');
                var alt = $(this).attr('alt');

                self.$lightbox.find('.bwg-lightbox__image')
                    .attr('src', src)
                    .attr('alt', alt);

                self.$lightbox.addClass('bwg-lightbox--active');
                $('body').css('overflow', 'hidden');
            });

            // Close lightbox on close button click
            self.$lightbox.find('.bwg-lightbox__close').on('click', function() {
                self.close();
            });

            // Close lightbox on backdrop click
            self.$lightbox.on('click', function(e) {
                if (e.target === this) {
                    self.close();
                }
            });

            // Close lightbox on escape key
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.$lightbox.hasClass('bwg-lightbox--active')) {
                    self.close();
                }
            });
        },

        close: function() {
            this.$lightbox.removeClass('bwg-lightbox--active');
            $('body').css('overflow', '');
        }
    };

    /**
     * Availability Calendar Navigation
     */
    var BWGCalendar = {
        dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],

        init: function() {
            var self = this;

            $(document).on('click', '.bwg-availability-calendar__nav', function(e) {
                e.preventDefault();

                var $button = $(this);
                var $container = $button.closest('.bwg-property-availability');
                var direction = $button.data('direction');
                var currentOffset = parseInt($container.data('offset') || 0, 10);
                var monthsToShow = parseInt($container.data('months-to-show') || 3, 10);
                var baseDate = $container.data('base-date');

                // Calculate new offset
                if (direction === 'prev') {
                    currentOffset -= monthsToShow;
                } else if (direction === 'next') {
                    currentOffset += monthsToShow;
                }

                // Update the offset
                $container.data('offset', currentOffset);
                $container.attr('data-offset', currentOffset);

                // Update prev button disabled state (can't go before today)
                var $prevButton = $container.find('.bwg-availability-calendar__nav--prev');
                if (currentOffset <= 0) {
                    $prevButton.prop('disabled', true);
                    // Reset to 0 if we went negative
                    if (currentOffset < 0) {
                        currentOffset = 0;
                        $container.data('offset', 0);
                        $container.attr('data-offset', 0);
                    }
                } else {
                    $prevButton.prop('disabled', false);
                }

                // Regenerate calendar months
                self.regenerateCalendar($container, baseDate, currentOffset, monthsToShow);
            });

            // Initialize prev button state on page load
            $('.bwg-property-availability').each(function() {
                var $container = $(this);
                var currentOffset = parseInt($container.data('offset') || 0, 10);
                var $prevButton = $container.find('.bwg-availability-calendar__nav--prev');

                if (currentOffset <= 0) {
                    $prevButton.prop('disabled', true);
                }
            });
        },

        /**
         * Regenerate calendar HTML for the given offset
         */
        regenerateCalendar: function($container, baseDate, offset, monthsToShow) {
            var self = this;
            var $calendarWrapper = $container.find('.bwg-availability-calendar');

            // Parse the base date
            var startDate = new Date(baseDate);
            if (isNaN(startDate.getTime())) {
                startDate = new Date();
            }

            // Add the offset months
            startDate.setMonth(startDate.getMonth() + offset);

            // Generate HTML for each month
            var html = '';
            for (var m = 0; m < monthsToShow; m++) {
                var monthDate = new Date(startDate);
                monthDate.setMonth(startDate.getMonth() + m);
                html += self.generateMonthHTML(monthDate);
            }

            // Update the calendar
            $calendarWrapper.html(html);
        },

        /**
         * Generate HTML for a single month
         */
        generateMonthHTML: function(date) {
            var self = this;
            var year = date.getFullYear();
            var month = date.getMonth();

            // Get first day of month and number of days
            var firstDay = new Date(year, month, 1);
            var lastDay = new Date(year, month + 1, 0);
            var daysInMonth = lastDay.getDate();
            var firstDayOfWeek = firstDay.getDay();

            // Month name
            var monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            var monthTitle = monthNames[month] + ' ' + year;

            var html = '<div class="bwg-availability-calendar__month">';
            html += '<div class="bwg-availability-calendar__title">' + monthTitle + '</div>';
            html += '<div class="bwg-availability-calendar__grid">';

            // Day headers
            for (var i = 0; i < self.dayNames.length; i++) {
                html += '<div class="bwg-availability-calendar__day-header">' + self.dayNames[i] + '</div>';
            }

            // Empty cells before first day
            for (var e = 0; e < firstDayOfWeek; e++) {
                html += '<div class="bwg-availability-calendar__day bwg-availability-calendar__day--empty"></div>';
            }

            // Days of the month
            for (var d = 1; d <= daysInMonth; d++) {
                // Default to available (actual availability check would require API data)
                var dayClass = 'bwg-availability-calendar__day bwg-availability-calendar__day--available';
                html += '<div class="' + dayClass + '">' + d + '</div>';
            }

            html += '</div></div>';

            return html;
        }
    };

    /**
     * Initialize on DOM ready
     */
    $(document).ready(function() {
        BWGSlider.init();
        BWGLightbox.init();
        BWGCalendar.init();
    });

})(jQuery);
