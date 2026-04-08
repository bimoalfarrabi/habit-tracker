
# Habit Tracker - Laravel 12 Documentation: Blade Layout, Page Flow & UI Design System

## 1. Tujuan Dokumen
Dokumen ini melanjutkan blueprint teknis **Habit Tracker Laravel 12** dengan fokus pada:
- struktur Blade layout
- page flow per modul
- UI architecture
- adaptasi visual dari dokumen desain referensi
- komponen UI utama
- layout dashboard, habit, focus timer, dan notifications
- aturan styling untuk implementasi Tailwind CSS

Dokumen ini diasumsikan berjalan di stack:
- Laravel 12
- PHP 8.2
- MySQL
- Vite
- Tailwind CSS
- shared hosting friendly
- notifikasi berbasis cron
- tanpa Reverb

Arah UI di dokumen ini mengambil inspirasi visual dari design system yang kamu upload: nuansa **warm parchment**, tipografi editorial, warna terracotta, neutral hangat, radius lembut, dan ring shadow yang halus. fileciteturn1file0

Dokumen ini juga tetap selaras dengan workflow frontend modern berbasis `resources/`, Vite, Tailwind, dan output asset build yang rapi. fileciteturn0file0

---

## 2. Design Direction

## 2.1 Karakter visual utama
Habit Tracker ini **tidak** akan memakai gaya dashboard teknikal yang dingin.  
Sebaliknya, UI akan dibangun dengan rasa:
- hangat
- tenang
- editorial
- reflektif
- human-centered

Mood visual yang dituju:
- seperti jurnal premium
- seperti planner pribadi yang rapi
- seperti ruang kerja tenang, bukan panel admin perusahaan

Ini cocok untuk produk habit tracker karena:
- habit identik dengan konsistensi dan refleksi
- fokus visual harus menenangkan, bukan agresif
- user akan sering membuka halaman yang sama setiap hari

---

## 2.2 Prinsip visual yang diadopsi
Dari dokumen desain referensi, prinsip yang paling relevan untuk aplikasi ini adalah: fileciteturn1file0

- gunakan **parchment background** yang terasa seperti kertas premium
- gunakan hierarki **serif untuk headline** dan **sans untuk UI text**
- gunakan **terracotta** sebagai primary accent, bukan warna tech yang dingin
- semua neutral harus terasa **hangat**, bukan abu kebiruan
- depth lebih banyak dibangun dengan **ring shadow** dan border cream tipis
- radius dibuat lembut dan cukup besar agar terasa ramah
- spacing dibuat lega agar pengalaman terasa seperti membaca jurnal, bukan spreadsheet

---

## 2.3 Adaptasi untuk Habit Tracker
Karena produk kita adalah aplikasi web, bukan product landing page, maka sistem visual akan diadaptasi menjadi:

- **headline besar** tetap memakai serif
- **body dan UI** memakai sans yang bersih
- **cards** dipakai sebagai unit utama untuk status, daftar habit, sesi fokus, dan notif
- **layout** lebih modular dan operasional daripada landing page
- **dark section alternation** dipakai seperlunya, bukan di semua halaman
- **illustration-heavy approach** tidak wajib; fokus utama pada layout, color, dan rhythm

---

## 3. Design Tokens

## 3.1 Color palette utama
Berikut palet yang direkomendasikan, diambil dan diadaptasi dari dokumen desain referensi. fileciteturn1file0

### Background & Surface
- `bg-page`: `#f5f4ed` — parchment utama
- `bg-card`: `#faf9f5` — ivory untuk card
- `bg-white-soft`: `#ffffff` — putih murni hanya untuk elemen tertentu
- `bg-sand`: `#e8e6dc` — warm sand untuk button sekunder
- `bg-dark`: `#141413` — near black hangat
- `bg-dark-soft`: `#30302e` — charcoal warm untuk dark card

### Text
- `text-primary`: `#141413`
- `text-secondary`: `#5e5d59`
- `text-muted`: `#87867f`
- `text-dark-link`: `#3d3d3a`
- `text-on-dark`: `#b0aea5`

### Accent
- `accent-primary`: `#c96442` — terracotta
- `accent-secondary`: `#d97757`
- `accent-danger`: `#b53333`
- `accent-focus`: `#3898ec`

