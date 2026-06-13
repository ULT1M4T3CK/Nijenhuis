/**
 * useBoatData Hook
 * 
 * A wrapper around the global BoatDataService to provide a consistent
 * interface for accessing boat configuration and static data.
 */
(function (window) {
    'use strict';

    function useBoatData() {
        // Ensure service is available
        const service = window.BoatDataService;
        if (!service) {
            console.error('BoatDataService not found. Ensure boat-data-service.js is loaded.');
            return null;
        }

        return {
            /**
             * Get all boats, optionally forcing a refresh from server
             * @param {boolean} forceRefresh 
             * @returns {Promise<Array>} Array of boat objects
             */
            getAll: (forceRefresh = false) => service.getAllBoats(forceRefresh),

            /**
             * Get a specific boat by ID
             * @param {string} id 
             * @returns {Promise<Object|null>}
             */
            getById: (id) => service.getBoatById(id),

            /**
             * Get boats by category
             * @param {string} category 
             * @returns {Promise<Array>}
             */
            getByCategory: (category) => service.getBoatsByCategory(category),

            /**
             * Get display name for a boat
             * @param {string} id 
             * @param {string|null} engineOption 'with' or 'without'
             * @returns {Promise<string>}
             */
            getDisplayName: (id, engineOption = null) => service.getBoatDisplayName(id, engineOption),

            /**
             * Calculate price for a duration
             * @param {string} id 
             * @param {number} days 
             * @returns {Promise<number>}
             */
            getPrice: (id, days) => service.getBoatPrice(id, days),

            /**
             * Subscribe to changes in boat data
             * @param {Function} callback 
             * @returns {Function} Unsubscribe function
             */
            subscribe: (callback) => service.subscribe(callback)
        };
    }

    // Expose to window
    window.useBoatData = useBoatData;

})(window);
