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
     * Property Slider/Carousel
     *
     * Handles the [bwg_property_slider] shortcode functionality
     */
    var BWGPropertySlider = {
        init: function() {
            var $sliders = $('.bwg-property-slider');

            $sliders.each(function() {
                var $slider = $(this);
                var $track = $slider.find('.bwg-property-slider__track');
                var $slides = $slider.find('.bwg-property-slider__slide');
                var $prevBtn = $slider.find('.bwg-property-slider__nav--prev');
                var $nextBtn = $slider.find('.bwg-property-slider__nav--next');
                var $indicators = $slider.find('.bwg-property-slider__indicator');
                var currentIndex = 0;
                var totalSlides = $slides.length;

                // Skip if only one slide
                if (totalSlides <= 1) {
                    $prevBtn.hide();
                    $nextBtn.hide();
                    $slider.find('.bwg-property-slider__indicators').hide();
                    return;
                }

                // Autoplay configuration
                var autoplay = $slider.data('autoplay') === 'true' || $slider.data('autoplay') === true;
                var speed = parseInt($slider.data('speed'), 10) || 5000;
                var autoplayTimer = null;

                // Start autoplay
                function startAutoplay() {
                    if (!autoplay) return;

                    stopAutoplay(); // Clear any existing timer
                    autoplayTimer = setInterval(function() {
                        currentIndex++;
                        if (currentIndex >= totalSlides) {
                            currentIndex = 0; // Loop back to first slide
                        }
                        updateSlider();
                    }, speed);
                }

                // Stop autoplay
                function stopAutoplay() {
                    if (autoplayTimer) {
                        clearInterval(autoplayTimer);
                        autoplayTimer = null;
                    }
                }

                // Pause on hover
                if (autoplay) {
                    $slider.on('mouseenter', function() {
                        stopAutoplay();
                    });

                    $slider.on('mouseleave', function() {
                        startAutoplay();
                    });
                }

                // Previous button handler
                $prevBtn.on('click', function() {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    }
                });

                // Next button handler
                $nextBtn.on('click', function() {
                    if (currentIndex < totalSlides - 1) {
                        currentIndex++;
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    }
                });

                // Indicator click handler
                $indicators.on('click', function() {
                    currentIndex = parseInt($(this).data('slide-to'), 10);
                    updateSlider();
                    if (autoplay) {
                        startAutoplay(); // Restart autoplay after manual navigation
                    }
                });

                // Keyboard navigation
                $slider.on('keydown', function(e) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        if (e.key === 'ArrowLeft' && currentIndex > 0) {
                            currentIndex--;
                            updateSlider();
                            if (autoplay) {
                                startAutoplay(); // Restart autoplay after manual navigation
                            }
                        } else if (e.key === 'ArrowRight' && currentIndex < totalSlides - 1) {
                            currentIndex++;
                            updateSlider();
                            if (autoplay) {
                                startAutoplay(); // Restart autoplay after manual navigation
                            }
                        }
                    }
                });

                // Touch/swipe support
                var touchStartX = 0;
                var touchEndX = 0;

                $track.on('touchstart', function(e) {
                    touchStartX = e.touches[0].clientX;
                });

                $track.on('touchmove', function(e) {
                    touchEndX = e.touches[0].clientX;
                });

                $track.on('touchend', function() {
                    var swipeThreshold = 50;
                    var diff = touchStartX - touchEndX;

                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0 && currentIndex < totalSlides - 1) {
                            // Swipe left - next slide
                            currentIndex++;
                            updateSlider();
                            if (autoplay) {
                                startAutoplay(); // Restart autoplay after manual navigation
                            }
                        } else if (diff < 0 && currentIndex > 0) {
                            // Swipe right - previous slide
                            currentIndex--;
                            updateSlider();
                            if (autoplay) {
                                startAutoplay(); // Restart autoplay after manual navigation
                            }
                        }
                    }
                });

                // Update slider position and controls
                function updateSlider() {
                    // Move track
                    $track.css('transform', 'translateX(-' + (currentIndex * 100) + '%)');

                    // Update indicators
                    $indicators.removeClass('bwg-property-slider__indicator--active');
                    $indicators.eq(currentIndex).addClass('bwg-property-slider__indicator--active');

                    // Update navigation button states
                    $prevBtn.prop('disabled', currentIndex === 0);
                    $nextBtn.prop('disabled', currentIndex === totalSlides - 1);

                    // Update ARIA attributes
                    $slides.attr('aria-hidden', 'true');
                    $slides.eq(currentIndex).attr('aria-hidden', 'false');
                }

                // Initial state
                updateSlider();
            });
        }
    };

    /**
     * Property Filters (AJAX)
     *
     * Handles the filter dropdowns for [bwg_properties] shortcode
     */
    var BWGFilters = {
        init: function() {
            var self = this;

            // Handle filter change
            $(document).on('change', '.bwg-filter__select', function() {
                var $select = $(this);
                var $container = $select.closest('.bwg-properties-container');
                self.updateProperties($container);
            });

            // Handle reset button
            $(document).on('click', '.bwg-filter__reset', function() {
                var $button = $(this);
                var $container = $button.closest('.bwg-properties-container');

                // Reset filter selects (beds, baths, sleeps)
                $container.find('[data-filter="beds"]').val('');
                $container.find('[data-filter="baths"]').val('');
                $container.find('[data-filter="sleeps"]').val('');

                // Reset orderby to default
                $container.find('[data-filter="orderby"]').val('name');

                // Update properties
                self.updateProperties($container);
            });

            // On page load, check if there are URL parameters and apply filters
            $('.bwg-properties-container').each(function() {
                self.applyUrlFilters($(this));
            });
        },

        /**
         * Apply filters from URL parameters on page load
         */
        applyUrlFilters: function($container) {
            var urlParams = new URLSearchParams(window.location.search);
            var beds = urlParams.get('bwg_beds');
            var baths = urlParams.get('bwg_baths');
            var sleeps = urlParams.get('bwg_sleeps');
            var orderby = urlParams.get('bwg_orderby');

            if (beds || baths || sleeps || orderby) {
                // Set select values
                if (beds) {
                    $container.find('[data-filter="beds"]').val(beds);
                }
                if (baths) {
                    $container.find('[data-filter="baths"]').val(baths);
                }
                if (sleeps) {
                    $container.find('[data-filter="sleeps"]').val(sleeps);
                }
                if (orderby) {
                    $container.find('[data-filter="orderby"]').val(orderby);
                }
            }
        },

        /**
         * Update properties via AJAX
         */
        updateProperties: function($container) {
            var self = this;
            var $grid = $container.find('.bwg-properties');
            var instanceId = $grid.data('instance');

            // Get filter values
            var beds = $container.find('[data-filter="beds"]').val() || '';
            var baths = $container.find('[data-filter="baths"]').val() || '';
            var sleeps = $container.find('[data-filter="sleeps"]').val() || '';
            var orderby = $container.find('[data-filter="orderby"]').val() || 'name';

            // Get shortcode attributes (stored in data attributes if needed)
            var atts = {
                layout: 'grid',
                columns: 3,
                limit: -1,
                orderby: orderby
            };

            // Show loading state
            $grid.css('opacity', '0.5');
            $grid.css('pointer-events', 'none');

            // Update URL without page reload
            self.updateUrl(beds, baths, sleeps, orderby);

            // Make AJAX request
            $.ajax({
                url: bwgRentals.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'bwg_filter_properties',
                    nonce: bwgRentals.filterNonce,
                    beds: beds,
                    baths: baths,
                    sleeps: sleeps,
                    atts: JSON.stringify(atts)
                },
                success: function(response) {
                    if (response.success) {
                        // Update the grid content
                        $grid.html(response.data.html);

                        // Show result count if available
                        if (response.data.count !== undefined) {
                            // Could add a count display here if needed
                            console.log('Found ' + response.data.count + ' properties');
                        }
                    } else {
                        console.error('Filter error:', response.data.message);
                        alert('Error filtering properties. Please try again.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    alert('Error filtering properties. Please try again.');
                },
                complete: function() {
                    // Remove loading state
                    $grid.css('opacity', '1');
                    $grid.css('pointer-events', 'auto');
                }
            });
        },

        /**
         * Update URL parameters without page reload
         */
        updateUrl: function(beds, baths, sleeps, orderby) {
            var url = new URL(window.location);

            // Remove existing filter parameters
            url.searchParams.delete('bwg_beds');
            url.searchParams.delete('bwg_baths');
            url.searchParams.delete('bwg_sleeps');
            url.searchParams.delete('bwg_orderby');

            // Add new parameters if they have values
            if (beds) {
                url.searchParams.set('bwg_beds', beds);
            }
            if (baths) {
                url.searchParams.set('bwg_baths', baths);
            }
            if (sleeps) {
                url.searchParams.set('bwg_sleeps', sleeps);
            }
            if (orderby && orderby !== 'name') {
                // Only add orderby to URL if it's not the default
                url.searchParams.set('bwg_orderby', orderby);
            }

            // Update URL without reloading page
            window.history.pushState({}, '', url);
        }
    };

    /**
     * Property Search AJAX
     */
    var BWGSearch = {
        init: function() {
            var $searchForms = $('.bwg-property-search');

            $searchForms.each(function() {
                var $form = $(this);
                var $button = $form.find('.bwg-property-search__button');
                var $resultsContainer = $form.next('.bwg-search-results');

                // If no results container exists, create one
                if ($resultsContainer.length === 0) {
                    $resultsContainer = $('<div class="bwg-search-results"></div>');
                    $form.after($resultsContainer);
                }

                // Handle form submission
                $form.on('submit', function(e) {
                    e.preventDefault();

                    // Get form data
                    var checkIn = $form.find('[name="check_in"]').val();
                    var checkOut = $form.find('[name="check_out"]').val();
                    var guests = $form.find('[name="guests"]').val();
                    var bedrooms = $form.find('[name="bedrooms"]').val();
                    var location = $form.find('[name="location"]').val();
                    var amenities = $form.find('[name="amenities[]"]').val() || [];

                    // Validate date range (check-out must be after check-in)
                    if (checkIn && checkOut) {
                        var checkInDate = new Date(checkIn);
                        var checkOutDate = new Date(checkOut);

                        if (checkOutDate <= checkInDate) {
                            alert('Check-out date must be after check-in date.');
                            return false;
                        }
                    }

                    // Show loading state
                    $resultsContainer.addClass('bwg-search-results--loading');
                    $resultsContainer.html('<div class="bwg-search-results__loader"><span class="bwg-spinner"></span><p>Searching properties...</p></div>');
                    $button.prop('disabled', true).addClass('bwg-property-search__button--loading');

                    // Make AJAX request
                    $.ajax({
                        url: bwgRentals.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'bwg_search_properties',
                            nonce: bwgRentals.searchNonce,
                            check_in: checkIn,
                            check_out: checkOut,
                            guests: guests,
                            bedrooms: bedrooms,
                            location: location,
                            amenities: amenities
                        },
                        success: function(response) {
                            if (response.success) {
                                // Update results container with HTML
                                $resultsContainer.html(response.data.html);

                                // Show count message
                                if (response.data.count !== undefined) {
                                    var countMessage = response.data.count === 0 ?
                                        'No properties found matching your criteria.' :
                                        'Found ' + response.data.count + ' ' + (response.data.count === 1 ? 'property' : 'properties');

                                    $resultsContainer.prepend('<div class="bwg-search-results__count">' + countMessage + '</div>');
                                }

                                // Scroll to results
                                $('html, body').animate({
                                    scrollTop: $resultsContainer.offset().top - 100
                                }, 500);
                            } else {
                                $resultsContainer.html('<div class="bwg-search-results__error">' + response.data.message + '</div>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Search AJAX error:', error);
                            $resultsContainer.html('<div class="bwg-search-results__error">Error searching properties. Please try again.</div>');
                        },
                        complete: function() {
                            // Remove loading state
                            $resultsContainer.removeClass('bwg-search-results--loading');
                            $button.prop('disabled', false).removeClass('bwg-property-search__button--loading');
                        }
                    });
                });

                // Handle reset button
                $form.find('.bwg-property-search__reset').on('click', function(e) {
                    e.preventDefault();
                    var clearUrl = $(this).data('clear-url');
                    if (clearUrl) {
                        // Redirect to base URL without query parameters
                        window.location.href = clearUrl;
                    } else {
                        // Fallback: just reset the form
                        $form[0].reset();
                        $resultsContainer.empty();
                    }
                });
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
        BWGPropertySlider.init();
        BWGFilters.init();
        BWGSearch.init();
    });

})(jQuery);
