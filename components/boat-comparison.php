<!-- Boat Comparison Section -->
<section id="boatComparison" class="comparison-section">
    <div class="container">
        <div class="section-title">
            <h3>Vergelijk Boten</h3>
            <p>Bekijk en vergelijk specificaties van al onze boten.</p>
        </div>

        <!-- Filter Controls -->
        <div class="comparison-filters">
            <div class="filter-group">
                <label>Capaciteit:</label>
                <select id="compFilterCapacity">
                    <option value="all">Alle</option>
                    <option value="1-4">1-4 personen</option>
                    <option value="5-8">5-8 personen</option>
                    <option value="9+">9+ personen</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Type:</label>
                <select id="compFilterType">
                    <option value="all">Alle</option>
                    <option value="electric">Elektrisch</option>
                    <option value="sailing">Zeilboot</option>
                    <option value="canoe">Kano/Kajak/Sup</option>
                </select>
            </div>
            <button class="btn btn-outline btn-sm" id="clearCompFilters" style="height: 40px;">Filters Wissen</button>
        </div>

        <!-- Pinned Boats Bar (Floating or Fixed) -->
        <div id="pinnedBoatsBar" class="pinned-boats-bar" style="display:none;">
            <div class="pinned-header">
                <span>Vergelijken (<span id="pinnedCount">0</span>/3)</span>
                <button class="btn-text btn-sm" id="clearPins" style="color:white;">Wissen</button>
            </div>
            <div class="pinned-items" id="pinnedItems"></div>
            <button class="btn btn-primary btn-sm" id="compareBtn">Vergelijk Nu</button>
        </div>

        <!-- Comparison Table HTML -->
        <div class="table-responsive">
            <table class="comparison-table" id="boatComparisonTable">
                <thead>
                    <tr>
                        <th style="min-width: 150px;">Boot</th>
                        <th>Type</th>
                        <th>Personen</th>
                        <th>Prijs (vanaf)</th>
                        <th>Borg</th>
                        <th>Kenmerken</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody id="comparisonTableBody">
                    <!-- Populated by JS -->
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Comparison Detail Modal -->
<div id="compareModal" class="boat-modal">
    <div class="boat-modal-content large">
        <button class="boat-modal-close" onclick="document.getElementById('compareModal').classList.remove('active')">&times;</button>
        <h2>Boten Vergelijken</h2>
        <div class="compare-grid-view" id="compareGrid"></div>
    </div>
</div>

<style>
.comparison-section {
    padding: 20px 0 60px;
    background: #fff;
    border-top: 1px solid #eee;
}

.comparison-filters {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 6px;
    min-width: 150px;
}

.comparison-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.comparison-table th, .comparison-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.comparison-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: var(--secondary-color);
}

.comparison-table tr:hover {
    background: #f0f7fa;
}

/* Checkbox specific */
.pin-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

/* Pinned Bar */
.pinned-boats-bar {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--secondary-color);
    color: white;
    padding: 15px 25px;
    border-radius: 50px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
    z-index: 1000;
    width: 90%;
    max-width: 600px;
    justify-content: space-between;
}

.pinned-items {
    display: flex;
    gap: 10px;
}

.pinned-thumb {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-size: cover;
    background-position: center;
    border: 2px solid white;
}

/* Modal Grid for Comparison */
.boat-modal-content.large {
    max-width: 900px;
}

.compare-grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.compare-card {
    border: 1px solid #eee;
    border-radius: 10px;
    padding: 15px;
    background: #f9f9f9;
}

.compare-card h4 {
    margin: 10px 0;
    color: var(--primary-color);
}

.compare-row {
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #eee;
    padding-bottom: 5px;
}

.compare-row strong {
    font-size: 0.9em;
    color: #666;
}
</style>

