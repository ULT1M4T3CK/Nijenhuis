// Modal-related JavaScript extracted from index.html

function showBoatInfo(boatId) {
    const boatData = window.boatData || {};
    const boat = boatData[boatId];
    if (!boat) return;

    // Create modal HTML
    const modalHTML = `
        <div id="boatModal" class="boat-modal">
            <div class="boat-modal-content">
                <span class="boat-modal-close" onclick="closeBoatModal()">&times;</span>
                <div class="boat-modal-header">
                    <img src="${boat.image}" alt="${boat.name}" class="boat-modal-image">
                    <div class="boat-modal-title">
                        <h2>${boat.name}</h2>
                        <p class="boat-modal-price">${boat.price}</p>
                        <p class="boat-modal-capacity">${boat.capacity}</p>
                    </div>
                </div>
                <div class="boat-modal-body">
                    <div class="boat-modal-section">
                        <h3>Beschrijving</h3>
                        <p>${boat.description}</p>
                    </div>
                    <div class="boat-modal-section">
                        <h3>Kenmerken</h3>
                        <ul>
                            ${boat.features.map(feature => `<li>${feature}</li>`).join('')}
                        </ul>
                    </div>
                    <div class="boat-modal-section">
                        <h3>Specificaties</h3>
                        <div class="specifications-grid">
                            ${Object.entries(boat.specifications).map(([key, value]) => 
                                `<div class="spec-item">
                                    <strong>${key}:</strong> ${value}
                                </div>`
                            ).join('')}
                        </div>
                    </div>
                </div>
                <div class="boat-modal-footer">
                    <button class="btn" onclick="closeBoatModal()">Sluiten</button>
                    <button class="btn btn-primary" onclick="bookBoat('${boatId}')">Nu Boeken</button>
                </div>
            </div>
        </div>
    `;

    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Add modal styles if not already present
    if (!document.getElementById('boatModalStyles')) {
        const styles = `
            <style id="boatModalStyles">
                .boat-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.8);
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 20px;
                    box-sizing: border-box;
                }
                .boat-modal-content {
                    background: white;
                    border-radius: 12px;
                    max-width: 800px;
                    width: 100%;
                    max-height: 90vh;
                    overflow-y: auto;
                    position: relative;
                    animation: modalSlideIn 0.3s ease;
                }
                @keyframes modalSlideIn {
                    from { opacity: 0; transform: translateY(-50px); }
                    to { opacity: 1; transform: translateY(0); }
                }
                .boat-modal-close {
                    position: absolute;
                    top: 15px;
                    right: 20px;
                    font-size: 28px;
                    font-weight: bold;
                    cursor: pointer;
                    color: #666;
                    z-index: 1;
                }
                .boat-modal-close:hover { color: #000; }
                .boat-modal-header { display: flex; gap: 20px; padding: 30px; border-bottom: 1px solid #eee; }
                .boat-modal-image { width: 200px; height: 150px; object-fit: cover; border-radius: 8px; }
                .boat-modal-title h2 { color: var(--secondary-color); margin-bottom: 10px; }
                .boat-modal-price { font-size: 1.5rem; font-weight: bold; color: var(--primary-color); margin-bottom: 5px; }
                .boat-modal-capacity { color: #666; font-size: 1.1rem; }
                .boat-modal-body { padding: 30px; }
                .boat-modal-section { margin-bottom: 30px; }
                .boat-modal-section h3 { color: var(--secondary-color); margin-bottom: 15px; font-size: 1.3rem; }
                .boat-modal-section ul { list-style: none; padding: 0; }
                .boat-modal-section li { padding: 8px 0; border-bottom: 1px solid #eee; }
                .boat-modal-section li:last-child { border-bottom: none; }
                .boat-modal-section li::before { content: "✓"; color: var(--primary-color); font-weight: bold; margin-right: 10px; }
                .specifications-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
                .spec-item { padding: 10px; background: #f8f9fa; border-radius: 6px; border-left: 4px solid var(--primary-color); }
                .boat-modal-footer { padding: 20px 30px; border-top: 1px solid #eee; display: flex; gap: 15px; justify-content: flex-end; }
                .btn-primary { background: var(--primary-color); }
                .btn-primary:hover { background: var(--secondary-color); }
                @media (max-width: 768px) {
                    .boat-modal-header { flex-direction: column; text-align: center; }
                    .boat-modal-image { width: 100%; height: 200px; }
                    .specifications-grid { grid-template-columns: 1fr; }
                    .boat-modal-footer { flex-direction: column; }
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', styles);
    }

    // Show modal
    document.getElementById('boatModal').style.display = 'flex';
}

function closeBoatModal() {
    const modal = document.getElementById('boatModal');
    if (modal) {
        modal.remove();
    }
}

function bookBoat(boatId) {
    closeBoatModal();
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        bookingForm.scrollIntoView({ behavior: 'smooth' });
        const boatTypeSelect = document.getElementById('boatType');
        if (boatTypeSelect) {
            boatTypeSelect.value = boatId;
        }
    }
}

document.addEventListener('click', function(event) {
    const modal = document.getElementById('boatModal');
    if (modal && event.target === modal) {
        closeBoatModal();
    }
}); 