### Border & Ring
- `border-light`: `#f0eee6`
- `border-strong`: `#e8e6dc`
- `border-dark`: `#30302e`
- `ring-warm`: `#d1cfc5`
- `ring-deep`: `#c2c0b6`

---

## 3.2 Tipografi
Dokumen referensi menekankan penggunaan serif untuk authority dan sans untuk utility. Adaptasi untuk Habit Tracker: fileciteturn1file0

### Font recommendation
Karena font asli referensi bersifat custom, implementasi praktis:
- **Headline serif**: `Georgia`, `ui-serif`, `serif`
- **Body/UI sans**: `Inter`, `system-ui`, `sans-serif`
- **Code/mono**: `ui-monospace`, `SFMono-Regular`, `monospace`

### Typographic roles
- **App Hero / page heading**: serif, 40–52px, medium
- **Section heading**: serif, 30–36px
- **Card heading**: serif, 22–28px
- **Body intro**: sans, 18–20px
- **Body normal**: sans, 15–16px
- **Label/caption**: sans, 12–14px

### Typography principles
- serif hanya untuk headline penting
- jangan gunakan bold berat berlebihan untuk serif
- body text diberi line-height lega
- text muted harus tetap mudah dibaca, jangan terlalu pudar
- angka statistik besar boleh pakai serif atau sans semibold, tergantung konteks

---

## 3.3 Radius & elevation
Mengikuti referensi, aplikasi memakai radius lembut dan bayangan minimalis. fileciteturn1file0

### Radius scale
- `rounded-sm`: 8px
- `rounded-md`: 12px
- `rounded-lg`: 16px
- `rounded-xl`: 24px
- `rounded-2xl`: 32px

### Shadow system
Gunakan dua pola utama:
- **ring shadow**: `0 0 0 1px`
- **whisper shadow**: `0 4px 24px rgba(0,0,0,0.05)`

### Prinsip
- hindari shadow hitam berat
- gunakan border cream + ring warm
- hover state cukup naik sedikit, jangan terlalu flashy

---

## 4. Tailwind Theme Planning

## 4.1 Kenapa perlu theme custom
Agar implementasi konsisten, warna dan font sebaiknya masuk ke `tailwind.config.js`, bukan ditulis inline terus-menerus.

## 4.2 Contoh theme extension
```js
export default {
  theme: {
    extend: {
      colors: {
        parchment: '#f5f4ed',
        ivory: '#faf9f5',
        sand: '#e8e6dc',
        ink: '#141413',
        charcoal: '#30302e',
        warmText: '#5e5d59',
        mutedText: '#87867f',
        terracotta: '#c96442',
        terracottaSoft: '#d97757',
        borderCream: '#f0eee6',
        ringWarm: '#d1cfc5',
        dangerWarm: '#b53333',
        focusBlue: '#3898ec',
      },
      fontFamily: {
        serifDisplay: ['Georgia', 'ui-serif', 'serif'],
        sansBody: ['Inter', 'system-ui', 'sans-serif'],
        monoUI: ['ui-monospace', 'SFMono-Regular', 'monospace'],
      },
      boxShadow: {
        ringWarm: '0 0 0 1px #d1cfc5',
        whisper: '0 4px 24px rgba(0,0,0,0.05)',
      },
      borderRadius: {
        soft: '12px',
        card: '16px',
        hero: '32px',
      },
    },
  },
}
```

---

## 4.3 Class naming convention
Agar Blade tetap bersih, bisa gunakan:
- reusable partial
- Blade components
- atau utility class custom di `resources/css/app.css`

Contoh semantic helper class:
- `.page-shell`
- `.page-title`
- `.section-title`
- `.card-soft`
- `.btn-primary-warm`
- `.btn-secondary-warm`
- `.metric-card`
- `.list-card`

---

## 5. Blade Layout Architecture

