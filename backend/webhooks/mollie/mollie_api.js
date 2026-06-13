// Deprecated client-side Mollie stub. The previous implementation fabricated
// a `paid` status client-side, which is unsafe. All Mollie interactions must
// happen server-side via mollie_api.php. This file is kept as a thin shim so
// any legacy imports still resolve; it throws on use.

function _refuse() {
    throw new Error('mollie_api.js stub disabled: use /mollie_api.php via createMolliePaymentJS / getMolliePaymentStatusJS in the root mollie_api.js');
}

async function createMolliePaymentJS() { _refuse(); }
async function getMolliePaymentStatusJS() { _refuse(); }

if (typeof window !== 'undefined') {
    window.createMolliePaymentJS = createMolliePaymentJS;
    window.getMolliePaymentStatusJS = getMolliePaymentStatusJS;
}
