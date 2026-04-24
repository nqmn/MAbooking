# MA Booking Joomla Component

`MA Booking` is a custom Joomla component (`com_mabooking`) for venue and room booking. It provides:

- an administrator dashboard with monthly calendar and booking summary
- a booking manager for creating and editing reservations
- a public-facing booking calendar with a request form
- an embeddable widget layout for external or internal use
- an optional public `.ics` feed
- optional Joomla Article syncing for selected booking statuses

The current packaged component version in this repository is `0.4.5`.

## Repository Layout

```text
MAbooking/
|-- com_mabooking/                 Joomla installable component source
|   |-- admin/                     Administrator MVC, SQL, forms, templates
|   |-- site/                      Site MVC, forms, templates, services
|   |-- com_mabooking.xml          Joomla manifest
|   `-- README.md                  Short component notes
|-- com_mabooking.zip              Packaged build
|-- com_mabooking_v0.4.5.zip       Packaged build with explicit version
`-- app.html                       Original UI mockup reference
```

## What The System Does

This component manages bookings for venues and their spaces. A venue is a top-level location such as `Grand Ballroom` or `Town Hall`. Each venue contains one or more bookable spaces such as `Section 1`, `Room 4`, or `Main Hall`.

Bookings store:

- event title
- booking date
- start and end time
- venue and room/space
- client name, phone, and email
- attendee count
- status: `pending`, `confirmed`, or `cancelled`
- source: `admin` or `site`
- notes
- optional linked Joomla article ID

The site form creates records as `pending` by default. Administrators can later review and change the status.

## Main Features

### 1. Admin dashboard

The admin dashboard is the default component screen:

- monthly booking calendar
- totals for all bookings by status
- quick navigation by month
- click-on-day shortcut to create a booking for that date
- upcoming bookings table

This is implemented primarily in:

- `com_mabooking/admin/src/Model/DashboardModel.php`
- `com_mabooking/admin/tmpl/dashboard/default.php`

### 2. Booking management in Joomla administrator

Admins can:

- list all bookings
- search by event, client, venue, room, phone, or email
- filter by booking status
- sort by date, event, client, status, or ID
- create and edit bookings manually

The booking form includes publishing state, booking source, and linked article ID.

Main files:

- `com_mabooking/admin/src/Model/BookingsModel.php`
- `com_mabooking/admin/src/Model/BookingModel.php`
- `com_mabooking/admin/forms/booking.xml`
- `com_mabooking/admin/tmpl/bookings/default.php`

### 3. Public booking calendar

The site side exposes a calendar view with:

- monthly calendar navigation
- per-day booking visibility
- booking status styling
- booking request form grouped into date/time, venue, client, and notes sections

The public form posts to `task=booking.submit`.

Main files:

- `com_mabooking/site/src/Controller/BookingController.php`
- `com_mabooking/site/src/Model/CalendarModel.php`
- `com_mabooking/site/forms/booking.xml`
- `com_mabooking/site/tmpl/calendar/default.php`

### 4. Widget layout

The component also exposes a lightweight widget layout:

- route: `index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component`
- designed for iframe embedding or menu-item use
- shows a clean calendar with venue-color markers

The admin `Widgets` screen gives ready-to-copy:

- direct widget URL
- iframe embed code
- Joomla internal route
- public ICS URL

Main files:

- `com_mabooking/admin/src/View/Widgets/HtmlView.php`
- `com_mabooking/admin/tmpl/widgets/default.php`
- `com_mabooking/site/tmpl/calendar/widget.php`

### 5. Public ICS feed

The component can expose an internet calendar feed:

- route: `index.php?option=com_mabooking&task=calendar.ics`
- disabled by default
- can be limited to confirmed bookings only
- excludes cancelled bookings
- includes venue, room, notes, summary, location, and event times

Main files:

- `com_mabooking/site/src/Controller/CalendarController.php`
- `com_mabooking/site/src/Model/CalendarModel.php`
- `com_mabooking/admin/config.xml`

### 6. Optional Joomla Article sync

When enabled in component options, bookings can create or update Joomla Articles.

Behavior:

- sync can be restricted to selected statuses
- target article category is configurable
- article publish state is configurable
- if a booking status no longer qualifies for sync, the linked article is unpublished
- synced articles keep their body content after creation, so editors can safely customize article content later

Main files:

- `com_mabooking/site/src/Helper/ArticleHelper.php`
- `com_mabooking/admin/src/Model/BookingModel.php`
- `com_mabooking/admin/config.xml`

## Data Model

The installer creates three tables:

### `#__mabooking_venues`

Stores the venue list.

Key fields:

