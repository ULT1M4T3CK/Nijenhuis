# Chatbot Training Data

## Overview

Training data for the multilingual customer support chatbot.

## Training Data

Location: `data/training_data.json`

### Format
```json
{
  "question": "what are your prices?",
  "response": "Our boat rental prices vary by boat type...",
  "language": "en",
  "response_type": "pricing"
}
```

### Languages Supported
- Dutch (nl)
- English (en)
- German (de)

### Response Types
- `pricing` - Price and rate questions
- `booking` - Reservation inquiries
- `general` - General information
- `opening_hours` - Operating hours
- `contact` - Contact information
- `boats` - Boat specifications
- `location` - Location and directions

## Enhancing Training Data

Run the enhancement script to add more training examples:
```bash
cd backend/chatbot/training
python3 enhance_albot_training.py
```

This downloads public datasets and integrates them with existing training data.

## API Server

After updating training data, restart the chatbot server:
```bash
python backend/chatbot/api/server.py
```

The server automatically loads training data on startup.
