/**
 * ========================================================================
 * BOAT DATA SERVICE - SINGLE SOURCE OF TRUTH
 * ========================================================================
 * 
 * This service provides centralized access to boat data across the entire website.
 * The authoritative source is the admin/boats.json file (via server) or localStorage.
 * 
 * All boat data changes made in boat-management.html automatically propagate to:
 * - All pages where boats are referenced
 * - The booking system (availability, pricing, metadata)
 * - Boat detail modals and UI components
 * 
 * USAGE (from other pages):
 *   const boats = await BoatDataService.getAllBoats();
 *   const boat = await BoatDataService.getBoatById('classic-tender-720');
 *   BoatDataService.subscribe((boats) => { console.log('Updated:', boats); });
 * 
 * ========================================================================
 */
class BoatDataServiceClass {
    constructor() {
        this.storageKey = 'nijenhuis_boats';
        this.cache = null;
        this.cacheTime = 0;
        this.cacheDuration = 100; // 100ms cache for near-instant updates
        this.subscribers = [];
        this.initialized = false;
    }

    init() {
        if (this.initialized) return;
        this.initialized = true;

        // Listen for storage changes from other tabs
        window.addEventListener('storage', (e) => {
            if (e.key === this.storageKey) {
                this.cache = null;
                this.notifySubscribers();
            }
        });

        // Listen for custom events from boat-management.html
        window.addEventListener('boatsUpdated', () => {
            this.cache = null;
            this.notifySubscribers();
        });

        window.addEventListener('boatsStorageUpdated', () => {
            this.cache = null;
            this.notifySubscribers();
        });
    }

    /**
     * Detect the correct server endpoint based on the current environment
     */
    detectServerEndpoint() {
        // Only use Python backend when opened directly as file://
        // When served via any web server (including localhost), use PHP
        if (window.location.protocol === 'file:' || window.location.hostname === '') {
            return 'http://localhost:8000/admin/booking-handler.py';
        }
        return `${window.location.origin}/admin/booking-handler.php`;
    }

    /**
     * Load boats from server or localStorage
     */
    async loadBoats(forceRefresh = false) {
        const now = Date.now();
        
        // Return cached data if still valid
        if (!forceRefresh && this.cache && (now - this.cacheTime) < this.cacheDuration) {
            return this.cache;
        }

        let boats = [];

        // Try localStorage first for quick response
        try {
            const stored = localStorage.getItem(this.storageKey);
            if (stored) {
                boats = JSON.parse(stored);
                if (boats.length > 0) {
                    this.cache = boats;
                    this.cacheTime = now;
                }
            }
        } catch (e) {
            console.error('Error loading boats from localStorage:', e);
        }

        // Then try server for authoritative data (if available)
        try {
            const serverBoats = await this.fetchBoatsFromServer();
            if (serverBoats.length > 0) {
                boats = serverBoats;
                // Update localStorage with server data
                localStorage.setItem(this.storageKey, JSON.stringify(boats));
                this.cache = boats;
                this.cacheTime = now;
            }
        } catch (e) {
            console.warn('Server fetch failed, using localStorage data:', e.message);
            // Continue with localStorage data
        }

        // If still no boats, use default boats
        if (!boats.length) {
            console.warn('No boats found, using defaults');
            boats = this.getDefaultBoats();
            localStorage.setItem(this.storageKey, JSON.stringify(boats));
        }

        this.cache = boats;
        this.cacheTime = now;
        return boats;
    }

