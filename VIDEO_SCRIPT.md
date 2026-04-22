# Video Walkthrough Script
**Durasi target: 6–8 menit**
**Upload ke:** YouTube (Unlisted)

---

## 🎬 Opening (0:00 – 0:30)

> "Halo, saya Khairul Umam Albi. Di video ini saya akan mendemonstrasikan solusi saya untuk technical task dari PT Dakwah Digital Network, yaitu **AI Sales Page Generator** — sebuah aplikasi Laravel yang mengubah informasi produk menjadi landing page penjualan lengkap, dibantu oleh Google Gemini AI."

**Show:** Buka tab browser ke URL production (`https://salespage.yourdomain.com`)

---

## 🏗️ Tech Stack Overview (0:30 – 1:15)

> "Stack yang saya pakai: Laravel 13 dengan PHP 8.3, MySQL sebagai database, Tailwind CSS untuk styling, dan Google Gemini untuk AI-nya. Saya pilih Gemini karena punya free tier yang cukup generous dan mendukung structured JSON output secara native, jadi output AI-nya selalu terstruktur dan bisa diandalkan."

**Show:** Code editor, buka file `config/gemini.php` dan `.env.example`

---

## 🔐 Authentication (1:15 – 1:45)

> "Fitur pertama: autentikasi. Saya pakai Laravel Breeze karena sudah include register, login, logout, dan password reset. Mari saya tunjukkan."

**Show:** Demo register → login → tampilan dashboard

---

## ✍️ Create Form (1:45 – 2:45)

> "Ini form input utama. User mengisi nama produk, deskripsi, fitur-fitur (dipisah koma), target audience, harga, dan unique selling points. User juga bisa pilih salah satu dari 3 template design: Modern, Minimalist, atau Bold."

**Show:** Isi form dengan data real — contoh: kursus digital marketing, atau produk skincare

> "Perhatikan, semua field divalidasi dengan Laravel validation, dan tombol Generate punya loading state supaya user tahu AI sedang bekerja."

---

## 🤖 AI Generation — The Core (2:45 – 4:15)

> "Sekarang bagian intinya. Saat user submit, request masuk ke `SalesPageController@store`, yang memanggil `GeminiService`."

**Show:** Buka `app/Services/GeminiService.php`, scroll ke method `buildPrompt()`

> "Di prompt, saya melakukan 3 hal penting:
> 1. Saya assign role 'expert direct-response copywriter' supaya outputnya punya voice profesional.
> 2. Saya instruksikan perbedaan benefit vs feature — benefits harus outcome-focused, features harus capability-focused. Ini bikin outputnya tidak generic.
> 3. Saya minta language matching — kalau input Bahasa Indonesia, output juga Bahasa Indonesia."

**Show:** Scroll ke method `salesPageSchema()`

> "Yang paling penting: saya pakai Gemini's `responseSchema` feature. Ini memaksa model mengembalikan JSON dengan struktur yang saya tentukan — headline, sub-headline, array benefits, array testimonials, dan seterusnya. Jadi saya tidak perlu parsing text dengan regex yang brittle, outputnya selalu valid JSON."

**Show:** Kembali ke browser, tunggu loading selesai (10-30 detik)

---

## 🎨 Live Preview (4:15 – 5:30)

> "Dan inilah hasilnya — sales page lengkap, fully styled, siap publish. Bukan text preview dengan label, tapi landing page sungguhan."

**Show:** Scroll dari atas ke bawah: hero, product description, benefits cards, features breakdown, testimonials dengan star rating, pricing card, CTA

> "Setiap section digenerate AI: headline yang hook, benefits yang outcome-focused, testimonials yang realistis dengan nama dan role, dan CTA yang action-oriented."

---

## 🔄 Bonus: Section Regeneration (5:30 – 6:15)

> "Fitur bonus pertama: section-by-section regeneration. Kalau user tidak suka headline-nya saja, dia tidak perlu regenerate seluruh page."

**Show:** Klik tombol "Headline" di toolbar → loading → headline berubah, section lain tetap sama

> "Di backend, saya kirim existing content sebagai context ke Gemini, lalu minta hanya key tertentu yang di-update. Hasilnya di-merge balik ke JSON."

---

## 📥 Bonus: Export HTML (6:15 – 6:45)

> "Fitur bonus kedua: export as standalone HTML."

**Show:** Klik tombol Export HTML → file ter-download → buka file → tampil di browser tanpa server

> "File HTML-nya self-contained dengan Tailwind di-inline via CDN, jadi bisa langsung di-upload ke hosting manapun."

---

## 📚 History & Search (6:45 – 7:15)

> "Semua generated pages tersimpan di database. User bisa search, edit untuk regenerate dengan input baru, atau delete."

**Show:** Ke `/pages`, demo search, klik edit → ubah input → regenerate

---

## 🚀 Deployment (7:15 – 7:45)

> "Aplikasi ini di-deploy ke VPS Ubuntu dengan Caddy sebagai reverse proxy. Caddy auto-issue SSL certificate dari Let's Encrypt. Database MySQL berjalan lokal di server yang sama."

**Show:** SSH ke server, `cat /etc/caddy/Caddyfile`, `systemctl status caddy`

---

## ✅ Closing (7:45 – 8:00)

> "Semua requirement task sudah terpenuhi: autentikasi, form input, AI generation, structured output, saved pages, live preview. Plus 3 bonus: export HTML, multiple templates, dan section regeneration.
>
> Link aplikasi, source code, dan dokumentasi sudah saya kirim via email. Terima kasih sudah memberi kesempatan untuk mengerjakan task ini!"

---

## 🎥 Recording Tips

- **Screen recorder:** OBS Studio (gratis) atau Loom
- **Audio:** pastikan microphone jernih, rekam di ruangan yang tidak gema
- **Resolusi:** 1080p minimum
- **Mouse cursor:** highlight/zoom saat klik tombol penting
- **Editing:** cut bagian loading yang lama jadi fast-forward 2–3x
- **Intro:** bisa tambahkan title card 2 detik: "AI Sales Page Generator — Technical Task Submission"

## 📧 Email Template untuk Submission

```
Subject: Technical Task Submission — AI Sales Page Generator — Khairul Umam Albi

Assalamu'alaikum Wr. Wb.
Dear PT Dakwah Digital Network Team,

Bersama email ini saya, Khairul Umam Albi, menyampaikan hasil technical task
untuk posisi yang saya lamar. Saya memilih **Option B: AI Sales Page Generator**.

Berikut materi submission:

1. Live Application: https://salespage.yourdomain.com
   Test account:
   - Email: demo@example.com
   - Password: demo12345

2. Video Walkthrough (YouTube, Unlisted):
   https://youtu.be/xxxxx

3. Source Code Repository:
   https://github.com/yourusername/sales-page-generator

4. Written Documentation:
   Terlampir (DOCUMENTATION.md)

Informasi kontak:
- Nama: Khairul Umam Albi
- WhatsApp: +62 822-8210-3998
- Email: albikhairul212@gmail.com

Terima kasih atas kesempatan yang diberikan. Saya siap menjawab pertanyaan
lebih lanjut jika diperlukan.

Hormat saya,
Khairul Umam Albi
```
