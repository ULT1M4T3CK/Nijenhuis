<!-- Boat Finder Component -->
<div id="boatFinder" class="boat-finder-section">
    <div class="container">
        <div class="boat-finder-card">
            <div class="boat-finder-header">
                <h3>⛵ Boot Keuzehulp</h3>
                <p>Vind de perfecte boot voor je dagje uit in 3 simpele stappen</p>
            </div>

            <div class="finder-progress-bar">
                <div class="progress-track">
                    <div class="progress-fill" id="progressFill" style="width: 33%"></div>
                </div>
                <div class="progress-labels">
                    <span class="p-label active">Groep</span>
                    <span class="p-label">Type</span>
                    <span class="p-label">Ervaring</span>
                </div>
            </div>

            <div class="boat-finder-content">
                <!-- Step 1: Group Size -->
                <div class="finder-step active" data-step="1">
                    <div class="step-question">
                        <h4>Met hoeveel personen ga je varen?</h4>
                        <p class="step-subtext">Inclusief kinderen</p>
                    </div>
                    <div class="step-input-group centered">
                        <button class="qty-btn minus" type="button" aria-label="Minder personen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                        <input type="number" id="finderGroupSize" min="1" max="20" value="2" class="qty-input">
                        <button class="qty-btn plus" type="button" aria-label="Meer personen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </button>
                    </div>
                    <div class="step-actions right-align">
                        <button class="btn btn-primary next-step">Volgende ❯</button>
                    </div>
                </div>

                <!-- Step 2: Trip Type -->
                <div class="finder-step" data-step="2">
                    <div class="step-question">
                        <h4>Wat voor soort tocht wil je maken?</h4>
                    </div>
                    <div class="finder-options-grid">
                        <label class="finder-option-card">
                            <input type="radio" name="tripType" value="relaxed" checked>
                            <span class="option-icon">😌</span>
                            <span class="option-label">Relaxed varen</span>
                            <span class="option-desc">Rustig genieten van de natuur en elkaar</span>
                        </label>
                        <label class="finder-option-card">
                            <input type="radio" name="tripType" value="active">
                            <span class="option-icon">💪</span>
                            <span class="option-label">Actief & Sportief</span>
                            <span class="option-desc">Lekker peddelen en bewegen</span>
                        </label>
                        <label class="finder-option-card">
                            <input type="radio" name="tripType" value="fishing">
                            <span class="option-icon">🎣</span>
                            <span class="option-label">Vissen</span>
                            <span class="option-desc">Op zoek naar de beste visplekjes</span>
                        </label>
                        <label class="finder-option-card">
                            <input type="radio" name="tripType" value="adventure">
                            <span class="option-icon">🧭</span>
                            <span class="option-label">Avontuur</span>
                            <span class="option-desc">Zeilen en de elementen trotseren</span>
                        </label>
                    </div>
                    <div class="step-actions">
                        <button class="btn btn-text prev-step">❮ Vorige</button>
                        <!-- Next button hidden for auto-advance feel, or kept as fallback -->
                    </div>
                </div>

                <!-- Step 3: Experience -->
                <div class="finder-step" data-step="3">
                    <div class="step-question">
                        <h4>Heb je ervaring met varen?</h4>
                    </div>
                    <div class="finder-options-row">
                        <label class="finder-option-btn">
                            <input type="radio" name="experience" value="none" checked>
                            <span class="opt-content">
                                <span class="opt-title">Geen ervaring</span>
                                <span class="opt-sub">Ik wil makkelijk varen</span>
                            </span>
                        </label>
                        <label class="finder-option-btn">
                            <input type="radio" name="experience" value="some">
                            <span class="opt-content">
                                <span class="opt-title">Enige ervaring</span>
                                <span class="opt-sub">Ik heb wel eens gevaren</span>
                            </span>
                        </label>
                        <label class="finder-option-btn">
                            <input type="radio" name="experience" value="experienced">
                            <span class="opt-content">
                                <span class="opt-title">Veel ervaring</span>
                                <span class="opt-sub">Ik ben een kapitein!</span>
                            </span>
                        </label>
                    </div>
                    
                    <div class="budget-section" style="margin-top: 40px; padding-top: 30px; border-top: 1px dashed #eee;">
                         <h4>Wat is je budget (per dag)?</h4>
                         <div class="range-slider-container">
                            <span class="range-min">€20</span>
                            <input type="range" id="budgetSlider" min="20" max="300" value="300" step="10">
                            <span class="range-max">€300+</span>
                         </div>
                         <div class="budget-value-display">Max <span id="budgetDisplayProp">€300</span></div>
                    </div>

                    <div class="step-actions">
                        <button class="btn btn-text prev-step">❮ Vorige</button>
                        <button class="btn btn-primary" id="showResultsBtn">Toon Resultaten 🔍</button>
                    </div>
                </div>
            </div>
            
            <!-- Loading State -->
            <div id="finderLoading" class="finder-loading" style="display: none;">
                <div class="spinner-modern"></div>
                <p>De beste boten zoeken...</p>
            </div>

            <!-- Results Section -->
            <div class="boat-finder-results" id="finderResults" style="display:none;">
                <div class="results-header">
                    <h4>✨ Wij raden deze boten aan:</h4>
                    <button class="btn-text" id="resetFinder">↻ Opnieuw zoeken</button>
                </div>
                <div class="finder-recommendations" id="finderRecommendations"></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Boat Finder Styles */