## 5.1 Struktur file yang direkomendasikan
```text
resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   ├── guest.blade.php
│   │   └── partials/
│   │       ├── head.blade.php
│   │       ├── navbar.blade.php
│   │       ├── sidebar.blade.php
│   │       ├── page-header.blade.php
│   │       ├── flash-message.blade.php
│   │       └── footer.blade.php
│   ├── components/
│   │   ├── button.blade.php
│   │   ├── card.blade.php
│   │   ├── metric-card.blade.php
│   │   ├── habit-card.blade.php
│   │   ├── notification-item.blade.php
│   │   └── empty-state.blade.php
│   ├── dashboard/
│   │   └── index.blade.php
│   ├── habits/
│   │   ├── index.blade.php
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   └── show.blade.php
│   ├── notifications/
│   │   └── index.blade.php
│   ├── focus-sessions/
│   │   └── index.blade.php
│   └── auth/
│       ├── login.blade.php
│       └── register.blade.php
```

---

## 5.2 Layout utama
### `layouts/app.blade.php`
Dipakai untuk halaman setelah login.

Berisi:
- navbar top
- optional sidebar desktop
- flash message
- content container
- slot scripts per page

### Struktur konseptual
```blade
<!DOCTYPE html>
<html lang="en">
  <head>
    @include('layouts.partials.head')
  </head>
  <body class="bg-parchment text-ink font-sansBody">
    <div class="min-h-screen">
      @include('layouts.partials.navbar')

      <div class="mx-auto max-w-7xl px-4 py-6 lg:px-8">
        @include('layouts.partials.flash-message')
        @yield('content')
      </div>
    </div>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('scripts')
  </body>
</html>
```

### Karakter layout
- background parchment
- container lebar tapi tetap editorial
- padding vertikal lega
- navbar tidak terlalu tinggi
- tidak terlalu banyak garis keras

---

## 5.3 Layout guest
### `layouts/guest.blade.php`
Dipakai untuk:
- login
- register
- forgot password jika nanti ada

Karakter:
- lebih minimal
- card auth di tengah
- heading serif
- body sans
- CTA terracotta

---

## 6. Navigation Design

## 6.1 Navbar utama
### Elemen navbar
- logo / app name: **Ritme** atau nama app pilihanmu
- link: Dashboard, Habits, Focus, Notifications
- profile dropdown
- unread notifications badge kecil
- optional quick-add habit button

### Style
- background: parchment / ivory tipis
- border bawah: border cream
- text: ink + warmText
- CTA kecil pakai terracotta
- sticky boleh, tapi tetap subtil

### Rekomendasi visual
- tinggi navbar sekitar 68–76px
- logo pakai serif medium
- link pakai sans 15–16px
- active state bisa underline halus atau pill sand background

---

## 6.2 Sidebar optional
Untuk desktop, sidebar bisa dipakai kalau ingin layout lebih “app-like”.

### Jika pakai sidebar
Kelebihan:
- navigasi lebih jelas
- dashboard terasa seperti produk penuh

Kekurangan:
- sedikit mengurangi nuansa editorial bersih

### Rekomendasi
Untuk versi awal:
- **pakai top navbar saja**
- sidebar bisa ditambahkan nanti jika modul bertambah banyak

---

## 7. Global Component Design

## 7.1 Button system

### Primary button
Digunakan untuk:
- simpan habit
- start focus
- create habit
- mark as read jika penting

Style:
- background: terracotta
- text: ivory
- radius: 12px
- ring halus
- hover: sedikit lebih gelap / lebih pekat

### Secondary button
Digunakan untuk:
- cancel
- back
- filter ringan
- open modal

Style:
- background: sand
- text: charcoal/ink
- border/ring warm
- hover: sedikit lebih gelap

### Ghost button
Digunakan untuk:
- icon actions
- dropdown item trigger
- mark as read kecil
- pagination light

Style:
- background transparan
- hover ke sand tipis
- text warmText

---

## 7.2 Card system

### Card standard
Untuk:
- habit item
- notifications item
- form sections
- detail info

Style:
- background ivory
- border cream
- radius 16px
- whisper shadow sangat halus

### Metric card
Untuk:
- total habits
- completed today
- streak
- focus minutes

Style:
- background ivory / white-soft
- border cream
- radius 16–24px
- angka besar
- label kecil muted
- accent line / icon kecil opsional

### Dark card
Untuk:
- focus timer hero
- important highlight
- daily reflection / summary

Style:
- background dark
- text ivory / warm silver
- border dark
- radius besar 24–32px

