# Road Damage Detection System
**Case Study: Rwanda Transport Development Agency (RTDA)**

**Author:** Ange Abayisenga | **Student ID:** 2405000178  
**University of Kigali** | Bachelor's Degree in Information Technology | July 2026

---

## Overview

An intelligent web-based platform that uses **Artificial Intelligence**, **Computer Vision**, **Machine Learning**, **GIS** and **Mobile Technology** to automatically detect, classify and manage road defects in real time.

## Features

- 📸 Road image capture and upload via mobile/web
- 🤖 AI-powered damage detection and classification (Potholes, Cracks, Rutting, etc.)
- 🗺️ GIS-based interactive map showing damage locations (Leaflet.js + OpenStreetMap)
- 📊 Dashboard with analytics and severity breakdown
- 🔔 Real-time notifications for road authorities
- 👥 Role-based access: Admin, Transport Authority Official, Engineer, Road User
- 📋 Maintenance tracking and status updates

## User Roles

| Role | Access |
|------|--------|
| Admin | Full system control, user management, settings |
| Transport Authority Official | View reports, analytics, approve/reject |
| Engineer | Submit reports, validate damage, update maintenance |
| Road User | Report damage, view map, track submitted reports |

## Tech Stack

- **Backend:** PHP 7.4+, MySQL
- **Frontend:** Bootstrap 3, jQuery, Font Awesome
- **Maps:** Leaflet.js + OpenStreetMap
- **Theme:** Porto Admin Theme

## Installation

1. Clone the repository into your web server root (e.g. `/opt/lampp/htdocs/angeabaseng`)
2. Import the database: `mysql -u root < database/rdds.sql`
3. Configure DB credentials in `inc/db.php`
4. Visit `http://localhost/angeabaseng`

## Default Login

| Username | Password | Role |
|----------|----------|------|
| admin | password | Admin |
| james | password | Transport Authority |
| ange | password | Engineer |

## Project Structure

```
angeabaseng/
├── inc/           # Core includes (db, auth, header, sidebar, footer)
├── assets/        # CSS, JS, vendor libraries
├── uploads/       # Uploaded road images
├── database/      # SQL schema
├── home.php       # Dashboard
├── report_damage.php  # Submit damage report
├── map_view.php   # GIS interactive map
├── all_reports.php    # Reports listing
├── view_report.php    # Report detail
└── index.php      # Login page
```

## References

- Rwanda Transport Development Agency (RTDA). (2024). Annual Report 2023–2024.
- Zhang et al. (2023). Automated pavement distress detection using deep CNNs.
- Li et al. (2022). GIS-integrated AI for road condition assessment.
- Ahsan, K.M.K. (2025). GeoAI and deep learning for road infrastructure damage detection.
