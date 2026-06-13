<?php
/**
 * Booking Modal Component
 * Provides the booking modal structure for the booking system
 * Includes support for direct checkout functionality
 */
if (!defined('BOOKING_ADMIN_FEE_PERCENT')) {
    require_once __DIR__ . '/config.php';
}
$modalAdminFeePct = (float) BOOKING_ADMIN_FEE_PERCENT;
$modalAdminFeePctParam = $modalAdminFeePct == floor($modalAdminFeePct) ? (int) $modalAdminFeePct : $modalAdminFeePct;
$modalAdminFeeI18nParams = htmlspecialchars(json_encode(['percent' => $modalAdminFeePctParam], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
?>
<!-- Booking Modal -->
<div id="bookingModal" class="booking-modal">
    <div class="booking-modal-content">
        <div class="booking-modal-header">
            <h2 id="modalTitle" data-i18n="booking_title">Reservering voltooien</h2>
            <button class="booking-modal-close"
                    id="closeBookingModal"
                    aria-label="Sluiten"
                    data-i18n="btn_close"
                    data-i18n-attr="aria-label">&times;</button>
        </div>
        
        <div class="booking-modal-body">
            <!-- Availability Check (Loading State) -->
            <div id="availabilityCheck" class="availability-check hidden">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p data-i18n="booking_modal_checking_availability">Beschikbaarheid controleren...</p>
                </div>
            </div>
            
            <!-- Booking Details Form -->
            <form id="bookingDetailsForm" class="booking-details-form hidden">
                <div class="booking-summary">
                    <h3 data-i18n="booking_summary_title">Reserveringsoverzicht</h3>
                    <div class="summary-item">
                        <span class="summary-label" data-i18n="booking_summary_date">Datum:</span>
                        <span class="summary-value" id="summaryDate">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label" data-i18n="booking_summary_boat">Boot type:</span>
                        <span class="summary-value" id="summaryBoat">-</span>
                    </div>
                </div>

                <p class="admin-fee-disclosure booking-modal__admin-fee-note" data-i18n="admin_fee_disclosure_note" data-i18n-params="<?php echo $modalAdminFeeI18nParams; ?>"></p>
                
                <div class="form-group">
                    <label for="rentalEndDate" data-i18n="booking_modal_end_date_label">Einddatum (optioneel)</label>
                    <input type="date" id="rentalEndDate" name="rentalEndDate">
                </div>
                
                <div class="form-group" id="engineOptionContainer" style="display: none;">
                    <label class="checkbox-container">
                        <input type="checkbox" id="engineOption" name="engineOption" value="with">
                        <span class="checkmark"></span>
                        <span class="label-text" data-i18n="booking_modal_engine_option">Met buitenboordmotor (+ meerprijs)</span>
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="button" id="addToCartBtn" class="btn btn-secondary" data-i18n="btn_add_to_cart">🛒 Toevoegen aan winkelwagen</button>
                    <button type="button" id="directCheckoutBtn" class="btn btn-primary" data-i18n="booking_modal_direct_checkout_btn">💳 Direct afrekenen</button>
                    <button type="submit" class="btn btn-primary" style="display: none;" data-i18n="booking_modal_confirm_btn">Reservering bevestigen</button>
                    <button type="button" id="cancelBooking" class="btn btn-outline" data-i18n="booking_modal_cancel_btn">Annuleren</button>
                </div>
            </form>
            
            <!-- Booking Success Message -->
            <div id="bookingSuccess" class="booking-success hidden">
                <div class="success-icon">✅</div>
                <h3 data-i18n="booking_modal_success_title">Reservering geslaagd!</h3>
                <p data-i18n="booking_modal_success_text">Je reservering is bevestigd. Je ontvangt binnenkort een bevestigingsmail.</p>
                <div class="booking-id">
                    <strong data-i18n="booking_modal_booking_id_label">Reserverings-ID:</strong> <span id="bookingId"></span>
                </div>
                <button type="button" id="closeSuccessModal" class="btn btn-primary" data-i18n="btn_close">Sluiten</button>
            </div>
            
            <!-- Booking Error Message -->
            <div id="bookingError" class="booking-error hidden">
                <div class="error-icon">❌</div>
                <h3 data-i18n="booking_modal_error_title">Fout</h3>
                <p id="errorMessage" data-i18n="booking_modal_error_default">Er is een fout opgetreden bij het verwerken van je reservering.</p>
                <button type="button" id="retryBooking" class="btn btn-primary" data-i18n="booking_modal_retry_btn">Opnieuw proberen</button>
                <button type="button" id="cancelBooking" class="btn btn-outline" data-i18n="booking_modal_cancel_btn">Annuleren</button>
            </div>
        </div>
    </div>
</div>
