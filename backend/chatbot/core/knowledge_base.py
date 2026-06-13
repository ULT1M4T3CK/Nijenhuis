#!/usr/bin/env python3
"""
Comprehensive Knowledge Base for Nijenhuis Chatbot
Provides accurate, data-driven responses with 95%+ accuracy
"""

import json
import os
import re
from typing import Dict, Any, Optional, List, Tuple
from dataclasses import dataclass
from enum import Enum


class Intent(Enum):
    """User intent categories"""
    GREETING = "greeting"
    PRICING = "pricing"
    PRICING_MULTIDAY = "pricing_multiday"
    BOAT_INFO = "boat_info"
    BOAT_CAPACITY = "boat_capacity"
    OPENING_HOURS = "opening_hours"
    LOCATION = "location"
    CONTACT = "contact"
    BOOKING = "booking"
    AVAILABILITY = "availability"
    VAKANTIEHUIS = "vakantiehuis"
    CAMPING = "camping"
    VAARKAART = "vaarkaart"
    DEPOSIT = "deposit"
    PETS = "pets"
    GIETHOORN = "giethoorn"
    GOODBYE = "goodbye"
    THANKS = "thanks"
    UNKNOWN = "unknown"


@dataclass
class BoatInfo:
    """Structured boat information"""
    id: str
    name: str
    category: str
    capacity: str
    price_per_day: int
    deposit: int
    description: str
    pricing: List[int]  # Multi-day pricing [1 day, 2 days, ...]
    pricing_with_engine: Optional[List[int]]


