# com_mabooking

Portable Joomla component for venue calendar booking.

Features in this first local build:

- Administrator dashboard with booking summary
- Administrator booking list and edit form
- Seeded venues and spaces matching the ICC mockup
- Public calendar view with booking request form
- Conflict detection for overlapping venue-space bookings
- Optional booking-to-article sync for Joomla Articles
- Administrator widget page with copyable calendar link and iframe embed code
- Public `.ics` feed for importing bookings into Google Calendar or other calendar apps

Install:

1. Zip the `com_mabooking` folder contents.
2. Install through Joomla Extension Manager.
3. Open `Components -> MA Booking`.

Notes:

- The component is self-contained and does not modify template files.
- Business statuses are `pending`, `confirmed`, and `cancelled`.
- The public form saves requests as `pending` by default.
- Uninstall preserves booking tables by default for safety.
- When article sync is enabled, linked articles keep their body content so users can edit description, gallery, and media in Joomla Articles without later booking saves overwriting that content.
- Joomla update-schema support is included under `administrator/sql/updates/mysql` for future upgrades.
- Joomla admin menu link repair is included in update schema `0.4.4.sql`.
