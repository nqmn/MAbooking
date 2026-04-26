# MA Booking Joomla Component

`MA Booking` is a custom Joomla component (`com_mabooking`) for venue and room booking. It provides:

- an administrator dashboard with monthly calendar and booking summary
- a booking manager for creating and editing reservations
- a venue and room management screen
- a public-facing booking calendar with a request form
- an embeddable widget layout for external or internal use
- an optional public `.ics` feed
- optional Joomla Article syncing for selected booking statuses
- a quickicon plugin for the Joomla home dashboard

The current packaged version in this repository is `0.4.6`.

## Repository Layout

```text
MAbooking/
|-- com_mabooking/                 Joomla component source
|   |-- admin/                     Administrator MVC, SQL, forms, templates
|   |-- site/                      Site MVC, forms, templates, services
|   `-- com_mabooking.xml          Joomla manifest
|-- plg_quickicon_mabooking/       Quickicon plugin source
|   |-- language/en-GB/
|   |-- services/
|   |-- src/Extension/
|   `-- mabooking.xml
|-- com_mabooking.zip              Component-only build
|-- plg_quickicon_mabooking.zip    Plugin-only build
|-- pkg_mabooking.xml              Package manifest
|-- pkg_mabooking.zip              Full package build (component + plugin)
`-- compress_zip.md                ZIP packaging reference for Windows
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

- monthly booking calendar with venue-color markers
- click-on-day popover showing booking summaries
- click-on-day shortcut to create a booking for that date
- totals for all bookings by status
- tabbed view: Master Calendar, Bookings, Upcoming Events, Past Events
- quick navigation by month

Main files:

- `com_mabooking/admin/src/Model/DashboardModel.php`
- `com_mabooking/admin/tmpl/dashboard/default.php`

### 2. Booking management

Admins can:

- list all bookings
- search by event, client, venue, room, phone, or email
- filter by booking status
- create and edit bookings manually

The booking form includes publishing state, booking source, and linked article ID.

Main files:

- `com_mabooking/admin/src/Model/BookingsModel.php`
- `com_mabooking/admin/src/Model/BookingModel.php`
- `com_mabooking/admin/forms/booking.xml`
- `com_mabooking/admin/tmpl/bookings/default.php`
- `com_mabooking/admin/tmpl/booking/edit.php`

### 3. Venue management

Admins can create, edit, and delete venues and their linked rooms from a single screen. The booking form only shows rooms that belong to the selected venue.

Main files:

- `com_mabooking/admin/src/Model/VenuesModel.php`
- `com_mabooking/admin/src/Controller/VenuesController.php`
- `com_mabooking/admin/tmpl/venues/default.php`

### 4. Public booking calendar

The site side exposes a calendar view with:

- monthly calendar navigation
- per-day booking visibility with venue-color markers
- booking request form grouped into date/time, venue, client, and notes sections

The public form posts to `task=booking.submit`.

Main files:

- `com_mabooking/site/src/Controller/BookingController.php`
- `com_mabooking/site/src/Model/CalendarModel.php`
- `com_mabooking/site/forms/booking.xml`
- `com_mabooking/site/tmpl/calendar/default.php`

### 5. Widget layout

The component exposes a lightweight widget layout:

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

### 6. Public ICS feed

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

### 7. Optional Joomla Article sync

When enabled in component options, bookings can create or update Joomla Articles.

Behavior:

- sync can be restricted to selected statuses
- target article category is configurable
- article publish state is configurable
- if a booking status no longer qualifies for sync, the linked article is unpublished
- synced articles keep their body content after creation

Main files:

- `com_mabooking/site/src/Helper/ArticleHelper.php`
- `com_mabooking/admin/src/Model/BookingModel.php`
- `com_mabooking/admin/config.xml`

### 8. Quickicon plugin

`plg_quickicon_mabooking` adds a shortcut tile to the Joomla Administrator home dashboard pointing to the MA Booking component.

Main files:

- `plg_quickicon_mabooking/src/Extension/Mabooking.php`
- `plg_quickicon_mabooking/services/provider.php`
- `plg_quickicon_mabooking/mabooking.xml`

## Data Model

The installer creates three tables.

### `#__mabooking_venues`

Key fields: `id`, `title`, `alias`, `description`, `ordering`, `state`

### `#__mabooking_spaces`

Key fields: `id`, `venue_id`, `title`, `alias`, `capacity_min`, `capacity_max`, `size_label`, `details`, `ordering`, `state`

### `#__mabooking_bookings`

Key fields: `event_title`, `booking_date`, `start_time`, `end_time`, `venue_id`, `space_id`, `client_name`, `client_phone`, `client_email`, `attendees`, `status`, `source`, `article_id`, `notes`, `state`

## Seeded Venue Data

The install SQL seeds example venues and spaces immediately after installation:

- `Grand Ballroom` — Section 1, Section 2, Section 3
- `Exhibition Hall` — Exhibition Hall
- `Bougainvillea Room` — Room 1 to Room 6
- `Town Hall` — Main Hall, Level 1

