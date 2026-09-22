# HostelMS Vercel App

This is the JavaScript/Next.js migration of the PHP hostel management system.

## Run locally

```bash
npm install
npm run dev
```

Open http://localhost:3000.

Demo access:

- Admin: `admin` / `admin123`
- Resident: `STU-1002` / `9876543211`

The current migration uses browser storage for demo data. For production multi-user data, connect the API layer to a hosted MySQL/Postgres database and replace the demo authentication.
