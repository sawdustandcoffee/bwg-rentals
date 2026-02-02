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
     * Gallery Slider with Thumbnails
     */
    var BWGSlider = {
        init: function() {
            var self = this;
            var $galleries = $('.bwg-property-gallery--slider');

            $galleries.each(function() {
                var $gallery = $(this);
                var $slides = $gallery.find('.bwg-property-gallery__slide');
                var $thumbnails = $gallery.find('.bwg-property-gallery__thumbnail');
                var $counter = $gallery.find('.bwg-property-gallery__current');
                var currentIndex = 0;
                var totalSlides = $slides.length;

                // Update slide display
                function showSlide(index) {
                    currentIndex = index;

                    // Update main slides
                    $slides.removeClass('bwg-property-gallery__slide--active');
                    $slides.eq(index).addClass('bwg-property-gallery__slide--active');

                    // Update thumbnails
                    $thumbnails.removeClass('bwg-property-gallery__thumbnail--active');
                    $thumbnails.eq(index).addClass('bwg-property-gallery__thumbnail--active');

                    // Update counter
                    if ($counter.length) {
                        $counter.text(index + 1);
                    }
                }

                // Navigation handlers
                $gallery.find('.bwg-property-gallery__nav--prev').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide((currentIndex - 1 + totalSlides) % totalSlides);
                });

                $gallery.find('.bwg-property-gallery__nav--next').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide((currentIndex + 1) % totalSlides);
                });

                // Thumbnail click handlers
                $thumbnails.on('click', function(e) {
                    e.preventDefault();
                    var index = parseInt($(this).data('index'), 10);
                    showSlide(index);
                });

                // Main image click opens lightbox
                $slides.on('click keydown', function(e) {
                    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                        return;
                    }
                    e.preventDefault();
                    var galleryId = $gallery.attr('id');
                    BWGLightbox.open(galleryId, currentIndex);
                });

                // Keyboard navigation when gallery is focused
                $gallery.on('keydown', function(e) {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        showSlide((currentIndex - 1 + totalSlides) % totalSlides);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        showSlide((currentIndex + 1) % totalSlides);
                    }
                });
            });

            // Initialize grid/lightbox galleries
            self.initGridGalleries();
        },

        initGridGalleries: function() {
            // Grid items open lightbox
            $(document).on('click keydown', '.bwg-property-gallery__grid-item', function(e) {
                if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                    return;
                }
                e.preventDefault();
                var $item = $(this);
                var $gallery = $item.closest('.bwg-property-gallery');
                var galleryId = $gallery.attr('id');
                var index = parseInt($item.data('index'), 10);
                BWGLightbox.open(galleryId, index);
            });
        }
    };

    /**
     * Gallery Lightbox
     */
    var BWGLightbox = {
        currentGalleryId: null,
        currentIndex: 0,
        images: [],

        init: function() {
            var self = this;

            // Close lightbox on close button click
            $(document).on('click', '.bwg-lightbox__close', function() {
                self.close();
            });

            // Close lightbox on overlay click
            $(document).on('click', '.bwg-lightbox__overlay', function() {
                self.close();
            });

            // Navigation handlers
            $(document).on('click', '.bwg-lightbox__nav--prev', function(e) {
                e.stopPropagation();
                self.prev();
            });

            $(document).on('click', '.bwg-lightbox__nav--next', function(e) {
                e.stopPropagation();
                self.next();
            });

            // Keyboard navigation
            $(document).on('keydown', function(e) {
                var $activeLightbox = $('.bwg-lightbox--active');
                if ($activeLightbox.length === 0) return;

                if (e.key === 'Escape') {
                    self.close();
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    self.prev();
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    self.next();
                }
            });
        },

        open: function(galleryId, index) {
            var self = this;
            var $lightbox = $('#' + galleryId + '-lightbox');

            if ($lightbox.length === 0) return;

            self.currentGalleryId = galleryId;
            self.currentIndex = index || 0;

            // Load images data from JSON
            var $dataScript = $lightbox.find('.bwg-lightbox__data');
            if ($dataScript.length) {
                try {
                    self.images = JSON.parse($dataScript.text());
                } catch (e) {
                    console.error('Error parsing lightbox images:', e);
                    return;
                }
            }

            // Show the image
            self.showImage($lightbox);

            // Open lightbox
            $lightbox.addClass('bwg-lightbox--active');
            $('body').css('overflow', 'hidden');

            // Focus the lightbox for keyboard navigation
            $lightbox.attr('tabindex', '-1').focus();
        },

        showImage: function($lightbox) {
            var self = this;
            if (self.images.length === 0) return;

            var image = self.images[self.currentIndex];
            var $img = $lightbox.find('.bwg-lightbox__image');
            var $counter = $lightbox.find('.bwg-lightbox__current');

            $img.attr('src', image.url);
            $img.attr('alt', image.alt);

            // Update counter
            if ($counter.length) {
                $counter.text(self.currentIndex + 1);
            }
        },

        prev: function() {
            var self = this;
            if (self.images.length === 0) return;

            self.currentIndex = (self.currentIndex - 1 + self.images.length) % self.images.length;
            var $lightbox = $('#' + self.currentGalleryId + '-lightbox');
            self.showImage($lightbox);
        },

        next: function() {
            var self = this;
            if (self.images.length === 0) return;

            self.currentIndex = (self.currentIndex + 1) % self.images.length;
            var $lightbox = $('#' + self.currentGalleryId + '-lightbox');
            self.showImage($lightbox);
        },

        close: function() {
            var self = this;
            var $lightbox = $('#' + self.currentGalleryId + '-lightbox');

            $lightbox.removeClass('bwg-lightbox--active');
            $('body').css('overflow', '');
            self.currentGalleryId = null;
            self.currentIndex = 0;
            self.images = [];
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

                // Get configuration from data attributes
                var slidesToShow = parseInt($slider.data('slides-to-show'), 10) || 1;
                var slidesToScroll = parseInt($slider.data('slides-to-scroll'), 10) || 1;

                // Responsive: Adjust slides to show based on screen width
                function getResponsiveSlidesToShow() {
                    var width = $(window).width();
                    var currentSlidesToShow = slidesToShow;

                    // Mobile: max 1 slide
                    if (width < 768) {
                        currentSlidesToShow = Math.min(1, slidesToShow);
                    }
                    // Tablet: max 2 slides
                    else if (width < 1024) {
                        currentSlidesToShow = Math.min(2, slidesToShow);
                    }
                    // Desktop: use configured value

                    return currentSlidesToShow;
                }

                // Calculate responsive values
                var responsiveSlidesToShow = getResponsiveSlidesToShow();

                // Skip if insufficient slides
                if (totalSlides <= responsiveSlidesToShow) {
                    $prevBtn.hide();
                    $nextBtn.hide();
                    $slider.find('.bwg-property-slider__indicators').hide();
                    // Set slide widths for proper display
                    var slideWidth = 100 / responsiveSlidesToShow;
                    $slides.css('flex', '0 0 ' + slideWidth + '%');
                    return;
                }

                // Autoplay and loop configuration
                var autoplay = $slider.data('autoplay') === 'true' || $slider.data('autoplay') === true;
                var speed = parseInt($slider.data('speed'), 10) || 5000;
                var loop = $slider.data('loop') === 'true' || $slider.data('loop') === true;
                var autoplayTimer = null;

                // Set slide widths based on slides to show
                function setSlidesWidth() {
                    responsiveSlidesToShow = getResponsiveSlidesToShow();
                    var slideWidth = 100 / responsiveSlidesToShow;
                    $slides.css('flex', '0 0 ' + slideWidth + '%');
                }

                // Initial slide widths
                setSlidesWidth();

                // Update on window resize
                $(window).on('resize', function() {
                    setSlidesWidth();
                    updateSlider();
                });

                // Start autoplay
                function startAutoplay() {
                    if (!autoplay) return;

                    stopAutoplay(); // Clear any existing timer
                    autoplayTimer = setInterval(function() {
                        currentIndex += slidesToScroll;
                        if (currentIndex + responsiveSlidesToShow > totalSlides) {
                            currentIndex = 0; // Loop back to start
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
                    if (loop) {
                        // Loop mode: wrap to end if at beginning
                        if (currentIndex === 0) {
                            currentIndex = totalSlides - responsiveSlidesToShow;
                        } else {
                            currentIndex = Math.max(0, currentIndex - slidesToScroll);
                        }
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    } else if (currentIndex > 0) {
                        // Non-loop mode: only navigate if not at beginning
                        currentIndex = Math.max(0, currentIndex - slidesToScroll);
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    }
                });

                // Next button handler
                $nextBtn.on('click', function() {
                    var maxIndex = totalSlides - responsiveSlidesToShow;
                    if (loop) {
                        // Loop mode: wrap to beginning if at end
                        if (currentIndex >= maxIndex) {
                            currentIndex = 0;
                        } else {
                            currentIndex = Math.min(maxIndex, currentIndex + slidesToScroll);
                        }
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    } else if (currentIndex < maxIndex) {
                        // Non-loop mode: only navigate if not at end
                        currentIndex = Math.min(maxIndex, currentIndex + slidesToScroll);
                        updateSlider();
                        if (autoplay) {
                            startAutoplay(); // Restart autoplay after manual navigation
                        }
                    }
                });

                // Indicator click handler
                $indicators.on('click', function() {
                    var targetIndex = parseInt($(this).data('slide-to'), 10);
                    var maxIndex = totalSlides - responsiveSlidesToShow;
                    currentIndex = Math.min(targetIndex, maxIndex);
                    updateSlider();
                    if (autoplay) {
                        startAutoplay(); // Restart autoplay after manual navigation
                    }
                });

                // Keyboard navigation
                $slider.on('keydown', function(e) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
                        e.preventDefault();
                        var maxIndex = totalSlides - responsiveSlidesToShow;
                        if (e.key === 'ArrowLeft') {
                            if (loop) {
                                currentIndex = currentIndex === 0 ? maxIndex : Math.max(0, currentIndex - slidesToScroll);
                            } else if (currentIndex > 0) {
                                currentIndex = Math.max(0, currentIndex - slidesToScroll);
                            } else {
                                return; // At beginning in non-loop mode
                            }
                            updateSlider();
                            if (autoplay) {
                                startAutoplay();
                            }
                        } else if (e.key === 'ArrowRight') {
                            if (loop) {
                                currentIndex = currentIndex >= maxIndex ? 0 : Math.min(maxIndex, currentIndex + slidesToScroll);
                            } else if (currentIndex < maxIndex) {
                                currentIndex = Math.min(maxIndex, currentIndex + slidesToScroll);
                            } else {
                                return; // At end in non-loop mode
                            }
                            updateSlider();
                            if (autoplay) {
                                startAutoplay();
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
                    var maxIndex = totalSlides - responsiveSlidesToShow;

                    if (Math.abs(diff) > swipeThreshold) {
                        if (diff > 0) {
                            // Swipe left - next slides
                            if (loop) {
                                currentIndex = currentIndex >= maxIndex ? 0 : Math.min(maxIndex, currentIndex + slidesToScroll);
                            } else if (currentIndex < maxIndex) {
                                currentIndex = Math.min(maxIndex, currentIndex + slidesToScroll);
                            } else {
                                return; // At end in non-loop mode
                            }
                            updateSlider();
                            if (autoplay) {
                                startAutoplay();
                            }
                        } else if (diff < 0) {
                            // Swipe right - previous slides
                            if (loop) {
                                currentIndex = currentIndex === 0 ? maxIndex : Math.max(0, currentIndex - slidesToScroll);
                            } else if (currentIndex > 0) {
                                currentIndex = Math.max(0, currentIndex - slidesToScroll);
                            } else {
                                return; // At beginning in non-loop mode
                            }
                            updateSlider();
                            if (autoplay) {
                                startAutoplay();
                            }
                        }
                    }
                });

                // Update slider position and controls
                function updateSlider() {
                    responsiveSlidesToShow = getResponsiveSlidesToShow();
                    var slideWidth = 100 / responsiveSlidesToShow;
                    var maxIndex = totalSlides - responsiveSlidesToShow;

                    // Constrain currentIndex to valid range
                    currentIndex = Math.max(0, Math.min(currentIndex, maxIndex));

                    // Move track (percentage based on slide width and current index)
                    var translateX = -(currentIndex * slideWidth);
                    $track.css('transform', 'translateX(' + translateX + '%)');

                    // Update indicators - highlight indicators for visible slides
                    $indicators.removeClass('bwg-property-slider__indicator--active');
                    for (var i = currentIndex; i < Math.min(currentIndex + responsiveSlidesToShow, totalSlides); i++) {
                        $indicators.eq(i).addClass('bwg-property-slider__indicator--active');
                    }

                    // Update navigation button states
                    if (loop) {
                        // Loop mode: buttons always enabled
                        $prevBtn.prop('disabled', false);
                        $nextBtn.prop('disabled', false);
                    } else {
                        // Non-loop mode: disable at ends
                        $prevBtn.prop('disabled', currentIndex === 0);
                        $nextBtn.prop('disabled', currentIndex >= maxIndex);
                    }

                    // Update ARIA attributes - mark visible slides
                    $slides.attr('aria-hidden', 'true');
                    for (var j = currentIndex; j < Math.min(currentIndex + responsiveSlidesToShow, totalSlides); j++) {
                        $slides.eq(j).attr('aria-hidden', 'false');
                    }
                }

                // Intersection Observer for visibility detection
                if (autoplay && typeof IntersectionObserver !== 'undefined') {
                    var observer = new IntersectionObserver(function(entries) {
                        entries.forEach(function(entry) {
                            if (entry.isIntersecting) {
                                // Slider is visible - start autoplay
                                startAutoplay();
                            } else {
                                // Slider is not visible - stop autoplay
                                stopAutoplay();
                            }
                        });
                    }, {
                        threshold: 0.1 // Trigger when at least 10% of slider is visible
                    });

                    observer.observe($slider[0]);
                }

                // Initial state
                updateSlider();

                // Start autoplay if enabled (only if no Intersection Observer support)
                if (autoplay && typeof IntersectionObserver === 'undefined') {
                    startAutoplay();
                }
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

                // Handle "More Filters" toggle for compact mode
                $form.find('.bwg-property-search__more-filters-toggle').on('click', function(e) {
                    e.preventDefault();
                    var $toggle = $(this);
                    var $moreFilters = $toggle.siblings('.bwg-property-search__more-filters');
                    var $icon = $toggle.find('.bwg-property-search__more-filters-icon');
                    var isExpanded = $moreFilters.attr('aria-hidden') === 'false';

                    if (isExpanded) {
                        // Collapse filters
                        $moreFilters.attr('aria-hidden', 'true').slideUp(300);
                        $toggle.removeClass('bwg-property-search__more-filters-toggle--expanded');
                        $icon.text('▼');
                    } else {
                        // Expand filters
                        $moreFilters.attr('aria-hidden', 'false').slideDown(300);
                        $toggle.addClass('bwg-property-search__more-filters-toggle--expanded');
                        $icon.text('▲');
                    }
                });
            });
        }
    };

    /**
     * Full Property Gallery (property-full.php)
     *
     * Main image with thumbnail strip - hover to preview, click to select
     */
    var BWGFullGallery = {
        init: function() {
            var self = this;
            var $galleries = $('.bwg-property-gallery--full');

            $galleries.each(function() {
                var $gallery = $(this);
                var $slides = $gallery.find('.bwg-property-gallery__slide');
                var $thumbs = $gallery.find('.bwg-property-gallery__thumb');
                var $counter = $gallery.find('.bwg-property-gallery__current');
                var $thumbSlider = $gallery.find('.bwg-property-gallery__thumb-slider');
                var $thumbTrack = $gallery.find('.bwg-property-gallery__thumb-track');
                var $thumbPrev = $gallery.find('.bwg-property-gallery__thumb-nav--prev');
                var $thumbNext = $gallery.find('.bwg-property-gallery__thumb-nav--next');
                var currentIndex = 0;
                var totalSlides = $slides.length;
                var thumbOffset = 0;
                var hoverTimeout = null;

                // Show a specific slide
                function showSlide(index, updateThumbScroll) {
                    currentIndex = index;

                    // Update main image
                    $slides.removeClass('bwg-property-gallery__slide--active');
                    $slides.eq(index).addClass('bwg-property-gallery__slide--active');

                    // Update thumbnails
                    $thumbs.removeClass('bwg-property-gallery__thumb--active');
                    $thumbs.eq(index).addClass('bwg-property-gallery__thumb--active');

                    // Update counter
                    if ($counter.length) {
                        $counter.text(index + 1);
                    }

                    // Scroll thumbnails to keep active one visible
                    if (updateThumbScroll !== false) {
                        self.scrollToThumb($thumbSlider, $thumbTrack, $thumbs, index);
                    }
                }

                // Main image navigation arrows
                $gallery.find('.bwg-property-gallery__nav--prev').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide((currentIndex - 1 + totalSlides) % totalSlides);
                });

                $gallery.find('.bwg-property-gallery__nav--next').on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    showSlide((currentIndex + 1) % totalSlides);
                });

                // Thumbnail hover - preview on hover with delay
                $thumbs.on('mouseenter', function() {
                    var $thumb = $(this);
                    var index = parseInt($thumb.data('index'), 10);

                    // Clear any pending timeout
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                    }

                    // Small delay before showing to prevent flickering when moving fast
                    hoverTimeout = setTimeout(function() {
                        showSlide(index, false);
                    }, 100);
                });

                $thumbs.on('mouseleave', function() {
                    // Clear timeout if leaving before it fires
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                        hoverTimeout = null;
                    }
                });

                // Thumbnail click - select and keep that image
                $thumbs.on('click', function(e) {
                    e.preventDefault();
                    var index = parseInt($(this).data('index'), 10);

                    // Clear hover timeout
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                        hoverTimeout = null;
                    }

                    showSlide(index);
                });

                // Main image click opens lightbox
                $slides.on('click keydown', function(e) {
                    if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') {
                        return;
                    }
                    e.preventDefault();
                    var galleryId = $gallery.attr('id');
                    BWGLightbox.open(galleryId, currentIndex);
                });

                // Thumbnail strip navigation
                $thumbPrev.on('click', function(e) {
                    e.preventDefault();
                    self.scrollThumbs($thumbSlider, $thumbTrack, $thumbs, 'prev');
                });

                $thumbNext.on('click', function(e) {
                    e.preventDefault();
                    self.scrollThumbs($thumbSlider, $thumbTrack, $thumbs, 'next');
                });

                // Keyboard navigation
                $gallery.attr('tabindex', '0').on('keydown', function(e) {
                    if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        showSlide((currentIndex - 1 + totalSlides) % totalSlides);
                    } else if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        showSlide((currentIndex + 1) % totalSlides);
                    }
                });

                // Initialize thumbnail nav button states
                self.updateThumbNavState($thumbSlider, $thumbTrack, $thumbPrev, $thumbNext);

                // Update on resize
                $(window).on('resize', function() {
                    self.updateThumbNavState($thumbSlider, $thumbTrack, $thumbPrev, $thumbNext);
                });
            });
        },

        /**
         * Scroll thumbnail strip left/right
         */
        scrollThumbs: function($slider, $track, $thumbs, direction) {
            var self = this;
            var trackWidth = $track.width();
            var sliderWidth = $slider[0].scrollWidth;
            var currentScroll = $slider.scrollLeft();
            var scrollAmount = trackWidth * 0.75; // Scroll 75% of visible area

            var newScroll;
            if (direction === 'prev') {
                newScroll = Math.max(0, currentScroll - scrollAmount);
            } else {
                newScroll = Math.min(sliderWidth - trackWidth, currentScroll + scrollAmount);
            }

            $slider.animate({ scrollLeft: newScroll }, 300, function() {
                self.updateThumbNavState($slider, $track,
                    $slider.closest('.bwg-property-gallery--full').find('.bwg-property-gallery__thumb-nav--prev'),
                    $slider.closest('.bwg-property-gallery--full').find('.bwg-property-gallery__thumb-nav--next')
                );
            });
        },

        /**
         * Scroll to make a specific thumbnail visible
         */
        scrollToThumb: function($slider, $track, $thumbs, index) {
            var self = this;
            var $thumb = $thumbs.eq(index);
            if (!$thumb.length) return;

            var trackWidth = $track.width();
            var thumbLeft = $thumb.position().left;
            var thumbWidth = $thumb.outerWidth(true);
            var currentScroll = $slider.scrollLeft();

            // Check if thumb is out of view
            if (thumbLeft < 0) {
                // Thumb is to the left - scroll left
                $slider.animate({ scrollLeft: currentScroll + thumbLeft - 10 }, 200);
            } else if (thumbLeft + thumbWidth > trackWidth) {
                // Thumb is to the right - scroll right
                $slider.animate({ scrollLeft: currentScroll + (thumbLeft + thumbWidth - trackWidth) + 10 }, 200);
            }
        },

        /**
         * Update nav button disabled states based on scroll position
         */
        updateThumbNavState: function($slider, $track, $prevBtn, $nextBtn) {
            if (!$slider.length) return;

            var trackWidth = $track.width();
            var sliderWidth = $slider[0].scrollWidth;
            var currentScroll = $slider.scrollLeft();

            // Disable prev if at start
            $prevBtn.prop('disabled', currentScroll <= 0);

            // Disable next if at end
            $nextBtn.prop('disabled', currentScroll >= sliderWidth - trackWidth - 1);
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
        BWGFullGallery.init();
    });

})(jQuery);