.boat-finder-section {
    padding: 0;
    background: #f8fafc;
    margin-bottom: 2rem;
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.boat-finder-card {
    background: white;
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.08), 0 5px 15px rgba(0,0,0,0.03);
    overflow: hidden;
    max-width: 900px;
    margin: 0 auto;
    border: 1px solid rgba(0,0,0,0.03);
    position: relative;
    transition: height 0.3s ease;
}

.boat-finder-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #007bb5 100%);
    color: white;
    padding: 2.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.boat-finder-header::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IndoaXRlIiBmaWxsLW9wYWNpdHk9IjAuMSIvPjwvc3ZnPg==');
    opacity: 0.3;
}

.boat-finder-header h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.8rem;
    font-weight: 800;
    position: relative;
    z-index: 1;
}

.boat-finder-header p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

/* Progress Bar */
.finder-progress-bar {
    background: #f1f5f9;
    padding: 1.5rem 3rem 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.progress-track {
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--primary-color);
    border-radius: 3px;
    transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-labels {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.p-label.active {
    color: var(--primary-color);
}

.boat-finder-content {
    padding: 3rem;
    min-height: 480px; /* Prevent layout shift */
}

/* Steps Animation */
.finder-step {
    display: none;
    animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

.finder-step.active {
    display: block;
}

@keyframes slideUpFade {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.step-question h4 {
    font-size: 1.75rem;
    color: var(--secondary-color, #1e293b);
    margin: 0 0 10px 0;
    font-weight: 700;
}

.step-subtext {
    color: #64748b;
    font-size: 1.1rem;
}

/* Step 1: Input styling */
.step-input-group {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1.5rem;
    margin: 3rem 0;
}

.qty-btn {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: 2px solid #e2e8f0;
    background: #fff;
    cursor: pointer;
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
}

.qty-btn:hover {
    border-color: var(--primary-color);
    transform: scale(1.05);
    background: #f0f9ff;
}

.qty-input {
    width: 100px;
    text-align: center;
    font-size: 3.5rem;
    border: none;
    font-weight: 800;
    color: var(--secondary-color);
    background: transparent;
    font-family: inherit;
}

.qty-input:focus {
    outline: none;
}

/* Step 2: Modern Cards */
.finder-options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 20px;
    margin: 2rem 0;
}

.finder-option-card {
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 2rem 1.5rem;
    cursor: pointer;
    text-align: center;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
}

.finder-option-card:hover {
    border-color: #cbd5e0;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
}

.finder-option-card:has(input:checked) {
    border-color: var(--primary-color);
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(var(--primary-rgb, 37, 99, 235), 0.15);
}

.finder-option-card input {
    position: absolute;
    opacity: 0;
}

.option-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    transition: transform 0.2s;
}

.finder-option-card:hover .option-icon {
    transform: scale(1.1);
}

.option-label {
    display: block;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: var(--secondary-color);
}

.option-desc {
    font-size: 0.9rem;
    color: #64748b;
    line-height: 1.4;
}

/* Step 3: Experience Row */
.finder-options-row {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 2rem;
}

@media (min-width: 640px) {
    .finder-options-row {
        flex-direction: row;
    }
}

.finder-option-btn {
    flex: 1;
    position: relative;
}

.finder-option-btn input {
    position: absolute;
    opacity: 0;
}

.opt-content {
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
    height: 100%;
    justify-content: center;
}

.finder-option-btn:hover .opt-content {
    border-color: #cbd5e0;
    background: #f8fafc;
}

.finder-option-btn input:checked + .opt-content {
    background: #eff6ff;
    border-color: var(--primary-color);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.opt-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--secondary-color);
    margin-bottom: 0.25rem;
}

.opt-sub {
    font-size: 0.9rem;
    color: #64748b;
}

/* Budget Slider */
.range-slider-container {
    display: flex;
    align-items: center;
    gap: 1rem;
    max-width: 500px;
    margin: 1rem auto;
}

.range-min, .range-max {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

#budgetSlider {
    flex: 1;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    -webkit-appearance: none;
}

#budgetSlider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    background: var(--primary-color);
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    cursor: pointer;
    transition: transform 0.1s;
}

#budgetSlider::-webkit-slider-thumb:hover {
    transform: scale(1.1);
}