<script>
(function() {
    let pinnedBoats = [];
    const maxPins = 3;
    let allBoats = [];

    // Init with logic hook
    if (document.readyState === 'loading') { // Loading hasn't finished yet
        document.addEventListener('DOMContentLoaded', initComp);
    } else { // `DOMContentLoaded` has already fired
        initComp();
    }

    function initComp() {
         // Attempt to load data if not present (although BoatFinder likely loaded it)
         if (typeof window.BoatDataService !== 'undefined') {
             // Use service
             window.BoatDataService.getAllBoats().then(boats => {
                 allBoats = boats;
                 renderTable(allBoats);
             });
         } else {
             // Wait or check loaded var from other scripts
             setTimeout(() => {
                 if (window.boatData && window.boatData.all) {
                     allBoats = window.boatData.all;
                     renderTable(allBoats);
                 } else {
                     // Fetch via API endpoint (secure, doesn't expose file path)
                     fetch('../admin/booking-handler.php?action=boats')
                        .then(r => r.json())
                        .then(data => {
                            if (data && data.success && Array.isArray(data.boats)) {
                                allBoats = data.boats;
                                renderTable(data.boats);
                            }
                        })
                        .catch(err => console.error('Failed to load boats:', err));
                 }
             }, 500);
         }

         attachCompHandlers();
    }

    function attachCompHandlers() {
        document.getElementById('compFilterCapacity').addEventListener('change', filterTable);
        document.getElementById('compFilterType').addEventListener('change', filterTable);
        document.getElementById('clearCompFilters').addEventListener('click', () => {
            document.getElementById('compFilterCapacity').value = 'all';
            document.getElementById('compFilterType').value = 'all';
            filterTable();
        });
        
        document.getElementById('clearPins').addEventListener('click', clearPins);
        document.getElementById('compareBtn').addEventListener('click', showCompareModal);
        
        window.addEventListener('boatFinderResult', (e) => {
            highlightRecommended(e.detail.recommendedIds, e.detail.topMatchId);
        });
    }

    function renderTable(boats) {
        const tbody = document.getElementById('comparisonTableBody');
        tbody.innerHTML = '';

        boats.forEach(boat => {
            // Check filters
            if (!checkFilters(boat)) return;

            const tr = document.createElement('tr');
            tr.dataset.boatId = boat.id;
            
            // Check if pinned
            const isPinned = pinnedBoats.includes(boat.id);
            
            // Image fixing
            let imgPath = boat.image || '';
            if (document.documentElement.classList.contains('webp') && boat.imageWebp) {
                imgPath = boat.imageWebp;
            }

            tr.innerHTML = `
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:50px; height:50px; background-image:url('${imgPath}'); background-size:cover; border-radius:4px;"></div>
                        <strong>${boat.name}</strong>
                    </div>
                </td>
                <td>${translateCat(boat.category)}</td>
                <td>${boat.capacity || boat.passengerCount}</td>
                <td>€${boat.pricePerDay || boat.price}</td>
                <td>€${boat.deposit}</td>
                <td><small>${getFeatures(boat)}</small></td>
                <td>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <label class="btn btn-outline btn-sm" style="display:flex; align-items:center; gap:5px; padding: 5px 10px; cursor:pointer; font-size:0.8rem;">
                            <input type="checkbox" class="pin-checkbox" value="${boat.id}" ${isPinned ? 'checked' : ''} onchange="window.togglePin('${boat.id}', this)">
                            Vergelijk
                        </label>
                         <button class="btn btn-sm" onclick="showAvailabilityCalendarForId('${boat.id}')">📅</button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
        
        // Expose toggle globally for the onclick attribute
        window.togglePin = function(id, checkbox) {
            if (checkbox.checked) {
                if (pinnedBoats.length >= maxPins) {
                    const msg = window.getTranslation
                      ? window.getTranslation('compare_max_pins')
                      : 'Je kunt maximaal 3 boten vergelijken.';
                    alert(msg);
                    checkbox.checked = false;
                    return;
                }
                pinnedBoats.push(id);
            } else {
                pinnedBoats = pinnedBoats.filter(pid => pid !== id);
            }
            updatePinnedUI();
        }
    }
    
    function checkFilters(boat) {
        const capFilter = document.getElementById('compFilterCapacity').value;
        const typeFilter = document.getElementById('compFilterType').value;
        
        // Capacity
        if (capFilter !== 'all') {
            let cap = 0;
            // Parse capacity logic similar to finder
             const match = (boat.capacity || boat.passengerCount || '').match(/(\d+)/);
             if (match) cap = parseInt(match[0]);
             
             if (capFilter === '1-4' && cap > 4) return false;
             if (capFilter === '5-8' && (cap < 5 || cap > 8)) return false;
             if (capFilter === '9+' && cap < 9) return false;
        }
        
        // Type
        if (typeFilter !== 'all') {
            let cat = boat.category || 'electric';
            if (typeFilter === 'electric' && cat !== 'electric') return false;
            // Treat canoe/sup/kayak as 'canoe' group
            if (typeFilter === 'canoe' && !['canoe', 'kayak', 'sup'].includes(cat)) return false;
            if (typeFilter === 'sailing' && cat !== 'sailing') return false;
        }
        
        return true;
    }
    
    function filterTable() {
        renderTable(allBoats);
    }

    function updatePinnedUI() {
        const bar = document.getElementById('pinnedBoatsBar');
        const items = document.getElementById('pinnedItems');
        const count = document.getElementById('pinnedCount');
        
        if (pinnedBoats.length > 0) {
            bar.style.display = 'flex';
        } else {
            bar.style.display = 'none';
        }
        
        count.textContent = pinnedBoats.length;
        
        items.innerHTML = '';
        pinnedBoats.forEach(id => {
            const boat = allBoats.find(b => b.id === id);
            if (boat) {
                const thumb = document.createElement('div');
                thumb.className = 'pinned-thumb';
                thumb.style.backgroundImage = `url('${document.documentElement.classList.contains("webp") && boat.imageWebp ? boat.imageWebp : boat.image}')`;
                thumb.title = boat.name;
                items.appendChild(thumb);
            }
        });
    }
    
    function clearPins() {
        pinnedBoats = [];
        updatePinnedUI();
        // Uncheck all boxes
        document.querySelectorAll('.pin-checkbox').forEach(cb => cb.checked = false);
    }
    
    function showCompareModal() {
        const modal = document.getElementById('compareModal');
        const grid = document.getElementById('compareGrid');
        
        // Populate grid
        grid.innerHTML = '';
        
        pinnedBoats.forEach(id => {
            const boat = allBoats.find(b => b.id === id);
            if (!boat) return;
            
            const card = document.createElement('div');
            card.className = 'compare-card';
            const compareImg = document.documentElement.classList.contains('webp') && boat.imageWebp ? boat.imageWebp : boat.image;
            card.innerHTML = `
                 <div style="height:150px; background-image:url('${compareImg}'); background-size:cover; border-radius:6px; margin-bottom:10px;"></div>
                 <h4>${boat.name}</h4>
                 <div class="compare-row"><strong>Prijs</strong> <span>€${boat.pricePerDay}</span></div>
                 <div class="compare-row"><strong>Personen</strong> <span>${boat.capacity || boat.passengerCount}</span></div>
                 <div class="compare-row"><strong>Borg</strong> <span>€${boat.deposit}</span></div>
                 <div class="compare-row"><strong>Type</strong> <span>${translateCat(boat.category)}</span></div>
                 <div class="compare-row" style="flex-direction:column; align-items:flex-start;">
                    <strong>Beschrijving</strong>
                    <p style="font-size:0.8em; margin:5px 0;">${boat.description}</p>
                 </div>
                 <button class="btn btn-primary btn-block" style="width:100%; margin-top:10px;" onclick="document.getElementById('compareModal').classList.remove('active'); showAvailabilityCalendarForId('${boat.id}')">Kies deze</button>
            `;
            grid.appendChild(card);
        });
        
        modal.classList.add('active');
    }
    
    // Helper
    function translateCat(cat) {
        const map = {
            'electric': 'Elektrisch',
            'sailing': 'Zeilboot',
            'canoe': 'Kano',
            'sup': 'SUP'
        };
        return map[cat] || cat;
    }
    
    function highlightRecommended(ids, topId) {
        // Clear previous highlights
        document.querySelectorAll('.comparison-table tr').forEach(tr => {
            tr.style.backgroundColor = '';
            tr.style.boxShadow = '';
        });
        
        ids.forEach(id => {
            const tr = document.querySelector(`tr[data-boat-id="${id}"]`);
            if (tr) {
                if (id === topId) {
                    tr.style.backgroundColor = '#fff9c4'; // Gold tint
                    tr.style.boxShadow = 'inset 3px 0 0 gold';
                } else {
                    tr.style.backgroundColor = '#f1f8e9'; // Green tint
                }
            }
        });
        
        // Scroll to table if not visible? Maybe not, too intrusive.
    }
    
    function getFeatures(boat) {
        let f = [];
        if (boat.category === 'electric') f.push('Stil varen');
        if (boat.id.includes('720')) f.push('Luxe');
        if (boat.deposit === 0) f.push('Geen borg');
        return f.join(', ');
    }

})();
</script>
