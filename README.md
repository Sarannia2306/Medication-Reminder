# MediTrack

MediTrack is a lightweight medication reminder and tracking prototype for patients and caregivers. It focuses on clear UX, accessibility, and offline-friendly data via localStorage.

## Features

- **Medication Management**
  - Add, edit, delete medications per patient
  - Frequencies: Once, Twice, Thrice daily; Every Morning (08:00), Afternoon (14:00), Night (21:00)
  - Mark doses as Taken/Missed and view adherence
- **Patients**
  - Create and manage patients
  - Patient-specific medication lists
- **Caregiver Dashboard**
  - Switch active patient
  - KPI cards: Taken, Missed, Total, Adherence rate
- **Accessibility & Preferences**
  - Light/Dark theme, Font size segmented control
  - High contrast mode
  - Notification preferences (simulated)
- **Pages**
  - Home, Patients, Add Medication, Medication List, Caregiver Dashboard, Settings, Profile, About, Help, Contact

## Tech Stack

- PHP (for templating/includes only; no server DB)
- Vanilla JavaScript (business logic in `script.js`)
- CSS (`styles.css`), responsive layout
- Data persistence: `localStorage` (keys: `meditrack:*`)

## Project Structure

```
Medication App/
├─ index.php                    # Home
├─ patients.php                 # Patients list + add/edit
├─ add_medication.php           # Add/Edit medication
├─ medication_list.php          # List/mark medications
├─ caregiver_dashboard.php      # Caregiver KPIs + patient switcher
├─ caregiver_profile.php        # Caregiver profile + logout
├─ settings.php                 # Theme, font, contrast, notifications
├─ about.php                    # About
├─ help.php                     # Help
├─ contact.php                  # Contact form (simulated submit)
├─ includes/
│  ├─ header.php                # Shared header/nav
│  └─ footer.php                # Shared footer/modal/toast
├─ script.js                    # App logic & routing
├─ styles.css                   # Styles (Light/Dark/High Contrast)
└─ README.md
```

## Getting Started (Local)

1. **Prerequisites**
   - PHP runtime (e.g., XAMPP, WAMP, or PHP CLI). No DB required.
2. **Clone / Place** the project under your web root (e.g., `htdocs/Medication App`).
3. **Run** using XAMPP/Apache (or `php -S localhost:8000` inside the project folder).
4. **Open** your browser at `http://localhost/Medication%20App/` (or your configured host).

## Usage Tips

- Use **Patients** to add a patient first; then add meds that are scoped to the active patient.
- In **Add Medication**, select a frequency:
  - Every Morning → 08:00, Afternoon → 14:00, Night → 21:00
  - Otherwise, times are derived from the base time and frequency
- **Settings** controls persist in `meditrack:prefs` and apply immediately.
- All data is local to your browser for this prototype (no login/backend required).

## Data Model (localStorage)

- `meditrack:prefs` — theme, font, contrast, notifications, active patient
- `meditrack:patients` — array of patients `{ id, name, age, condition, contact }`
- `meditrack:medications_<patientId>` — medications array per patient
- `meditrack:caregiver_profile` — caregiver profile data
- `meditrack:caregiver_session` — simple session flag

Medication shape:
```
{
  id, name, dosage,
  times: ["HH:MM", ...],
  meal: "Before Meal|After Meal|With Meal",
  frequency: "Once Daily|Twice Daily|Thrice Daily|Every Morning|Every Afternoon|Every Night",
  history: [{ date:"YYYY-MM-DD", time:"HH:MM", status:"taken|missed" }]
}
```

## Accessibility

- Keyboard focus visible states
- High contrast mode via `html.high-contrast`
- Semantic headings and labels, larger tap targets

## Development Notes

- Routing is client-side using `document.body.dataset.page` set per page. See `routeRefresh()` in `script.js`.
- Avoid hard refresh after edits to JS/CSS when cache-busting query strings are present (already added).
- This is a coursework/demo app; do not use in production without proper auth, backend, and validations.

## License

MIT (for coursework/demo purposes).