.budget-value-display {
    text-align: center;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary-color);
}

/* Actions */
.step-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3rem;
}

.step-actions.right-align {
    justify-content: flex-end;
}

.btn-text {
    background: none;
    border: none;
    color: #64748b;
    font-weight: 600;
    cursor: pointer;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    transition: color 0.2s;
}

.btn-text:hover {
    color: var(--secondary-color);
}

/* Spinner */
.finder-loading {
    padding: 5rem 2rem;
    text-align: center;
    animation: fadeIn 0.3s;
}

.spinner-modern {
    width: 50px;
    height: 50px;
    border: 4px solid #e2e8f0;
    border-top-color: var(--primary-color);
    border-radius: 50%;
    margin: 0 auto 1.5rem;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Results */
.boat-finder-results {
    padding: 3rem;
    background: #fafbfc;
}

.rec-boat-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    border: 1px solid #f1f5f9;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
}

.rec-boat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.08);
}

.rec-boat-card.top-match {
    border: 2px solid #fbbf24;
    position: relative;
}

.rec-boat-card.top-match::before {
    content: '★ Beste Match';
    position: absolute;
    top: 12px;
    left: 12px;
    background: #fbbf24;
    color: #fff;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 10;
}

.rec-boat-img {
    height: 220px;
    background-size: cover;
    background-position: center;
    position: relative;
}

.rec-boat-img::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 60px;
    background: linear-gradient(to top, rgba(0,0,0,0.4), transparent);
}

