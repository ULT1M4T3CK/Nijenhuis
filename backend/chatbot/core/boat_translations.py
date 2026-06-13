#!/usr/bin/env python3
"""
Boat translation utilities for the Nijenhuis chatbot
"""

# Boat model translations
BOAT_TRANSLATIONS = {
    'nl': {
        'Tender 720': 'Tender 720',
        'Tender 570': 'Tender 570',
        'Electrosloep 10': 'Electrosloep 10',
        'Electrosloep 8': 'Electrosloep 8',
        'Zeilboot': 'Zeilboot',
        'Kano': 'Kano',
        'Kajak': 'Kajak',
        'Sup Board': 'Sup Board'
    },
    'de': {
        'Tender 720': 'Tender 720',
        'Tender 570': 'Tender 570',
        'Electrosloep 10': 'Electrosloep 10',
        'Electrosloep 8': 'Electrosloep 8',
        'Zeilboot': 'Segelboot',
        'Kano': 'Kanu',
        'Kajak': 'Kajak',
        'Sup Board': 'SUP-Board'
    },
    'en': {
        'Tender 720': 'Tender 720',
        'Tender 570': 'Tender 570',
        'Electrosloep 10': 'Electrosloep 10',
        'Electrosloep 8': 'Electrosloep 8',
        'Zeilboot': 'Sailboat',
        'Kano': 'Canoe',
        'Kajak': 'Kayak',
        'Sup Board': 'SUP Board'
    }
}

def translate_boat_names(text: str, target_language: str) -> str:
    """Translate boat model names in the given text"""
    if target_language not in BOAT_TRANSLATIONS:
        return text
    
    translations = BOAT_TRANSLATIONS[target_language]
    translated_text = text
    
    for dutch_name, translated_name in translations.items():
        translated_text = translated_text.replace(dutch_name, translated_name)
    
    return translated_text 