---

## 7.3 Form system

### Input
- bg: white-soft / ivory
- border: border-strong
- radius: 12px
- focus ring: focus blue
- label: sans 14px medium
- helper text: muted 12–13px

### Textarea
Sama dengan input, tetapi:
- padding lebih besar
- line-height lebih lega

### Select
- gunakan warna warm, jangan default browser jika bisa di-style
- ikon dropdown lembut
- hindari abu dingin

---

## 7.4 Empty state
Setiap modul utama perlu empty state yang bagus:
- habits kosong
- notifications kosong
- focus session kosong
- dashboard belum ada data

Karakter:
- card ivory
- icon sederhana
- heading serif kecil
- body sans muted
- CTA primer yang jelas

---

## 8. Page Flow & UI Planning

## 8.1 Auth Pages

### Login page
#### Tujuan
Masuk ke aplikasi dengan pengalaman yang tenang dan premium.

#### Layout
- full page parchment background
- auth card di tengah
- page title serif
- subtitle singkat
- email & password input
- login button terracotta
- link register di bawah

#### Komponen
- auth logo/title
- input email
- input password
- remember me
- forgot password optional
- CTA login

#### Visual notes
- card tidak terlalu sempit
- jarak antar elemen lega
- hindari visual noise
- tambahkan kalimat ringan seperti:
  - “Bangun ritme kecil setiap hari.”

---

### Register page
Mirip login, tetapi:
- ada input nama
- konfirmasi password
- CTA create account

---

## 8.2 Dashboard Page

### Tujuan
Menjadi halaman ringkasan utama saat user masuk.

### Susunan blok
1. page header
2. quick metrics
3. today habits
4. focus session panel
5. latest notifications
6. optional weekly progress chart

### Page header
Konten:
- greeting
- page title serif
- short daily message
- quick action button “Tambah Habit”

Contoh:
- “Selamat pagi, Bimo”
- “Mari jaga ritmemu hari ini.”

### Quick metrics section
Isi:
- total habit aktif
- completed today
- current streak
- focus minutes today

Layout:
- grid 2 kolom mobile
- grid 4 kolom desktop

### Today habits section
Menampilkan:
- daftar habit aktif hari ini
- status completed / pending
- button check-in cepat

Layout:
- list card
- setiap habit card punya:
  - warna accent kecil
  - judul serif kecil
  - deskripsi singkat
  - target
  - reminder time
  - aksi check-in

### Focus session panel
Bisa dibuat sebagai dark feature card:
- timer state
- tombol start/stop
- current session info
- focused vs unfocused info kecil

### Notifications preview
Menampilkan:
- 5 notif terbaru
- badge unread
- link “Lihat semua”

### Weekly chart
Gunakan chart simpel:
- 7 hari terakhir
- completion bar / line chart
- warna terracotta soft + neutral hangat

---

## 8.3 Habits Index Page

### Tujuan
Mengelola semua habit user.

### Susunan
1. page header + create button
2. filter bar
3. habits grid/list
4. empty state jika kosong

### Filter bar
Opsional awal:
- all
- active
- archived
- daily
- weekly

Style:
- pill button sand / ivory
- active filter pakai terracotta soft atau ink

### Habit card detail
Setiap habit card berisi:
- title
- description singkat
- frequency badge
- target count
- reminder time
- streak kecil
- status aktif/arsip
- edit button
- archive button
- check-in cepat

### Layout recommendation
- list vertical untuk kemudahan scan
- atau grid 2 kolom desktop kalau ingin lebih visual

Untuk MVP, **list card vertical** lebih jelas.

---

## 8.4 Habit Create & Edit Page

### Tujuan
Form input habit yang rapi dan nyaman.

### Struktur form
- title
- description
- frequency
- target_count
- reminder_time
- color/icon optional
- active toggle
- submit button
- cancel button

### Layout recommendation
- form diletakkan dalam card besar
- page title di atas
- form dibagi section kecil jika perlu
- kolom 1 untuk mobile
- kolom 2 untuk desktop pada field tertentu

### Visual tone
- form harus terasa ringan
- jangan terlalu banyak border keras
- beri helper text kecil untuk frequency dan reminder

