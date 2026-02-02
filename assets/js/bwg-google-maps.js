/**
 * BWG Rentals Google Maps Integration
 *
 * Initializes Google Maps for property location display.
 * Only loaded when a Google Maps API key is configured.
 *
 * @package BWG_Rentals
 */

(function($) {
    'use strict';

    /**
     * BWG Google Maps Handler
     */
    var BWGGoogleMaps = {
        maps: [],
        markers: [],
        initialized: false,

        /**
         * Initialize all Google Maps on the page
         */
        init: function() {
            var self = this;

            // Find all map containers
            var $mapContainers = $('.bwg-property-location__google-map');

            if ($mapContainers.length === 0) {
                return;
            }

            $mapContainers.each(function() {
                self.initMap($(this));
            });

            self.initialized = true;
        },

        /**
         * Initialize a single map
         *
         * @param {jQuery} $container Map container element
         */
        initMap: function($container) {
            var self = this;
            var mapId = $container.attr('id');

            // Get coordinates from data attributes
            var lat = parseFloat($container.data('lat'));
            var lng = parseFloat($container.data('lng'));
            var title = $container.data('title') || '';
            var address = $container.data('address') || '';

            // Validate coordinates
            if (isNaN(lat) || isNaN(lng)) {
                console.error('BWG Google Maps: Invalid coordinates for map', mapId);
                self.showError($container, 'Unable to load map: Invalid coordinates');
                return;
            }

            var position = { lat: lat, lng: lng };

            // Hide loading indicator
            $container.find('.bwg-property-location__map-loading').hide();

            // Create map options
            var mapOptions = {
                center: position,
                zoom: 15,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
                    position: google.maps.ControlPosition.TOP_RIGHT
                },
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_CENTER
                },
                streetViewControl: true,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_BOTTOM
                },
                fullscreenControl: true,
                fullscreenControlOptions: {
                    position: google.maps.ControlPosition.RIGHT_TOP
                },
                scaleControl: true,
                styles: self.getMapStyles()
            };

            try {
                // Create the map
                var map = new google.maps.Map($container[0], mapOptions);
                self.maps.push(map);

                // Create marker
                var marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: title,
                    animation: google.maps.Animation.DROP
                });
                self.markers.push(marker);

                // Create info window with property details
                if (title || address) {
                    var infoContent = '<div class="bwg-google-maps-info">';
                    if (title) {
                        infoContent += '<h4 class="bwg-google-maps-info__title">' + self.escapeHtml(title) + '</h4>';
                    }
                    if (address) {
                        infoContent += '<p class="bwg-google-maps-info__address">' + self.escapeHtml(address) + '</p>';
                    }
                    infoContent += '<a href="https://www.google.com/maps/dir/?api=1&destination=' + lat + ',' + lng + '" target="_blank" rel="noopener noreferrer" class="bwg-google-maps-info__directions">Get Directions</a>';
                    infoContent += '</div>';

                    var infoWindow = new google.maps.InfoWindow({
                        content: infoContent,
                        maxWidth: 300
                    });

                    // Open info window on marker click
                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    // Auto-open info window initially
                    infoWindow.open(map, marker);
                }

                // Handle resize events
                $(window).on('resize', function() {
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(position);
                });

            } catch (error) {
                console.error('BWG Google Maps: Error initializing map', error);
                self.showError($container, 'Unable to load map. Please try again later.');
            }
        },

        /**
         * Show error message in map container
         *
         * @param {jQuery} $container Map container
         * @param {string} message Error message
         */
        showError: function($container, message) {
            $container.html(
                '<div class="bwg-property-location__map-error">' +
                '<p>' + this.escapeHtml(message) + '</p>' +
                '</div>'
            );
        },

        /**
         * Escape HTML special characters
         *
         * @param {string} text Text to escape
         * @return {string} Escaped text
         */
        escapeHtml: function(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        },

        /**
         * Get custom map styles for a clean, modern look
         *
         * @return {Array} Map styles array
         */
        getMapStyles: function() {
            return [
                {
                    "featureType": "poi",
                    "elementType": "labels",
                    "stylers": [
                        { "visibility": "simplified" }
                    ]
                },
                {
                    "featureType": "transit",
                    "stylers": [
                        { "visibility": "simplified" }
                    ]
                }
            ];
        }
    };

    /**
     * Global callback function for Google Maps API
     * This is called when the Google Maps API is fully loaded
     */
    window.bwgInitGoogleMaps = function() {
        BWGGoogleMaps.init();
    };

    /**
     * If Google Maps API is already loaded (cached), initialize immediately
     */
    $(document).ready(function() {
        // Check if Google Maps is already available
        if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
            BWGGoogleMaps.init();
        }
    });

})(jQuery);