- `id`
- `title`
- `alias`
- `description`
- `ordering`
- `state`

### `#__mabooking_spaces`

Stores bookable rooms/spaces under each venue.

Key fields:

- `id`
- `venue_id`
- `title`
- `alias`
- `capacity_min`
- `capacity_max`
- `size_label`
- `details`
- `ordering`
- `state`

### `#__mabooking_bookings`

Stores actual reservations and requests.

Key fields:

- `event_title`
- `booking_date`
- `start_time`
- `end_time`
- `venue_id`
- `space_id`
- `client_name`
- `client_phone`
- `client_email`
- `attendees`
- `status`
- `source`
- `article_id`
- `notes`
- `state`

## Seeded Venue Data

The install SQL seeds example venues and spaces immediately after installation:

- `Grand Ballroom`
  with `Section 1`, `Section 2`, `Section 3`
- `Exhibition Hall`
  with `Exhibition Hall`
- `Bougainvillea Room`
  with `Room 1` to `Room 6`
- `Town Hall`
  with `Main Hall` and `Level 1`

These records come from `com_mabooking/admin/sql/install.mysql.utf8.sql`.

## Validation And Booking Safety

Both site and admin booking saves enforce:

- valid time format: `HH:MM` or `HH:MM:SS`
- end time must be later than start time
- selected room must belong to the selected venue
- no overlapping booking in the same venue/space/date unless the conflicting booking is `cancelled`

To reduce race conditions, the save logic also acquires a MySQL named lock using:

- venue ID
- space ID
- booking date

That means two users trying to reserve the same room/date window at the same time are serialized before insert or update.

## Component Options

Configured in Joomla via `Components -> MA Booking -> Options`.

Available settings from `admin/config.xml`:

### Articles

- `enable_article_sync`
- `article_sync_statuses`
- `article_category_id`
- `article_state`

### ICS

- `enable_public_ics`
- `public_ics_confirmed_only`

## Install And Use

### Install in Joomla

1. Go to Joomla Administrator.
2. Open `System -> Install -> Extensions`.
3. Upload either `com_mabooking.zip` or `com_mabooking_v0.4.5.zip`.
4. After install, open `Components -> MA Booking`.

### Basic setup after install

1. Review the seeded venues and spaces in the database if you need a different venue structure.
2. Open component `Options` and decide whether to enable article sync and public ICS.
3. Open the `Widgets` screen if you want an iframe or public calendar embed.
4. Create a Joomla menu item pointing to the calendar view if you want a public navigation entry.

## Upgrade Notes

The manifest includes Joomla update schema support:

- `0.2.0.sql`
- `0.4.3.sql`
- `0.4.4.sql`

The component manifest version is `0.4.5`.

## Uninstall Behavior

Uninstall intentionally preserves booking data. The uninstall SQL only contains comments and does not drop the tables automatically.

Preserved tables:

- `#__mabooking_bookings`
- `#__mabooking_spaces`
- `#__mabooking_venues`

This is a safety choice so uninstalling the extension does not silently destroy booking records.

## Joomla Architecture Summary

This project follows the Joomla component MVC pattern:

- `admin/src/...` handles administrator controllers, models, views, and table classes
- `site/src/...` handles public controllers, models, and views
- `forms/*.xml` define Joomla form fields
- `tmpl/...` contains the rendered layouts
- `services/provider.php` wires the component services into Joomla
- `com_mabooking.xml` declares install, update, languages, files, and admin menu entries

Admin menu entries defined by the manifest:

- `Dashboard`
- `Bookings`
- `Widgets`

## Important Implementation Notes

- The UI styling is embedded directly inside the view templates.
- The public booking form stores requests as `pending`.
- Cancelled bookings are excluded from the public calendar and ICS feed.
- ICS entries are generated dynamically, not stored as files.
- Article sync uses Joomla content tables and stores the linked content ID back into the booking record.
- The repository includes `app.html` as the original design reference used to shape the admin and public layouts.

## Relevant Entry Points

- Manifest: `com_mabooking/com_mabooking.xml`
- Admin bootstrap: `com_mabooking/admin/mabooking.php`
- Site bootstrap: `com_mabooking/site/mabooking.php`
- Public calendar page: `index.php?option=com_mabooking&view=calendar`
- Public widget page: `index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component`
- Public ICS feed: `index.php?option=com_mabooking&task=calendar.ics`

## Notes For Future Development

Areas that are still likely candidates for improvement:

- venue and space management UI in administrator instead of SQL-seeded-only structure
- stronger permission rules per booking action
- email notifications on booking submission or status change
- recurring bookings
- frontend filtering by venue or room
- richer ICS timezone handling
- automated tests