---

## 8.5 Habit Detail Page

### Tujuan
Menampilkan ringkasan satu habit.

### Isi
- title & description
- metadata: frequency, target, reminder, created date
- current streak
- longest streak
- log history
- completion chart mini
- quick check-in form

### Layout
- hero card kecil di atas
- history di bawah
- mungkin ada sidebar kecil untuk stats jika desktop

---

## 8.6 Focus Session Page

### Tujuan
Memberi ruang khusus untuk timer dan sesi fokus.

### Susunan
1. page header
2. timer hero card
3. session form/config
4. summary hari ini
5. history sessions

### Timer hero card
Ini salah satu bagian paling menonjol di app.

Style rekomendasi:
- dark surface
- angka timer besar
- heading serif kecil
- tombol start/stop dominan
- info pendukung:
  - related habit
  - target duration
  - focused duration
  - unfocused duration
  - interruption count

### UX notes
Karena user bisa pakai multi-monitor:
- timer tidak auto-pause
- tampilkan label:
  - focused
  - background
- beri teks informatif:
  - “Timer tetap berjalan saat halaman tidak aktif, namun background time akan dicatat.”

### Session history
List session sebelumnya:
- tanggal
- habit terkait
- durasi total
- focused duration
- interruptions
- status

---

## 8.7 Notifications Page

### Tujuan
Menampilkan semua notifikasi reminder dan info sistem.

### Susunan
1. page header
2. mark all as read
3. notification list
4. empty state

### Notification item
Isi:
- icon / badge type
- title
- message
- created_at human readable
- status unread/read
- action mark as read

### Visual hierarchy
- unread item lebih menonjol
- read item sedikit lebih muted
- jangan terlalu banyak warna
- type reminder cukup diberi accent terracotta kecil

---

## 9. Blade Components Planning

## 9.1 `components/button.blade.php`
Props:
- `variant`: primary, secondary, ghost, danger
- `size`: sm, md, lg
- `icon`: optional

Tujuan:
- konsistensi semua tombol
- memudahkan perubahan global

---

## 9.2 `components/card.blade.php`
Props:
- `variant`: default, metric, dark, bordered
- `padding`
- `title`
- `actions`

---

## 9.3 `components/metric-card.blade.php`
Props:
- `label`
- `value`
- `hint`
- `icon`
- `accent`

Digunakan di dashboard.

---

## 9.4 `components/habit-card.blade.php`
Props:
- `habit`
- `showActions`
- `showQuickCheckin`

Isi:
- badge warna
- title
- frequency
- target
- reminder
- actions

---

## 9.5 `components/notification-item.blade.php`
Props:
- `notification`

Isi:
- title
- message
- timestamp
- read state
- optional action

---

## 10. Page Header Pattern

Setiap halaman utama sebaiknya punya pola header konsisten:

### Struktur
- eyebrow kecil optional
- page title serif
- short helper text
- action button di kanan

### Contoh per halaman
#### Dashboard
- title: “Dashboard”
- helper: “Ringkasan kecil untuk menjaga ritme hari ini.”

#### Habits
- title: “Habits”
- helper: “Kelola kebiasaan yang ingin kamu jaga.”

#### Focus
- title: “Focus Session”
- helper: “Catat sesi fokus tanpa membuat ritme terasa kaku.”

#### Notifications
- title: “Notifications”
- helper: “Pengingat dan informasi yang relevan untuk hari ini.”

---

## 11. Responsive Behavior

## 11.1 Mobile
- layout 1 kolom
- metric cards 2 kolom
- timer tetap dominan
- navbar jadi lebih ringkas
- actions dipadatkan

## 11.2 Tablet
- dashboard mulai 2 kolom
- form create/edit mulai bisa 2 kolom pada field tertentu
- habit cards tetap list

## 11.3 Desktop
- dashboard 4 metric cards
- section berdampingan jika perlu
- focus page bisa dua kolom
- notification list tetap 1 kolom untuk readability

### Prinsip penting
Walaupun responsif, aplikasi harus tetap terasa:
- editorial
- hangat
- bernapas
- tidak padat berlebihan

---

## 12. Accessibility Notes

