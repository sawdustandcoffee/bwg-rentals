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
        init: function() {
            $(document).on('click', '.bwg-availability-calendar__nav', function() {
                var $calendar = $(this).closest('.bwg-property-availability');
                var direction = $(this).data('direction');
                var $months = $calendar.find('.bwg-availability-calendar__month');
                var currentOffset = parseInt($calendar.data('offset') || 0);

                if (direction === 'prev' && currentOffset > 0) {
                    currentOffset--;
                } else if (direction === 'next') {
                    currentOffset++;
                }

                $calendar.data('offset', currentOffset);

                // TODO: Implement AJAX loading of calendar months
                // or client-side month generation
            });
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