class KnowledgeBase:
    """
    Comprehensive knowledge base with all business information
    Loads actual data from boats.json for accurate pricing
    OPTIMIZED: Includes response caching for sub-100ms repeated queries
    """
    
    def __init__(self, cache_size: int = 500):
        self.boats = self._load_boats()
        self.business_info = self._load_business_info()
        self.intent_keywords = self._build_intent_keywords()
        self.trained_responses = self._load_trained_responses()
        
        # PERFORMANCE: LRU cache for query responses
        self._response_cache = {}
        self._cache_order = []  # Track insertion order for LRU
        self._cache_size = cache_size
    
    def _load_trained_responses(self) -> Dict[str, str]:
        """Load trained responses from all training data sources"""
        trained = {}
        
        # Load from enhanced training data (multiple possible formats)
        training_file = os.path.join(
            os.path.dirname(__file__), '..', 'training', 'data', 'enhanced_training_data.json'
        )
        
        try:
            with open(training_file, 'r', encoding='utf-8') as f:
                data = json.load(f)
                
                # Format 1: training_sessions array
                for session in data.get('training_sessions', []):
                    if session.get('corrected_response'):
                        query = session.get('question', '').lower().strip()
                        if query:
                            trained[query] = session['corrected_response']
                
                # Format 2: training_data.improved_responses dict
                improved = data.get('training_data', {}).get('improved_responses', {})
                for key, value in improved.items():
                    if isinstance(value, dict):
                        query = value.get('original', '').lower().strip()
                        response = value.get('corrected', '')
                        if query and response:
                            trained[query] = response
                
                print(f"✅ Loaded {len(trained)} trained responses from enhanced data")
        except Exception as e:
            print(f"⚠️ Could not load enhanced training data: {e}")
        
        
        return trained
    
    def reload_trained_responses(self):
        """Reload trained responses (call after new training data is added)"""
        self.trained_responses = self._load_trained_responses()
        # Clear cache to use new responses
        self._response_cache = {}
        self._cache_order = []
    
    def find_trained_response(self, query: str) -> Optional[str]:
        """Check if there's a trained response for this query"""
        query_lower = query.lower().strip()
        
        # Exact match
        if query_lower in self.trained_responses:
            return self.trained_responses[query_lower]
        
        # Fuzzy match - check for similar queries
        for trained_query, response in self.trained_responses.items():
            # Check if queries are very similar (>80% word overlap)
            query_words = set(query_lower.split())
            trained_words = set(trained_query.split())
            
            if len(query_words) > 0 and len(trained_words) > 0:
                overlap = len(query_words & trained_words)
                similarity = overlap / max(len(query_words), len(trained_words))
                
                if similarity > 0.8:
                    return response
        
        return None
    
    def _load_boats(self) -> Dict[str, BoatInfo]:
        """Load boat data from boats.json"""
        boats = {}
        root = os.path.join(os.path.dirname(__file__), '..', '..', '..')
        boats_file = os.path.join(root, 'data', 'boats.json')
        legacy = os.path.join(root, 'admin', 'boats.json')
        if not os.path.isfile(boats_file) and os.path.isfile(legacy):
            boats_file = legacy

        try:
            with open(boats_file, 'r', encoding='utf-8') as f:
                data = json.load(f)
                for boat in data:
                    boat_id = boat.get('id', '')
                    boats[boat_id] = BoatInfo(
                        id=boat_id,
                        name=boat.get('name', ''),
                        category=boat.get('category', ''),
                        capacity=boat.get('passengerCount', ''),
                        price_per_day=boat.get('pricePerDay', 0),
                        deposit=boat.get('deposit', 0),
                        description=boat.get('description', ''),
                        pricing=boat.get('pricing', []),
                        pricing_with_engine=boat.get('pricingWithEngine')
                    )
                print(f"✅ Knowledge base loaded {len(boats)} boats")
        except Exception as e:
            print(f"⚠️ Could not load boats.json: {e}")
        
        return boats
    
    def _load_business_info(self) -> Dict[str, Any]:
        """Load business information"""
        return {
            'name': 'Nijenhuis Botenverhuur',
            'tagline': 'Camping & Botenverhuur',
            'phone': '0522 281 528',
            'address': 'Veneweg 199',
            'postal': '7946 LP Wanneperveen',
            'country': 'Nederland',
            'location_area': 'Weerribben-Wieden natuurgebied, bij Giethoorn',
            'kvk': '6769 7097',
            'btw': 'NL857 1361 48 B01',
            'hours': '9:00 - 18:00',
            'season_start': '1 april',
            'season_end': '31 oktober',
            'services': [
                'Botenverhuur',
                'Vakantiehuis',
                'Camping (vaste plaatsen)',
                'Vaarkaart'
            ],
            'vakantiehuis': {
                'name': 'Vakantiehuis Belterwiede',
                'location': 'Direct aan het Belterwiede meer',
                'open': 'Het hele jaar geopend',
                'bedrooms': '5 slaapkamers (1 beneden, 4 boven)',
                'bathrooms': '2 badkamers met douche en toilet',
                'amenities': [
                    'Centrale verwarming',
                    'Open keuken met oven, magnetron, koelkast',
                    'TV en radio',
                    'Wasmachine',
                    'Kussens en dekbedden beschikbaar',
                    'Linnengoed te huur'
                ],
                'contact': 'Bel voor prijzen en beschikbaarheid: 0522 281 528'
            },
            'camping': {
                'type': 'Vaste seizoenplaatsen',
                'note': 'Alleen voor seizoensplaatsen, geen toeristische plaatsen',
                'contact': 'Bel voor informatie: 0522 281 528'
            }
        }
    
    def _build_intent_keywords(self) -> Dict[Intent, List[str]]:
        """Build keyword patterns for intent classification"""
        return {
            Intent.GREETING: [
                'hallo', 'hello', 'hi', 'hey', 'goedemorgen', 'goedemiddag', 
                'goedenavond', 'dag', 'hoi', 'good morning', 'good afternoon',
                'guten tag', 'guten morgen'
            ],
            Intent.PRICING: [
                'prijs', 'prijzen', 'kost', 'kosten', 'price', 'prices', 'cost',
                'hoeveel', 'how much', 'tarief', 'tarieven', 'wie viel', 'preis',
                'wat kost', 'euro', '€'
            ],
            Intent.PRICING_MULTIDAY: [
                'dagen', 'days', 'tage', 'week', 'weken', 'weekend', 'midweek'
            ],
            Intent.BOAT_INFO: [
                'boot', 'boten', 'boat', 'boats', 'sloep', 'tender', 'electro',
                'zeilboot', 'kano', 'kajak', 'kayak', 'sup', 'paddleboard',
                'welke boten', 'which boats', 'aanbod', 'vloot', 'fleet'
            ],
            Intent.BOAT_CAPACITY: [
                'personen', 'persoon', 'mensen', 'people', 'person', 'persons',
                'hoeveel mensen', 'capaciteit', 'capacity', 'passen', 'fit'
            ],
            Intent.OPENING_HOURS: [
                'open', 'openingstijden', 'openingstijd', 'opening', 'hours',
                'geopend', 'dicht', 'sluit', 'wanneer open', 'öffnungszeiten',
                'geöffnet', 'offen', 'geschlossen', 'öffnen',
                'seizoen', 'season', 'saison'
            ],
            Intent.LOCATION: [
                'waar', 'locatie', 'adres', 'location', 'address', 'where',
                'vinden', 'find', 'route', 'bereiken', 'wo', 'standort'
            ],
            Intent.CONTACT: [
                'contact', 'telefoon', 'phone', 'bellen', 'call', 'nummer',
                'number', 'bereikbaar', 'reach', 'telefonnummer', 'email'
            ],
            Intent.BOOKING: [
                'reserveren', 'reservering', 'boeken', 'boeking', 'book',
                'booking', 'reserve', 'reservation', 'huren', 'rent'
            ],
            Intent.AVAILABILITY: [
                'beschikbaar', 'beschikbaarheid', 'available', 'availability',
                'vrij', 'free', 'nog plek', 'vacant'
            ],
            Intent.VAKANTIEHUIS: [
                'vakantiehuis', 'vakantiewoning', 'huis', 'house', 'verblijf',
                'accommodation', 'slapen', 'overnachten', 'stay', 'ferienwohnung',
                'ferienhaus', 'holiday home', 'cottage', 'rental house'
            ],
            Intent.CAMPING: [
                'camping', 'kamperen', 'camper', 'caravan', 'tent', 'staanplaats',
                'seizoenplaats'
            ],
            Intent.VAARKAART: [
                'vaarkaart', 'kaart', 'route', 'map', 'navigatie', 'navigation',
                'vaarroute', 'routes'
            ],
            Intent.DEPOSIT: [
                'borg', 'borgsom', 'deposit', 'waarborgsom', 'kaution',
                'security deposit', 'caution', 'guarantee'
            ],
            Intent.PETS: [
                'huisdier', 'huisdieren', 'hond', 'kat', 'pet', 'pets', 'dog',
                'cat', 'dieren', 'animals', 'haustier'
            ],
            Intent.GIETHOORN: [
                'giethoorn', 'door giethoorn', 'naar giethoorn', 'via giethoorn'
            ],
            Intent.GOODBYE: [
                'doei', 'dag', 'bye', 'goodbye', 'tot ziens', 'bedankt', 'thanks'
            ],
            Intent.THANKS: [
                'bedankt', 'dank', 'thanks', 'thank you', 'dankjewel', 'dankuwel',
                'danke', 'merci'
            ]
        }
    
    def detect_intent(self, query: str) -> Tuple[Intent, float]:
        """Detect user intent from query"""
        query_lower = query.lower()
        intent_scores = {}
        
        for intent, keywords in self.intent_keywords.items():
            score = sum(1 for kw in keywords if kw in query_lower)
            if score > 0:
                intent_scores[intent] = score
        
        if not intent_scores:
            return Intent.UNKNOWN, 0.0
        
        best_intent = max(intent_scores, key=intent_scores.get)
        confidence = min(intent_scores[best_intent] / 3.0, 1.0)
        
        # Priority overrides for specific intents
        # Deposit takes priority over pricing and boat_info when deposit keywords are present
        if Intent.DEPOSIT in intent_scores and best_intent in [Intent.PRICING, Intent.BOAT_INFO]:
            best_intent = Intent.DEPOSIT
            confidence = min(intent_scores[Intent.DEPOSIT] / 2.0, 1.0)
        
        # Pets takes priority over boat info
        if Intent.PETS in intent_scores and best_intent == Intent.BOAT_INFO:
            best_intent = Intent.PETS
            confidence = min(intent_scores[Intent.PETS] / 2.0, 1.0)
        
        # Giethoorn takes priority over location
        if Intent.GIETHOORN in intent_scores and best_intent == Intent.LOCATION:
            best_intent = Intent.GIETHOORN
            confidence = min(intent_scores[Intent.GIETHOORN] / 2.0, 1.0)
        
        # Check for multi-day pricing
        if best_intent == Intent.PRICING:
            if any(kw in query_lower for kw in self.intent_keywords[Intent.PRICING_MULTIDAY]):
                best_intent = Intent.PRICING_MULTIDAY
        
        return best_intent, confidence
    
    def find_boat_in_query(self, query: str) -> Optional[BoatInfo]:
        """Find which boat is mentioned in the query"""
        query_lower = query.lower()
        
        # Boat type keywords mapped to boat IDs (ordered by specificity)
        boat_keywords = {
            'classic-tender-720': ['tender 720', 'grote tender', 'tender720', '10/12 pers', '12 personen', 'grote sloep'],
            'classic-tender-570': ['tender 570', 'tender570', '8 pers tender'],
            'electrosloop-10': ['electrosloep 10', 'sloep 10 pers', 'elektrische sloep 10', 'electrosloep voor 10'],
            'electrosloop-8': ['electrosloep 8', 'sloep 8 pers', 'elektrische sloep 8', 'electrosloep voor 8'],
            'electroboat-5': ['electroboot 5', 'elektroboot 5', 'elektrische boot 5', 'electroboot', 'elektroboot', 'kleine sloep'],
            'sailboat-4-5': ['zeilboot', 'sailboat', 'segelboot', 'zeilen', 'sailing'],
            'sailpunter-3-4': ['zeilpunter', 'punter'],
            'canoe-3': ['kano', 'canoe', 'canadese kano', 'canadese'],
            'kayak-2': ['kajak 2', 'kayak 2', 'dubbel kajak', 'tandem kajak', 'tweepersoons kajak'],
            'kayak-1': ['kajak 1', 'kayak 1', 'enkel kajak', 'solo kajak', 'eenpersoons kajak'],
            'sup-board': ['sup', 'paddleboard', 'stand up paddle', 'supboard', 'sup board']
        }
        
        # First try specific matches
        for boat_id, keywords in boat_keywords.items():
            for keyword in keywords:
                if keyword in query_lower:
                    return self.boats.get(boat_id)
        
        # Fallback: try generic boat terms (return first match)
        generic_terms = {
            'kayak': 'kayak-2',
            'kajak': 'kayak-2',
            'kano': 'canoe-3',
            'canoe': 'canoe-3',
            'tender': 'classic-tender-720',
            'sloep': 'electrosloop-8',
            'electrosloep': 'electrosloop-8',
            'electroboot': 'electroboat-5',
            'zeil': 'sailboat-4-5'
        }
        
        for term, boat_id in generic_terms.items():
            if term in query_lower:
                return self.boats.get(boat_id)
        
        return None
    
    def extract_days(self, query: str) -> int:
        """Extract number of days from query"""
        query_lower = query.lower()
        
        # Match patterns like "3 dagen", "2 days", "een week"
        days_match = re.search(r'(\d+)\s*(dag|dagen|day|days|tage|tag)', query_lower)
        if days_match:
            return min(int(days_match.group(1)), 7)  # Max 7 days in pricing
        
        # Check for weekend (2 days) - BEFORE week to avoid 'weekend' containing 'week'
        if 'weekend' in query_lower:
            return 2
        
        # Check for week
        if 'week' in query_lower:
            return 7
        
        # Check for midweek (usually 4-5 days)
        if 'midweek' in query_lower:
            return 4
        
        return 1
    
    def get_boat_price(self, boat: BoatInfo, days: int = 1, with_engine: bool = False) -> int:
        """Get accurate price for a boat for given number of days"""
        pricing = boat.pricing_with_engine if with_engine and boat.pricing_with_engine else boat.pricing
        
        if not pricing:
            return boat.price_per_day * days
        
        # Pricing array is 0-indexed where index = days - 1 (or index 0 for day 1)
        # Handle edge cases
        if days <= 0:
            return 0
        
        # For single day (index 0), some boats have 0 which means use pricePerDay
        if days == 1:
            if len(pricing) > 0 and pricing[0] > 0:
                return pricing[0]
            return boat.price_per_day
        
        # For multiple days, use the pricing array
        if days <= len(pricing):
            return pricing[days - 1] if pricing[days - 1] > 0 else boat.price_per_day * days
        
        # If days exceed pricing array, extrapolate
        return boat.price_per_day * days
    
    def get_all_boats_overview(self, language: str = 'nl') -> str:
        """Get overview of all boats with prices"""
        lines = []
        
        if language == 'en':
            categories = {
                'electric': 'Electric boats',
                'sailing': 'Sailboats',
                'canoe': 'Canoes and Kayaks',
                'sup': 'SUP boards'
            }
            deposit_text = 'deposit'
            day_text = '/day'
        elif language == 'de':
            categories = {
                'electric': 'Elektrische Boote',
                'sailing': 'Segelboote',
                'canoe': 'Kanus und Kajaks',
                'sup': 'SUP-Boards'
            }
            deposit_text = 'Kaution'
            day_text = '/Tag'
        else:  # Default: Dutch
            categories = {
                'electric': 'Elektrische boten',
                'sailing': 'Zeilboten',
                'canoe': 'Kano\'s en Kajaks',
                'sup': 'SUP boards'
            }
            deposit_text = 'borg'
            day_text = '/dag'
        
        for cat_id, cat_name in categories.items():
            cat_boats = [b for b in self.boats.values() if b.category == cat_id]
            if cat_boats:
                lines.append(f"\n**{cat_name}:**")
                for boat in sorted(cat_boats, key=lambda x: x.price_per_day, reverse=True):
                    deposit_info = f" ({deposit_text} €{boat.deposit})" if boat.deposit > 0 else ""
                    lines.append(f"• {boat.name}: €{boat.price_per_day}{day_text}, {boat.capacity}{deposit_info}")
        
        return '\n'.join(lines)
    
    def _detect_language_from_query(self, query: str) -> str:
        """Detect language from query text as fallback"""
        query_lower = query.lower()
        
        # Strong English indicators
        english_words = ['what', 'how', 'where', 'when', 'the', 'your', 'prices', 'boats', 'cost', 'much', 'are', 'is', 'can', 'do', 'have', 'hello', 'hi', 'thanks', 'thank', 'you', 'please', 'available', 'book', 'rent']
        english_count = sum(1 for word in english_words if word in query_lower)
        
        # Strong German indicators
        german_words = ['was', 'wie', 'wo', 'wann', 'ihre', 'kosten', 'boote', 'können', 'haben', 'sind', 'ist', 'viel', 'das', 'der', 'die', 'guten', 'danke', 'bitte', 'verfügbar', 'buchen', 'mieten']
        german_count = sum(1 for word in german_words if word in query_lower)
        
        # Strong Dutch indicators
        dutch_words = ['wat', 'hoe', 'waar', 'wanneer', 'jullie', 'kosten', 'boten', 'kunnen', 'hebben', 'zijn', 'prijs', 'kost', 'voor', 'een', 'de', 'het', 'hallo', 'hoi', 'bedankt', 'alstublieft', 'beschikbaar', 'boeken', 'huren']
        dutch_count = sum(1 for word in dutch_words if word in query_lower)
        
        if english_count > german_count and english_count > dutch_count:
            return 'en'
        elif german_count > english_count and german_count > dutch_count:
            return 'de'
        return 'nl'
    
    def _get_cache_key(self, query: str, language: str) -> str:
        """Generate a normalized cache key for the query"""
        # Normalize query for better cache hits
        normalized = query.lower().strip()
        return f"{normalized}:{language}"
    
    def _cache_response(self, cache_key: str, response: Dict[str, Any]):
        """Add response to cache with LRU eviction"""
        if len(self._response_cache) >= self._cache_size:
            # Remove oldest entry (LRU)
            oldest_key = self._cache_order.pop(0)
            del self._response_cache[oldest_key]
        
        self._response_cache[cache_key] = response
        self._cache_order.append(cache_key)
    
    def answer_query(self, query: str, language: str = 'nl') -> Dict[str, Any]:
        """
        Generate accurate answer based on query and knowledge base
        OPTIMIZED: Uses response caching for repeated queries
        PRIORITY: Checks trained responses first
        """
        # Check cache first (sub-millisecond for cache hits)
        cache_key = self._get_cache_key(query, language)
        if cache_key in self._response_cache:
            # Move to end of LRU order
            self._cache_order.remove(cache_key)
            self._cache_order.append(cache_key)
            return self._response_cache[cache_key]
        
        # PRIORITY: Check for trained responses first
        trained_response = self.find_trained_response(query)
        if trained_response:
            result = {
                'response': trained_response,
                'intent': 'trained',
                'confidence': 0.95,  # High confidence for trained responses
                'response_type': 'trained',
                'boat_detected': None,
                'days_detected': None,
                'from_training': True
            }
            # Cache the result
            self._cache_response(cache_key, result)
            print(f"📚 Using trained response for: {query[:50]}...")
            return result
        
        # Verify/correct language detection (fast pattern-based)
        detected_lang = self._detect_language_from_query(query)
        if detected_lang != language:
            language = detected_lang
        
        intent, confidence = self.detect_intent(query)
        boat = self.find_boat_in_query(query)
        days = self.extract_days(query)
        
        response = ""
        response_type = intent.value
        
        # Handle different intents
        if intent == Intent.GREETING:
            response = self._greeting_response(language)
        
        elif intent in [Intent.PRICING, Intent.PRICING_MULTIDAY]:
            response = self._pricing_response(boat, days, language)
        
        elif intent == Intent.BOAT_INFO:
            if boat:
                response = self._boat_info_response(boat, language)
            else:
                response = self._all_boats_response(language)
        
        elif intent == Intent.BOAT_CAPACITY:
            if boat:
                response = self._capacity_response(boat, language)
            else:
                response = self._all_capacity_response(language)
        
        elif intent == Intent.OPENING_HOURS:
            response = self._hours_response(language)
        
        elif intent == Intent.LOCATION:
            response = self._location_response(language)
        
        elif intent == Intent.CONTACT:
            response = self._contact_response(language)
        
        elif intent == Intent.BOOKING:
            response = self._booking_response(language)
        
        elif intent == Intent.AVAILABILITY:
            response = self._availability_response(language)
        
        elif intent == Intent.VAKANTIEHUIS:
            response = self._vakantiehuis_response(language)
        
        elif intent == Intent.CAMPING:
            response = self._camping_response(language)
        
        elif intent == Intent.VAARKAART:
            response = self._vaarkaart_response(language)
        
        elif intent == Intent.DEPOSIT:
            response = self._deposit_response(boat, language)
        
        elif intent == Intent.PETS:
            response = self._pets_response(language)
        
        elif intent == Intent.GIETHOORN:
            response = self._giethoorn_response(language)
        
        elif intent == Intent.THANKS:
            response = self._thanks_response(language)
        
        elif intent == Intent.GOODBYE:
            response = self._goodbye_response(language)
        
        else:
            response = self._fallback_response(language)
            response_type = "fallback"
        
        result = {
            'response': response,
            'intent': intent.value,
            'confidence': confidence,
            'response_type': response_type,
            'boat_detected': boat.name if boat else None,
            'days_detected': days if days > 1 else None
        }
        
        # Cache the response for future queries
        self._cache_response(cache_key, result)
        
        return result
    
    # Response generators for each intent
    def _greeting_response(self, lang: str) -> str:
        responses = {
            'nl': f"Hallo! Welkom bij {self.business_info['name']}. Ik help u graag met informatie over onze boten, prijzen, het vakantiehuis of andere diensten. Wat wilt u weten?",
            'en': f"Hello! Welcome to {self.business_info['name']}. I'm happy to help you with information about our boats, prices, the holiday home, or other services. What would you like to know?",
            'de': f"Hallo! Willkommen bei {self.business_info['name']}. Ich helfe Ihnen gerne mit Informationen über unsere Boote, Preise, das Ferienhaus oder andere Dienstleistungen. Was möchten Sie wissen?"
        }
        return responses.get(lang, responses['nl'])
    
    def _pricing_response(self, boat: Optional[BoatInfo], days: int, lang: str) -> str:
        if boat:
            price = self.get_boat_price(boat, days)
            deposit_info = f" Er is een borg van €{boat.deposit}." if boat.deposit > 0 else ""
            
            if days > 1:
                day_price = boat.price_per_day
                if lang == 'nl':
                    return f"De {boat.name} ({boat.capacity}) kost €{price} voor {days} dagen (€{day_price}/dag).{deposit_info}"
                elif lang == 'en':
                    deposit_info = f" There is a deposit of €{boat.deposit}." if boat.deposit > 0 else ""
                    return f"The {boat.name} ({boat.capacity}) costs €{price} for {days} days (€{day_price}/day).{deposit_info}"
                else:
                    deposit_info = f" Es gibt eine Kaution von €{boat.deposit}." if boat.deposit > 0 else ""
                    return f"Die {boat.name} ({boat.capacity}) kostet €{price} für {days} Tage (€{day_price}/Tag).{deposit_info}"
            else:
                if lang == 'nl':
                    return f"De {boat.name} ({boat.capacity}) kost €{price} per dag.{deposit_info}"
                elif lang == 'en':
                    deposit_info = f" There is a deposit of €{boat.deposit}." if boat.deposit > 0 else ""
                    return f"The {boat.name} ({boat.capacity}) costs €{price} per day.{deposit_info}"
                else:
                    deposit_info = f" Es gibt eine Kaution von €{boat.deposit}." if boat.deposit > 0 else ""
                    return f"Die {boat.name} ({boat.capacity}) kostet €{price} pro Tag.{deposit_info}"
        else:
            # Return all prices
            overview = self.get_all_boats_overview(lang)
            if lang == 'nl':
                return f"Hier zijn onze prijzen per dag:{overview}\n\nVoor meerdere dagen gelden kortingen. Bel {self.business_info['phone']} voor exacte prijzen."
            elif lang == 'en':
                return f"Here are our prices per day:{overview}\n\nDiscounts apply for multiple days. Call {self.business_info['phone']} for exact prices."
            else:
                return f"Hier sind unsere Preise pro Tag:{overview}\n\nBei mehreren Tagen gibt es Rabatte. Rufen Sie {self.business_info['phone']} für genaue Preise an."
    
    def _boat_info_response(self, boat: BoatInfo, lang: str) -> str:
        if lang == 'nl':
            return f"De {boat.name} is een {boat.category} boot voor {boat.capacity}. {boat.description} Prijs: €{boat.price_per_day}/dag."
        elif lang == 'en':
            return f"The {boat.name} is a {boat.category} boat for {boat.capacity}. Price: €{boat.price_per_day}/day."
        else:
            return f"Die {boat.name} ist ein {boat.category} Boot für {boat.capacity}. Preis: €{boat.price_per_day}/Tag."
    
    def _all_boats_response(self, lang: str) -> str:
        overview = self.get_all_boats_overview(lang)
        if lang == 'nl':
            return f"Wij verhuren diverse boten:{overview}"
        elif lang == 'en':
            return f"We rent various boats:{overview}"
        else:
            return f"Wir vermieten verschiedene Boote:{overview}"
    
    def _capacity_response(self, boat: BoatInfo, lang: str) -> str:
        if lang == 'nl':
            return f"De {boat.name} is geschikt voor {boat.capacity}."
        elif lang == 'en':
            return f"The {boat.name} can accommodate {boat.capacity}."
        else:
            return f"Die {boat.name} ist geeignet für {boat.capacity}."
    
    def _all_capacity_response(self, lang: str) -> str:
        lines = ["Onze boten per capaciteit:" if lang == 'nl' else "Our boats by capacity:"]
        for boat in sorted(self.boats.values(), key=lambda x: x.price_per_day, reverse=True):
            lines.append(f"• {boat.name}: {boat.capacity}")
        return '\n'.join(lines)
    
    def _hours_response(self, lang: str) -> str:
        info = self.business_info
        if lang == 'nl':
            return f"Wij zijn geopend van {info['season_start']} tot {info['season_end']}, dagelijks van {info['hours']}. Buiten het seizoen op afspraak."
        elif lang == 'en':
            return f"We are open from {info['season_start']} to {info['season_end']}, daily from {info['hours']}. Outside the season by appointment."
        else:
            return f"Wir sind geöffnet von {info['season_start']} bis {info['season_end']}, täglich von {info['hours']}. Außerhalb der Saison nach Vereinbarung."
    
    def _location_response(self, lang: str) -> str:
        info = self.business_info
        if lang == 'nl':
            return f"U vindt ons op {info['address']}, {info['postal']}, {info['country']}. We liggen in het prachtige {info['location_area']}."
        elif lang == 'en':
            return f"You can find us at {info['address']}, {info['postal']}, {info['country']}. We are located in the beautiful {info['location_area']}."
        else:
            return f"Sie finden uns am {info['address']}, {info['postal']}, {info['country']}. Wir befinden uns im wunderschönen {info['location_area']}."
    
    def _contact_response(self, lang: str) -> str:
        info = self.business_info
        if lang == 'nl':
            return f"U kunt ons bereiken op {info['phone']}. We zijn beschikbaar van {info['hours']} tijdens het seizoen ({info['season_start']} - {info['season_end']})."
        elif lang == 'en':
            return f"You can reach us at {info['phone']}. We are available from {info['hours']} during the season ({info['season_start']} - {info['season_end']})."
        else:
            return f"Sie erreichen uns unter {info['phone']}. Wir sind von {info['hours']} während der Saison ({info['season_start']} - {info['season_end']}) erreichbar."
    
    def _booking_response(self, lang: str) -> str:
        if lang == 'nl':
            return f"U kunt reserveren door te bellen naar {self.business_info['phone']}. We raden aan om in het hoogseizoen (juli-augustus) ruim van tevoren te reserveren. U kunt ook online boeken via onze website."
        elif lang == 'en':
            return f"You can make a reservation by calling {self.business_info['phone']}. We recommend booking well in advance during high season (July-August). You can also book online via our website."
        else:
            return f"Sie können reservieren, indem Sie {self.business_info['phone']} anrufen. Wir empfehlen, in der Hauptsaison (Juli-August) rechtzeitig zu buchen. Sie können auch online über unsere Website buchen."
    
    def _availability_response(self, lang: str) -> str:
        if lang == 'nl':
            return f"Voor actuele beschikbaarheid kunt u het beste bellen naar {self.business_info['phone']} of online reserveren via onze website. De beschikbaarheid varieert per dag en seizoen."
        elif lang == 'en':
            return f"For current availability, please call {self.business_info['phone']} or book online via our website. Availability varies by day and season."
        else:
            return f"Für aktuelle Verfügbarkeit rufen Sie bitte {self.business_info['phone']} an oder buchen Sie online über unsere Website. Die Verfügbarkeit variiert je nach Tag und Saison."
    
    def _vakantiehuis_response(self, lang: str) -> str:
        vh = self.business_info['vakantiehuis']
        if lang == 'nl':
            return f"Ons {vh['name']} ligt {vh['location']} en is {vh['open']}. Het heeft {vh['bedrooms']} en {vh['bathrooms']}. Faciliteiten: centrale verwarming, complete keuken, TV. {vh['contact']}"
        elif lang == 'en':
            return f"Our {vh['name']} is located {vh['location']} and is open all year. It has {vh['bedrooms']} and {vh['bathrooms']}. Facilities: central heating, complete kitchen, TV. Call {self.business_info['phone']} for prices and availability."
        else:
            return f"Unser {vh['name']} liegt {vh['location']} und ist das ganze Jahr geöffnet. Es hat {vh['bedrooms']} und {vh['bathrooms']}. Ausstattung: Zentralheizung, komplette Küche, TV. Rufen Sie {self.business_info['phone']} für Preise und Verfügbarkeit an."
    
    def _camping_response(self, lang: str) -> str:
        camping = self.business_info['camping']
        if lang == 'nl':
            return f"Onze camping biedt {camping['type']}. Let op: {camping['note']}. {camping['contact']}"
        elif lang == 'en':
            return f"Our camping offers seasonal pitches. Note: Only for seasonal pitches, no tourist pitches. Call {self.business_info['phone']} for information."
        else:
            return f"Unser Campingplatz bietet Saisonstellplätze. Hinweis: Nur für Saisonplätze, keine Touristenplätze. Rufen Sie {self.business_info['phone']} für Informationen an."
    
    def _vaarkaart_response(self, lang: str) -> str:
        if lang == 'nl':
            return f"Wij bieden vaarkaarten aan voor het Weerribben-Wieden gebied. Deze zijn verkrijgbaar bij onze locatie. Bel {self.business_info['phone']} voor meer informatie."
        elif lang == 'en':
            return f"We offer navigation maps for the Weerribben-Wieden area. These are available at our location. Call {self.business_info['phone']} for more information."
        else:
            return f"Wir bieten Navigationskarten für das Weerribben-Wieden Gebiet an. Diese sind an unserem Standort erhältlich. Rufen Sie {self.business_info['phone']} für weitere Informationen an."
    
    def _deposit_response(self, boat: Optional[BoatInfo], lang: str) -> str:
        if boat:
            if boat.deposit > 0:
                if lang == 'nl':
                    return f"Voor de {boat.name} is een borg van €{boat.deposit} vereist. Deze krijgt u terug bij het inleveren van de boot in goede staat."
                elif lang == 'en':
                    return f"A deposit of €{boat.deposit} is required for the {boat.name}. This will be returned when the boat is returned in good condition."
                else:
                    return f"Für die {boat.name} ist eine Kaution von €{boat.deposit} erforderlich. Diese wird zurückgegeben, wenn das Boot in gutem Zustand zurückgegeben wird."
            else:
                if lang == 'nl':
                    return f"Voor de {boat.name} is geen borg vereist."
                else:
                    return f"No deposit is required for the {boat.name}."
        else:
            if lang == 'nl':
                return "De borg varieert per boot: €100 voor tenders en electrosloepen, €50 voor zeilboten, geen borg voor kano's, kajaks en SUP boards."
            elif lang == 'en':
                return "The deposit varies by boat: €100 for tenders and electric sloops, €50 for sailboats, no deposit for canoes, kayaks and SUP boards."
            else:
                return "Die Kaution variiert je nach Boot: €100 für Tender und Elektrosloepen, €50 für Segelboote, keine Kaution für Kanus, Kajaks und SUP-Boards."
    
    def _pets_response(self, lang: str) -> str:
        if lang == 'nl':
            return "Huisdieren zijn helaas niet toegestaan op onze boten."
        elif lang == 'en':
            return "Unfortunately, pets are not allowed on our boats."
        else:
            return "Haustiere sind leider auf unseren Booten nicht erlaubt."
    
    def _giethoorn_response(self, lang: str) -> str:
        if lang == 'nl':
            return "Let op: Met de grote boten (Tender 720) mag u niet door Giethoorn heen varen vanwege de lage bruggen. Met kleinere boten, kano's en kajaks is dit wel mogelijk."
        elif lang == 'en':
            return "Please note: You cannot sail through Giethoorn with the large boats (Tender 720) due to low bridges. This is possible with smaller boats, canoes and kayaks."
        else:
            return "Bitte beachten Sie: Mit den großen Booten (Tender 720) können Sie wegen der niedrigen Brücken nicht durch Giethoorn fahren. Mit kleineren Booten, Kanus und Kajaks ist dies möglich."
    
    def _thanks_response(self, lang: str) -> str:
        if lang == 'nl':
            return "Graag gedaan! Heeft u nog andere vragen?"
        elif lang == 'en':
            return "You're welcome! Do you have any other questions?"
        else:
            return "Gerne geschehen! Haben Sie weitere Fragen?"
    
    def _goodbye_response(self, lang: str) -> str:
        if lang == 'nl':
            return f"Tot ziens! We hopen u snel te verwelkomen bij {self.business_info['name']}. Bel gerust naar {self.business_info['phone']} als u nog vragen heeft."
        elif lang == 'en':
            return f"Goodbye! We hope to welcome you soon at {self.business_info['name']}. Feel free to call {self.business_info['phone']} if you have any questions."
        else:
            return f"Auf Wiedersehen! Wir hoffen, Sie bald bei {self.business_info['name']} begrüßen zu dürfen. Rufen Sie gerne {self.business_info['phone']} an, wenn Sie Fragen haben."
    
    def _fallback_response(self, lang: str) -> str:
        if lang == 'nl':
            return f"Ik help u graag verder! Voor specifieke vragen kunt u ons bellen op {self.business_info['phone']}. We verhuren boten, een vakantiehuis, en hebben een camping. Wat wilt u weten over onze diensten?"
        elif lang == 'en':
            return f"I'm happy to help! For specific questions, please call us at {self.business_info['phone']}. We rent boats, a holiday home, and have a camping site. What would you like to know about our services?"
        else:
            return f"Ich helfe Ihnen gerne! Für spezifische Fragen rufen Sie uns bitte an unter {self.business_info['phone']}. Wir vermieten Boote, ein Ferienhaus und haben einen Campingplatz. Was möchten Sie über unsere Dienstleistungen wissen?"


# Global instance
_knowledge_base = None

def get_knowledge_base() -> KnowledgeBase:
    """Get the global knowledge base instance"""
    global _knowledge_base
    if _knowledge_base is None:
        _knowledge_base = KnowledgeBase()
    return _knowledge_base