.rec-boat-body {
    padding: 1.5rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.rec-boat-title {
    font-size: 1.25rem;
    margin: 0 0 0.5rem;
    color: var(--secondary-color);
}

.rec-match-score {
    display: inline-block;
    color: #10b981;
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    background: #d1fae5;
    padding: 2px 8px;
    border-radius: 6px;
    width: fit-content;
}

.rec-boat-specs {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    color: #64748b;
    font-size: 0.9rem;
}

.specs-item {
    display: flex;
    align-items: center;
    gap: 4px;
}

.rec-boat-price-row {
    margin-top: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f1f5f9;
    padding-top: 1rem;
}

.rec-price {
    font-weight: 800;
    font-size: 1.2rem;
    color: var(--primary-color);
}

.rec-price small {
    font-weight: 400;
    font-size: 0.8rem;
    color: #94a3b8;
}

.rec-boat-btn {
    background: var(--primary-color);
    color: white;
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.95rem;
    transition: background 0.2s;
}

.rec-boat-btn:hover {
    background: #004494; /* Darker Blue */
}

@media (max-width: 640px) {
    .boat-finder-header {
        padding: 2rem 1.5rem;
    }
    
    .boat-finder-content {
        padding: 1.5rem;
    }
    
    .step-input-group {
        flex-wrap: wrap;
    }
    
    .finder-options-grid {
        grid-template-columns: 1fr;
    }
    
    .finder-progress-bar {
        padding: 1rem 1.5rem 0;
    }
}
</style>

<script>
(function() {
    // Defines extra metadata not in existing boats.json
    const boatTags = {
        'classic-tender-720': { comfort: 10, speed: 'slow', type: ['relaxed', 'group', 'family'], minDiff: 1 },
        'classic-tender-570': { comfort: 9, speed: 'slow', type: ['relaxed', 'group', 'family'], minDiff: 1 },
        'electrosloop-10': { comfort: 8, speed: 'slow', type: ['relaxed', 'group', 'family'], minDiff: 1 },
        'electrosloop-8': { comfort: 8, speed: 'slow', type: ['relaxed', 'group', 'family'], minDiff: 1 },
        'electroboat-5': { comfort: 7, speed: 'slow', type: ['relaxed', 'small-group'], minDiff: 1 },
        'sailboat-4-5': { comfort: 6, speed: 'varies', type: ['active', 'adventure', 'nature'], minDiff: 3 }, 
        'sailpunter-3-4': { comfort: 5, speed: 'varies', type: ['active', 'adventure', 'nature'], minDiff: 4 }, 
        // Canoe is intentionally not marked as "fishing" (not suitable for fishing in this helper)
        'canoe-3': { comfort: 4, speed: 'active', type: ['active', 'sport', 'nature'], minDiff: 1 },
        'kayak-2': { comfort: 4, speed: 'active', type: ['active', 'sport', 'nature'], minDiff: 1 },
        'kayak-1': { comfort: 3, speed: 'active', type: ['active', 'sport', 'nature'], minDiff: 1 },
        'sup-board': { comfort: 2, speed: 'active', type: ['active', 'sport'], minDiff: 2 } 
    };

    const state = {
        groupSize: 2,
        tripType: 'relaxed',
        experience: 'none',
        budget: 300,
        currentStep: 1
    };

    let allBoats = [];

    // DOM Elements
    const container = document.getElementById('boatFinder');
    const inputGroupSize = document.getElementById('finderGroupSize');
    const sliderBudget = document.getElementById('budgetSlider');
    const displayBudget = document.getElementById('budgetDisplayProp');
    const progressFill = document.getElementById('progressFill');
    const progressLabels = container.querySelectorAll('.p-label');

    // Init
    init();

    function init() {
        attachEventHandlers();
        loadBoatData();
        updateProgress();
    }

    async function loadBoatData() {
        try {
            if (window.BoatDataService) {
                allBoats = await window.BoatDataService.getAllBoats();
            } else {
                // Use booking-handler API (boats.json is blocked by admin/.htaccess)
                const endpoint = `${window.location.origin}/admin/booking-handler.php?action=boats`;
                const response = await fetch(endpoint);
                const data = await response.json();
                allBoats = (data && data.success && data.boats) ? data.boats : [];
            }
        } catch (e) {
            console.error('Cannot load boat data for finder', e);
        }
    }

    function attachEventHandlers() {
        // Step Navigation
        container.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', () => changeStep(state.currentStep + 1));
        });
        
        container.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', () => changeStep(state.currentStep - 1));
        });

        // Group Size
        inputGroupSize.addEventListener('change', (e) => state.groupSize = parseInt(e.target.value));
        container.querySelector('.qty-btn.minus').addEventListener('click', () => {
             if (state.groupSize > 1) {
                 state.groupSize--;
                 inputGroupSize.value = state.groupSize;
             }
        });
        container.querySelector('.qty-btn.plus').addEventListener('click', () => {
             if (state.groupSize < 20) {
                 state.groupSize++;
                 inputGroupSize.value = state.groupSize;
             }
        });

        // Radio Inputs with Auto-Advance using Card Clicks (fixes pre-selection bug)
        container.querySelectorAll('.finder-option-card').forEach(card => {
            card.addEventListener('click', (e) => {
                // Find input inside
                const input = card.querySelector('input[type="radio"]');
                if (input) {
                    // Update state
                    const val = input.value;
                    state.tripType = val;
                    
                    // Manually check it (if click was on card padding)
                    input.checked = true;
                    
                    // Auto advance
                    setTimeout(() => changeStep(3), 300);
                }
            });
        });
        
        // Experience Inputs - No auto advance because there is budget slider below?
        // Actually, Step 3 has budget too, so stay here or advance to results?
        // Step 3 layout: Experience then Budget then "Show Results". 
        // So no auto advance on experience click.
        container.querySelectorAll('input[name="experience"]').forEach(r => {
            r.addEventListener('change', (e) => state.experience = e.target.value);
        });

        // Budget
        sliderBudget.addEventListener('input', (e) => {
            state.budget = parseInt(e.target.value);
            displayBudget.textContent = state.budget >= 300 ? '€300+' : '€' + state.budget;
        });

        // Results
        document.getElementById('showResultsBtn').addEventListener('click', showLoadingAndCalculate);
        document.getElementById('resetFinder').addEventListener('click', resetFinder);
    }

    function changeStep(step) {
        if (state.currentStep === 1 && step === 2) {
             state.groupSize = parseInt(inputGroupSize.value);
             if (!state.groupSize || state.groupSize < 1) return;
        }

        // Hide current
        container.querySelector(`.finder-step[data-step="${state.currentStep}"]`).classList.remove('active');
        
        // Show new
        state.currentStep = step;
        const nextStepEl = container.querySelector(`.finder-step[data-step="${state.currentStep}"]`);
        if (nextStepEl) {
            nextStepEl.classList.add('active');
        }

        updateProgress();
    }

    function updateProgress() {
        // Steps: 1, 2, 3
        const percent = ((state.currentStep - 0.5) / 3) * 100;
        progressFill.style.width = `${percent}%`;
        
        progressLabels.forEach((label, idx) => {
            if (idx + 1 <= state.currentStep) label.classList.add('active');
            else label.classList.remove('active');
        });
    }

    function showLoadingAndCalculate() {
        // Hide content
        container.querySelector('.boat-finder-content').style.display = 'none';
        
        // Show loading
        const loader = document.getElementById('finderLoading');
        loader.style.display = 'block';

        // Fake calculation delay
        setTimeout(() => {
            loader.style.display = 'none';
            document.getElementById('finderResults').style.display = 'block';
            calculateResults();
        }, 800);
    }

    function calculateResults() {
        const scoredBoats = allBoats.map(boat => {
            const tags = boatTags[boat.id] || { comfort: 5, type: [], minDiff: 1 };
            let score = 0;
            let isExcluded = false;

            // Capacity parsing
            let capacity = 0;
            if (boat.capacity) {
                if (typeof boat.capacity === 'number') capacity = boat.capacity;
                else {
                    const matches = boat.capacity.match(/(\d+)/);
                    if (matches) capacity = parseInt(matches[0]);
                }
            } else if (boat.passengerCount) {
                 const matches = boat.passengerCount.match(/(\d+)/);
                 if (matches) capacity = parseInt(matches[0]);
            }
            
            // Logic: MUST fit group size.
            // AND preferably not be too huge (e.g. 10p boat for 2 people is weird, but acceptable if nothing else).
            // User Request: "show matching + up to 4 higher". 
            // Interpret as: Include boats where capacity >= groupSize AND capacity <= groupSize + 4.
            // HOWEVER: If the group is 2, groupSize+4=6. Electrosloop 8 is excluded. 
            // Maybe this is too strict if availability is low? user said "It should show...".
            // Let's implement this window as the primary filter.
            // If NO boats match this window, we might need a fallback? 
            // But let's stick to the request: "show the matching + up to 4 higher".
            
            // For now, strict filter based on user request.
            if (capacity < state.groupSize) isExcluded = true;
            
            // Upper bound filter (optional per user request "show... up to 4 higher")
            // We'll apply a heavy penalty or soft exclusion for > +4.
            // Actually, user said "It should show matching + up to 4 higher". 
            // This implies: Don't show if > +4.
            if (capacity > state.groupSize + 4) isExcluded = true;

            // Budget Hard Filter
            if (boat.pricePerDay > state.budget) isExcluded = true;

            // Experience Filter
            let userSkill = 1;
            if (state.experience === 'some') userSkill = 3;
            if (state.experience === 'experienced') userSkill = 5;

            if (tags.minDiff > userSkill) {
                // If sailboat requires engine-free handling but user has no skill, exclude or downrank?
                if (boat.id.includes('sailboat') && tags.minDiff > userSkill) {
                     // Exclude pure sailing if no experience
                     isExcluded = true; 
                } else if (tags.minDiff > userSkill) { 
                     isExcluded = true; 
                }
            }

            // Business rule: paddle craft are not suitable options for fishing
            if (state.tripType === 'fishing' && ['canoe-3', 'kayak-2', 'kayak-1', 'sup-board'].includes(boat.id)) {
                isExcluded = true;
            }

            // Business rule: when sporty/active, show sailboat and zeilpunter instead of Electroboot 5 pers
            if (state.tripType === 'active' && boat.id === 'electroboat-5') {
                isExcluded = true;
            }

            // Scoring for Match %
            // Max potential score calc:
            // Base: 100
            // Trip Type: 40 (max possible)
            // Comfort: 10 * 2 = 20
            // Speed Match: 10
            // Total Max = 170 approx.
            
            if (!isExcluded) {
                score += 100; // Base
                
                // Trip Type Match
                if (state.tripType === 'relaxed') {
                    if (tags.type.includes('relaxed')) score += 20;
                    score += tags.comfort * 3; // Focus on comfort
                }
                if (state.tripType === 'active') {
                    if (tags.type.includes('active') || tags.type.includes('sport')) score += 30;
                    if (tags.speed === 'active') score += 15;
                    // Lower score for lazy boats
                    if (tags.type.includes('relaxed')) score -= 10;
                }
                if (state.tripType === 'fishing') {
                    if (tags.type.includes('fishing')) score += 40;
                    if (tags.type.includes('relaxed')) score += 15; // Stability
                }
                if (state.tripType === 'adventure') {
                    if (tags.type.includes('adventure')) score += 40;
                    if (tags.speed === 'varies') score += 10;
                }
                
                // Capacity Match Bonus: Closer is better
                const diff = capacity - state.groupSize;
                // diff is between 0 and 4 (guaranteed by filter)
                // 0 diff (exact match) = +20 bonus?
                // 4 diff = +0?
                score += (4 - diff) * 5; 
            }

            return { ...boat, score, isExcluded, capacity };
        });

        // Calculate Max Possible Score for this Trip Type?
        // Or generic max = 170.
        // Let's use 180 as a safe ceiling for "100%".
        const MAX_SCORE = 180;

        const recommendations = scoredBoats
            .filter(b => !b.isExcluded)
            .sort((a, b) => b.score - a.score);
            // .slice(0, 3); // User said "show all that match". Removed slice.

        const containerRec = document.getElementById('finderRecommendations');
        containerRec.innerHTML = '';

        if (recommendations.length === 0) {
            containerRec.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 2rem;">Geen boten gevonden die aan alle criteria voldoen. <br><small>Tip: Vergroot je groep of budget om meer opties te zien.</small> <br><br> <button class="btn-text" onclick="document.getElementById(\'resetFinder\').click()">Filters aanpassen</button></div>';
            return;
        }

        recommendations.forEach((boat, index) => {
            const isTop = index === 0;
            let imgPath = boat.image || '';
            if (document.documentElement.classList.contains('webp') && boat.imageWebp) {
                imgPath = boat.imageWebp;
            }
            
            // Accurate Percentage Calculation
            let percent = Math.round((boat.score / MAX_SCORE) * 100);
            if (percent > 99) percent = 99; // Cap at 99
            if (percent < 60) percent = 60; // Floor at 60 for "matching" boats

            // Color coding based on percent
            let matchColor = '#10b981'; // Green
            if (percent < 80) matchColor = '#f59e0b'; // Orange
            
            const el = document.createElement('div');
            el.className = `rec-boat-card ${isTop ? 'top-match' : ''}`;
            el.innerHTML = `
                <div class="rec-boat-img" style="background-image: url('${imgPath}')"></div>
                <div class="rec-boat-body">
                    <h4 class="rec-boat-title">${boat.name}</h4>
                    <div class="rec-match-score" style="color:${matchColor}; background:${matchColor}20;">${percent}% Match</div>
                    <div class="rec-boat-specs">
                        <span class="specs-item">👥 ${boat.capacity} Pers.</span>
                        <span class="specs-item">ℹ️ ${boat.category || 'Boot'}</span>
                    </div>
                    <div class="rec-boat-price-row">
                        <div class="rec-price">€${boat.pricePerDay} <small>/dag</small></div>
                        <a href="/${boat.id}#booking" class="rec-boat-btn">Bekijken</a>
                    </div>
                </div>
            `;
            containerRec.appendChild(el);
        });
    }

    function resetFinder() {
        document.getElementById('finderResults').style.display = 'none';
        container.querySelector('.boat-finder-content').style.display = 'block';
        
        state.currentStep = 1;
        state.groupSize = 2;
        inputGroupSize.value = 2;
        
        container.querySelectorAll('.finder-step').forEach(s => s.classList.remove('active'));
        container.querySelector('.finder-step[data-step="1"]').classList.add('active');
        updateProgress();
    }

})();
</script>
