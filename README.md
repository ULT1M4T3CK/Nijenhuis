# Nijenhuis Botenverhuur

Boat rental website with booking system, admin panel, chatbot, and payment integration.

## Project Structure

```
├── admin/              Admin panel & management
│   └── overview.html   Admin navigation page
├── backend/
│   ├── api/            Flask API scaffold
│   ├── chatbot/        Chatbot engine & training
│   └── webhooks/       Payment webhooks (Mollie)
├── components/         Shared PHP components
├── deploy/
│   ├── nginx/          Web server config
│   └── systemd/        Service files
├── documentation/      System documentation
├── frontend/
│   ├── css/            Stylesheets
│   ├── Images/         Website images
│   ├── public/         Static assets
│   └── src/js/         JavaScript modules
├── js/                 Shared JS (boat data service)
├── pages/              Public PHP pages
├── scripts/            Management scripts
│
├── index.php           Web entry point
├── .env                Environment variables
├── package.json        npm configuration
├── requirements.txt    Python dependencies
└── README.md           This file
```

## Running Locally

Use the router so clean URLs (`/blog`, `/booking`, and so on) work. Without it, paths like `/blog` fall through to root `index.php` and redirect to the homepage.

```bash
php -S localhost:8888 router.php
```

Or: `npm run dev` (same command). Then open http://localhost:8888 — blog: http://localhost:8888/blog

## Documentation

| Document | Description |
|----------|-------------|
| [Admin System](admin/README.md) | Admin panel |
| [Booking System](documentation/BOOKING_SYSTEM_GUIDE.md) | Booking flow |
| [Payment Integration](documentation/MOLLIE_INTEGRATION.md) | Mollie setup |
| [Chatbot Training](backend/chatbot/training/README.md) | Training data |
| [Environment Example](documentation/env.example) | Config template |

## License

Proprietary. All rights reserved.