### Warna
- pastikan kontras text cukup
- terracotta tidak dipakai untuk body text panjang
- muted text tetap readable

### Focus state
- gunakan focus ring biru yang jelas untuk input dan tombol
- jangan menghapus outline tanpa pengganti

### Ukuran klik
- target minimal 44x44 px
- action kecil seperti mark-as-read tetap cukup besar

### Typografi
- body text minimal 15–16px
- line-height nyaman
- jangan terlalu banyak text caps lock

---

## 13. Frontend Interaction Notes

## 13.1 Flash message
Style:
- success: warm green / neutral success yang halus
- error: warm crimson
- info: sand / ivory border

### Posisi
- di atas content area
- dalam card kecil
- bisa dismissable

---

## 13.2 Notification polling
Jika dipakai:
- update unread badge di navbar
- update list dropdown sederhana
- jangan terlalu agresif
- interval 15–30 detik cukup

---

## 13.3 Focus timer UI state
State utama:
- idle
- running
- completed
- cancelled

Setiap state punya:
- teks status
- warna status
- CTA berbeda

---

## 14. App.css Planning

Di `resources/css/app.css`, selain Tailwind directives, tambahkan utility kecil untuk gaya khas aplikasi:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer base {
  body {
    @apply bg-parchment text-ink font-sansBody;
  }

  h1, h2, h3, h4 {
    font-family: Georgia, ui-serif, serif;
  }
}

@layer components {
  .page-shell {
    @apply mx-auto max-w-7xl px-4 py-6 lg:px-8;
  }

  .page-title {
    @apply font-serifDisplay text-4xl leading-tight text-ink md:text-5xl;
  }

  .page-subtitle {
    @apply mt-2 max-w-2xl text-base leading-7 text-warmText;
  }

  .card-soft {
    @apply rounded-card border border-borderCream bg-ivory shadow-whisper;
  }

  .btn-primary-warm {
    @apply inline-flex items-center justify-center rounded-soft bg-terracotta px-4 py-2 text-sm font-medium text-ivory shadow-ringWarm transition hover:opacity-95;
  }

  .btn-secondary-warm {
    @apply inline-flex items-center justify-center rounded-soft bg-sand px-4 py-2 text-sm font-medium text-ink shadow-ringWarm transition hover:opacity-95;
  }

  .metric-card {
    @apply rounded-card border border-borderCream bg-ivory p-5 shadow-whisper;
  }
}
```

---

## 15. UI Task Breakdown

## Fase 1 - Theme Foundation
- setup color tokens di Tailwind
- setup font family
- setup shared utilities di `app.css`
- buat layout app dan guest

## Fase 2 - Global Components
- button component
- card component
- page header partial
- flash message partial
- empty state component

## Fase 3 - Auth UI
- login page
- register page
- validation error display

## Fase 4 - Dashboard UI
- metric cards
- habits today list
- notifications preview
- focus card mini

## Fase 5 - Habits UI
- habits index
- create/edit form
- habit detail page

## Fase 6 - Focus UI
- timer hero card
- start/stop interaction
- session summary
- history list

## Fase 7 - Notifications UI
- notifications index
- unread badge
- mark as read interaction

## Fase 8 - Polish
- responsive refinement
- hover/focus state refinement
- spacing adjustments
- empty states and loading states

---

## 16. Final UI Direction Summary

UI aplikasi ini harus terasa seperti:
- planner pribadi digital
- jurnal kebiasaan yang elegan
- dashboard yang hangat dan tenang

Bukan seperti:
- admin panel korporat dingin
- aplikasi SaaS penuh warna kontras
- dashboard analytics yang keras

### Kata kunci visual final
- warm
- editorial
- calm
- premium
- readable
- reflective

### Pilar implementasi
1. parchment background
2. serif headline + sans UI
3. terracotta accent
4. ivory cards
5. soft radius
6. warm ring shadows
7. spacious layout rhythm

---

## 17. Langkah Berikut yang Paling Logis
Setelah dokumen ini, opsi lanjutan terbaik adalah:
1. membuat **Blade file skeleton per halaman**
2. membuat **Tailwind component starter code**
3. membuat **wireframe markdown / ASCII layout per halaman**
4. atau langsung membuat **starter Blade + Tailwind implementation**
