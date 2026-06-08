# Deploy Frontend on Vercel & Backend on Supabase

## Overview
This guide shows how to host a **frontend (FE)** on **Vercel** and use **Supabase** as a lightweight backend‑as‑a‑service (BaaS) for authentication, storage, and simple API endpoints. No persistent database is required for the demo – Supabase will handle the minimal server‑side logic and any temporary data.

---
### 1️⃣ Create a Supabase Project
1. Go to https://app.supabase.com and click **"New project"**.
2. Choose a name (e.g., `demo‑fe‑supabase`).
3. Select the **Free tier** – it includes Auth, Storage, Edge Functions, and a Postgres instance (you can ignore the database if you don’t need persistent tables).
4. After the project is ready, note the **`SUPABASE_URL`** and **`SUPABASE_ANON_KEY`** from **Settings → API**. These will be used as environment variables in Vercel.

---
### 2️⃣ (Optional) Add a Simple Edge Function
If you need a tiny server‑side endpoint, you can use Supabase Edge Functions:
```bash
# Install the Supabase CLI (once)
npm i -g supabase
# Initialise functions folder inside your repo
supabase functions new hello-world
```
Edit `functions/hello-world/index.ts`:
```ts
export default async (req, res) => {
  return res.json({ message: "Hello from Supabase Edge!" });
};
```
Deploy with:
```bash
supabase functions deploy hello-world
```
The function will be reachable at `https://<project-ref>.functions.supabase.co/hello-world`.

---
### 3️⃣ Prepare the Frontend Repository
1. Create a new repo (GitHub, GitLab, etc.) for your FE project – e.g., a **React** or **Next.js** app.
2. Add the Supabase client library:
```bash
npm install @supabase/supabase-js
```
3. Initialise the Supabase client (e.g., in `src/lib/supabase.js`):
```js
import { createClient } from "@supabase/supabase-js";

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL;
const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY;
export const supabase = createClient(supabaseUrl, supabaseAnonKey);
```
4. Use the client in your components for auth, storage, or calling the Edge Function.

---
### 4️⃣ Deploy the Frontend to Vercel
1. Push the repo to GitHub (or your preferred Git host).
2. Sign in to https://vercel.com and click **"New Project"**.
3. Import the repository you just pushed.
4. Vercel will automatically detect the framework (Next.js, React, etc.) and set up the build.
5. **Add Environment Variables**:
   - `NEXT_PUBLIC_SUPABASE_URL` → value from Supabase **URL**.
   - `NEXT_PUBLIC_SUPABASE_ANON_KEY` → value from Supabase **anon key**.
   *(If you used a plain HTML/JS site, use `VITE_` or plain `REACT_APP_` prefixes accordingly.)*
6. Click **Deploy**. Vercel will build and publish a live URL (e.g., `https://my-demo.vercel.app`).

---
### 5️⃣ Verify the Integration
- Open the Vercel URL.
- Test authentication (sign‑up / sign‑in) using Supabase Auth.
- If you deployed the Edge Function, call it from the FE:
```js
const fetchHello = async () => {
  const { data } = await fetch("https://<project-ref>.functions.supabase.co/hello-world");
  console.log(await data.json());
};
```
- Ensure the request succeeds and returns the JSON `{ message: "Hello from Supabase Edge!" }`.

---
### 6️⃣ Useful Links
- **Vercel Docs** – https://vercel.com/docs
- **Supabase Quickstart** – https://supabase.com/docs/guides/getting-started
- **Supabase Edge Functions** – https://supabase.com/docs/guides/functions

---
## TL;DR
1. **Supabase**: create project → copy `URL` & `ANON KEY`.
2. **Frontend**: add `@supabase/supabase-js`, configure client with env vars.
3. **Vercel**: import repo, set the two env vars, deploy.
4. Test!

Enjoy a zero‑maintenance, free‑tier showcase that lives on Vercel with a fully‑managed backend on Supabase.