Source: `com_mabooking/admin/sql/install.mysql.utf8.sql`

## Validation And Booking Safety

Both site and admin booking saves enforce:

- valid time format: `HH:MM` or `HH:MM:SS`
- end time must be later than start time
- selected room must belong to the selected venue
- no overlapping booking in the same venue/space/date unless the conflicting booking is `cancelled`

To reduce race conditions, the save logic acquires a MySQL named lock keyed by venue ID, space ID, and booking date before insert or update.

## Component Options

Configured via `Components -> MA Booking -> Options`.

### Articles

- `enable_article_sync`
- `article_sync_statuses`
- `article_category_id`
- `article_state`

### ICS

- `enable_public_ics`
- `public_ics_confirmed_only`

## Install And Use

### Recommended: install the full package

1. Go to Joomla Administrator.
2. Open `System -> Install -> Extensions`.
3. Upload `pkg_mabooking.zip`.
4. Joomla installs the component and the quickicon plugin in one step.
5. Open `Components -> MA Booking`.

### Component only

Upload `com_mabooking.zip` if you do not need the quickicon plugin.

### Basic setup after install

1. Review seeded venues and spaces, or add your own via `Components -> MA Booking -> Venues`.
2. Open `Options` to enable article sync and/or the public ICS feed.
3. Open `Widgets` to copy the iframe embed code or widget URL.
4. Create a Joomla menu item pointing to the calendar view for a public navigation entry.

## Upgrade Notes

The manifest includes Joomla update schema support:

- `0.2.0.sql`
- `0.4.3.sql`
- `0.4.4.sql`
- `0.4.6.sql`

Current manifest version: `0.4.6`

### Missing Table Repair in `0.4.6`

Version `0.4.6` addresses an installation/update failure where administrator submenu pages could show:

```text
1146 Table '<database>.#__mabooking_bookings' doesn't exist
```

This can happen when Joomla has an existing `com_mabooking` extension record and treats the upload as an upgrade, but the component tables are missing because a prior install did not run the install SQL, was interrupted, or the tables were manually removed. In that state, the admin dashboard, bookings, venues, and widgets views query `#__mabooking_bookings`, `#__mabooking_venues`, or `#__mabooking_spaces` before the tables exist.

The `0.4.6` package adds two repair paths:

- root-level `script.php`, registered through `<scriptfile>script.php</scriptfile>`, creates the MA Booking tables during install, update, and discover install
- `admin/sql/updates/mysql/0.4.6.sql` creates the same tables with `CREATE TABLE IF NOT EXISTS` during Joomla schema updates

After installing `pkg_mabooking.zip` version `0.4.6`, the following tables should exist with the site database prefix:

- `#__mabooking_bookings`
- `#__mabooking_spaces`
- `#__mabooking_venues`

If the error persists after installing `0.4.6`, verify that the uploaded package is the rebuilt `pkg_mabooking.zip`, then check whether the Joomla database user has `CREATE TABLE` permission.

## Uninstall Behavior

Uninstall intentionally preserves booking data. The uninstall SQL does not drop tables.

Preserved tables: `#__mabooking_bookings`, `#__mabooking_spaces`, `#__mabooking_venues`

## Joomla Architecture Summary

- `admin/src/` — administrator controllers, models, views, table classes
- `site/src/` — public controllers, models, views
- `forms/*.xml` — Joomla form field definitions
- `tmpl/` — rendered layouts
- `services/provider.php` — wires component services into Joomla DI container
- `com_mabooking.xml` — declares install, update, languages, files, admin menu

Admin submenu entries:

- `Dashboard`
- `Bookings`
- `Venues`
- `Widgets`

## Important Implementation Notes

- UI styling is embedded directly inside each view template.
- All admin templates include a footer credit: `© Developed by NQMN`.
- The public booking form stores requests as `pending`.
- Cancelled bookings are excluded from the public calendar and ICS feed.
- ICS entries are generated dynamically, not stored as files.
- Article sync uses Joomla content tables and stores the linked content ID back into the booking record.

## Packaging Notes

ZIP files in this repository are created using the .NET `ZipArchive` API to ensure forward-slash entry names and no wrapping folder, both required for Joomla installation on Linux servers. See `compress_zip.md` for details.

## Relevant Entry Points

- Manifest: `com_mabooking/com_mabooking.xml`
- Admin bootstrap: `com_mabooking/admin/mabooking.php`
- Site bootstrap: `com_mabooking/site/mabooking.php`
- Public calendar: `index.php?option=com_mabooking&view=calendar`
- Public widget: `index.php?option=com_mabooking&view=calendar&layout=widget&tmpl=component`
- Public ICS feed: `index.php?option=com_mabooking&task=calendar.ics`

## Notes For Future Development

- email notifications on booking submission or status change
- recurring bookings
- frontend filtering by venue or room
- stronger permission rules per booking action
- richer ICS timezone handling
- automated tests