    /**
     * Fetch boats from the server
     */
    async fetchBoatsFromServer() {
        const endpoint = this.detectServerEndpoint();

        try {
            // Only treat as truly "local" (no credentials) for file:// protocol
            const isFileProtocol = window.location.protocol === 'file:' || window.location.hostname === '';

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000); // 3 second timeout

            const res = await fetch(`${endpoint}?action=boats`, {
                credentials: isFileProtocol ? 'omit' : 'include',
                mode: 'cors',
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            });
            
            clearTimeout(timeoutId);

            if (res.ok) {
                const data = await res.json();
                if (data && data.success && Array.isArray(data.boats)) {
                    return data.boats;
                }
            }
        } catch (e) {
            if (e.name !== 'AbortError') {
                console.warn('Server fetch failed:', e.message);
            }
        }
        return [];
    }

    /**
     * Get all boats
     */
    async getAllBoats(forceRefresh = false) {
        this.init();
        return await this.loadBoats(forceRefresh);
    }

    /**
     * Get a specific boat by ID
     */
    async getBoatById(boatId) {
        const boats = await this.loadBoats();
        return boats.find(b => b.id === boatId) || null;
    }

    /**
     * Get boats by category
     */
    async getBoatsByCategory(category) {
        const boats = await this.loadBoats();
        return boats.filter(b => b.category === category);
    }

    /**
     * Get boat display name (with engine option for sailboat)
     */
    async getBoatDisplayName(boatId, engineOption = null) {
        const boat = await this.getBoatById(boatId);
        if (!boat) return boatId;
        
        let name = boat.name;
        if (boatId === 'sailboat-4-5' && engineOption) {
            name += engineOption === 'with' ? ' (met motor)' : ' (zonder motor)';
        }
        return name;
    }

    /**
     * Calculate price for a boat for a given number of days
     */
    async getBoatPrice(boatId, days = 1) {
        const boat = await this.getBoatById(boatId);
        if (!boat) return 0;
        
        // Multi-day pricing: pricing object keys are (days - 1)
        // pricing["1"] = 2 days, pricing["2"] = 3 days, etc.
        if (days === 1) {
            return Number(boat.pricePerDay || 0);
        }
        
        if (days >= 2 && days <= 7 && boat.pricing && typeof boat.pricing === 'object') {
            const pricingKey = String(days - 1);
            if (boat.pricing[pricingKey] !== undefined && boat.pricing[pricingKey] !== null) {
                return Number(boat.pricing[pricingKey]);
            }
        }
        
        // Fallback: multiply by pricePerDay
        return (Number(boat.pricePerDay) || 0) * days;
    }

    /**
     * Check boat availability for a given date
     */
    async checkBoatAvailability(boatId, date) {
        const boat = await this.getBoatById(boatId);
        if (!boat) return { available: false, count: 0, total: 0 };
        
        // Basic availability check (doesn't account for bookings)
        return {
            available: boat.available > 0,
            count: boat.available || 0,
            total: boat.total ?? 0
        };
    }

    /**
     * Subscribe to boat data changes
     */
    subscribe(callback) {
        if (typeof callback !== 'function') {
            throw new Error('Callback must be a function');
        }
        
        this.init();
        this.subscribers.push(callback);
        
        // Immediately call with current data
        this.loadBoats().then(boats => callback(boats));
        
        // Return unsubscribe function
        return () => {
            const index = this.subscribers.indexOf(callback);
            if (index > -1) this.subscribers.splice(index, 1);
        };
    }

    /**
     * Notify all subscribers of data changes
     */
    async notifySubscribers() {
        // Force refresh and bypass cache for instant updates
        this.cache = null;
        this.cacheTime = 0;
        const boats = await this.loadBoats(true);
        this.subscribers.forEach(callback => {
            try {
                callback(boats);
            } catch (e) {
                console.error('Error in subscriber:', e);
            }
        });
    }

    /**
     * Get default boats (fallback when no data is available)
     */
    getDefaultBoats() {
        return [
            {
                id: 'classic-tender-720',
                name: 'Classic Tender 720 10/12 pers',
                category: 'electric',
                total: 2,
                available: 2,
                pricePerDay: 230,
                deposit: 100,
                passengerCount: '10 tot 12 personen',
                description: 'De Classic Tender 720 is een robuuste aluminium sloep met ruime zitplaatsen voor 10 tot 12 personen. Dankzij de elektrische motor vaart u geruisloos en milieuvriendelijk door het natuurgebied - ideaal voor een gezellige dag met familie, vrienden of een bedrijfsuitje. Comfortabele kussens nodigen uit om lang te tafelen en te genieten van het uitzicht.\n\nHuisdieren zijn aan boord niet toegestaan.\n\nLet op: met deze boot mag u niet door Giethoorn heen varen.',
                pricing: { "1": 410, "2": 510, "3": 570, "4": 640, "5": 730, "6": 800 },
                orderId: 1
            },
            {
                id: 'classic-tender-570',
                name: 'Classic Tender 570 8 pers',
                category: 'electric',
                total: 2,
                available: 2,
                pricePerDay: 200,
                deposit: 100,
                passengerCount: '8 personen',
                description: 'De Classic Tender 570 biedt plaats aan acht personen en combineert een strakke, moderne uitstraling met het gemak van een elektrische motor. Stil varen, geen uitlaatgassen en volop ruimte om bij elkaar te zitten: perfect voor gezinnen en vriendengroepen die ontspannen het water op willen. De boot is comfortabel ingericht en geschikt voor langere tochten langs riet, weilanden en dorpen.\n\nHuisdieren zijn aan boord niet toegestaan.',
                pricing: { "1": 350, "2": 420, "3": 490, "4": 560, "5": 630, "6": 700 },
                orderId: 2
            },
            {
                id: 'electrosloop-10',
                name: 'Electrosloep 10 pers',
                category: 'electric',
                total: 1,
                available: 1,
                pricePerDay: 200,
                deposit: 100,
                passengerCount: '10 personen',
                description: 'Onze electrosloep voor tien personen is de meest ruime keuze voor grotere gezelschappen. De elektrische aandrijving maakt varen eenvoudig en aangenaam: geen gerommel met een benzinemotor, wel voldoende dekruimte om te eten, drinken en te kletsen. Ideaal voor verjaardagen, familiedagen of een teamuitje waarbij iedereen comfortabel mee kan.\n\nHuisdieren zijn welkom aan boord.',
                pricing: { "1": 310, "2": 390, "3": 470, "4": 550, "5": 630, "6": 700 },
                orderId: 3
            },
            {
                id: 'electrosloop-8',
                name: 'Electrosloep 8 pers',
                category: 'electric',
                total: 2,
                available: 2,
                pricePerDay: 175,
                deposit: 100,
                passengerCount: '8 personen',
                description: 'De electrosloep voor acht personen slaat een mooie balans tussen ruimte en wendbaarheid. Met elektrische motor stuurt u rustig door smalle vaarten en bredere plassen - fijn met kinderen of oudere passagiers aan boord. Ruim zitcomfort en een stabiele gang maken deze boot bij uitstek geschikt voor een ontspannen familiedag of een dagje uit met vrienden.\n\nHuisdieren zijn welkom aan boord.',
                pricing: { "1": 280, "2": 345, "3": 410, "4": 475, "5": 540, "6": 600 },
                orderId: 4
            },
            {
                id: 'electroboat-5',
                name: 'Electroboot 5 pers',
                category: 'electric',
                total: 3,
                available: 3,
                pricePerDay: 80,
                deposit: 0,
                passengerCount: '5 personen',
                description: 'De compacte electroboot voor vijf personen is ideaal wie zonder vaarbewijs of grote bootervaring toch het water op wil. Hij is wendbaar, overzichtelijk en stil door de elektromotor - perfect voor een stel met kinderen of een klein gezelschap dat rustig wil genieten van natuur en water. Een fijne instap naar sloepvaren zonder direct de grootste boot te hoeven huren.',
                pricing: { "1": 140, "2": 175, "3": 210, "4": 245, "5": 280, "6": 310 },
                orderId: 5
            },
            {
                id: 'sailboat-4-5',
                name: 'Zeilboot 4/5 pers',
                category: 'sailing',
                total: 1,
                available: 1,
                pricePerDay: 70,
                deposit: 50,
                passengerCount: '4 tot 5 personen',
                description: 'Vaar klassiek met onze traditionele zeilboot voor vier tot vijf personen. Zonder hulpmotor geniet u puur van wind en zeil - geschikt voor zeilers die hun vaardigheden kennen. Met optionele motor heeft u extra zekerheid bij weinig wind of in smalle vaarten, zodat u flexibel blijft in route en tijd. Een authentieke manier om het water te beleven, ver weg van massatoerisme.\n\nHuisdieren zijn aan boord niet toegestaan.',
                pricing: { "1": 130, "2": 160, "3": 190, "4": 220, "5": 250, "6": 280 },
                orderId: 6
            },
            {
                id: 'sailpunter-3-4',
                name: 'Zeilpunter 3/4 pers',
                category: 'sailing',
                total: 1,
                available: 1,
                pricePerDay: 40,
                deposit: 0,
                passengerCount: '3 tot 4 personen',
                description: 'De zeilpunter voor drie tot vier personen is een echte klassieker: smal, wendbaar en vol traditie. Dit type boot vraagt zeilervaring; beloning is een intieme, rustige vaart waarbij u het ritme van wind en water voelt. Perfect voor liefhebbers van authentiek zeilen in een weids natuurgebied.',
                pricing: { "1": 40, "2": 40, "3": 40, "4": 40, "5": 40, "6": 40 },
                orderId: 7
            },
            {
                id: 'canoe-3',
                name: 'Canadese kano 3 pers',
                category: 'canoe',
                total: 3,
                available: 3,
                pricePerDay: 25,
                deposit: 0,
                passengerCount: '3 personen',
                description: 'Met de Canadese kano voor drie personen peddelt u zelfstandig door kreken, vaarten en rustige stukken open water. De brede romp zorgt voor stabiliteit - handig met kinderen of als u spullen meeneemt voor een picknick. Ideaal voor wie actief bezig wil zijn en dicht bij het water en de natuur wil komen, ver van de motorboot.',
                pricing: { "1": 40, "2": 55, "3": 70, "4": 85, "5": 100, "6": 115 },
                orderId: 8
            },
            {
                id: 'kayak-2',
                name: 'Kajak 2 pers',
                category: 'canoe',
                total: 5,
                available: 5,
                pricePerDay: 25,
                deposit: 0,
                passengerCount: '2 personen',
                description: 'De tweepersoonskajak is gebouwd voor samen peddelen: voorin en achterin in hetzelfde tempo het water op. Lekker sportief, wendbaar en geschikt voor langere tochten of kortere rondjes. Een leuke optie voor stellen, vrienden of ouder met kind dat al wat paddle-ervaring heeft.',
                pricing: { "1": 40, "2": 55, "3": 70, "4": 85, "5": 100, "6": 115 },
                orderId: 9
            },
            {
                id: 'kayak-1',
                name: 'Kajak 1 pers',
                category: 'canoe',
                total: 5,
                available: 5,
                pricePerDay: 20,
                deposit: 0,
                passengerCount: '1 persoon',
                description: 'De eenpersoonskajak geeft u volledige vrijheid: eigen tempo, eigen route. Licht en wendbaar, ideaal om even helemaal op te gaan in het peddelen en de omgeving. Geschikt voor wie zelfstandig wil varen en van een actieve dag op het water houdt.',
                pricing: { "1": 20, "2": 20, "3": 20, "4": 20, "5": 20, "6": 20 },
                orderId: 10
            },
            {
                id: 'sup-board',
                name: 'SUP board 1 pers',
                category: 'sup',
                total: 5,
                available: 5,
                pricePerDay: 35,
                deposit: 0,
                passengerCount: '1 persoon',
                description: 'Op het SUP-board staat u rechtop op het water en peddelt u met een lange peddel - een mix van balans, ontspanning en een beetje workout. Vanaf het board ziet u het landschap en het water op een andere manier dan vanuit een boot. Geschikt voor wie een beetje waterervaring heeft en een rustige, bijzondere beleving zoekt.',
                pricing: { "1": 55, "2": 75, "3": 95, "4": 115, "5": 135, "6": 150 },
                orderId: 11
            }
        ];
    }
}

// Create global singleton instance
window.BoatDataService = new BoatDataServiceClass();

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.BoatDataService.init();
    });
} else {
    window.BoatDataService.init();
}

// Export for module usage if supported
if (typeof module !== 'undefined' && module.exports) {
    module.exports = window.BoatDataService;
}
