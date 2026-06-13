// Client-side helpers for the Mollie proxy (mollie_api.php).
// IMPORTANT: This file must never forge payment results. All payment state
// must come from the server (or Mollie via the server). The previous version
// of this file contained a developer stub that returned a fake `paid` status
// and a fake `tr_test_*` payment id; it has been removed to prevent any code
// path from trusting a client-fabricated success.

async function createMolliePaymentJS(paymentData) {
    const basePath = window.location.origin;
    const response = await fetch(`${basePath}/mollie_api.php?action=createPayment`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify(paymentData)
    });

    const result = await response.json().catch(() => ({}));
    if (response.ok) {
        return result;
    }

    if (response.status === 400) {
        if (result.error === 'payment_method_required' || result.error === 'payment_method_invalid') {
            const err = new Error('payment_method');
            err.paymentMethodError = result.error;
            throw err;
        }
        if (result.error === 'pay_on_arrival_arrival_invalid' || result.error === 'customer_details_required') {
            throw new Error(result.error);
        }
    }

    const message = (result && result.message) || `Payment request failed (HTTP ${response.status})`;
    const err = new Error(message);
    err.status = response.status;
    throw err;
}

async function getMolliePaymentStatusJS(paymentId, opts = {}) {
    const basePath = window.location.origin;
    const params = new URLSearchParams({ action: 'getPaymentStatus', paymentId });
    if (opts.bookingId) params.set('bookingId', opts.bookingId);
    if (opts.cartId) params.set('cartId', opts.cartId);
    const response = await fetch(`${basePath}/mollie_api.php?${params.toString()}`, {
        credentials: 'same-origin'
    });
    if (!response.ok) {
        const message = `Payment status request failed (HTTP ${response.status})`;
        const err = new Error(message);
        err.status = response.status;
        throw err;
    }
    return await response.json();
}

window.createMolliePaymentJS = createMolliePaymentJS;
window.getMolliePaymentStatusJS = getMolliePaymentStatusJS;
