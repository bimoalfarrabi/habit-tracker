# Chat Memory

## Context

- Project: `habit-tracker` (Laravel 12, Blade, Tailwind, Alpine).
- Local environment requested by user: XAMPP with PHP binary `/opt/lampp/bin/php`.
- Conversation language: Indonesian.

## Timeline Ringkas

1. User meminta pembuatan proyek web berdasarkan dokumentasi di folder `docs`.
2. User menyampaikan Breeze sudah dijalankan manual, lalu meminta lanjut implementasi.
3. User meminta konfigurasi DB ke MySQL dengan nama database `habit-tracker`.
4. User meminta perbaikan UI form create habit:
   - input `color` tidak cocok sebagai text,
   - posisi bagian `active habit` tidak sejajar.
5. UI create habit diperbarui:
   - `color` menjadi color picker,
   - status active jadi toggle modern dan lebih rapi.
6. User meminta dibuatkan halaman profile.
7. Halaman profile dirombak agar konsisten dengan theme aplikasi:
   - layout baru berbasis `layouts.app`,
   - form info profile, password, dan delete account ditata ulang,
   - link `Profile` ditambahkan ke navbar.
8. User setuju lanjut enhancement profile.
9. Ditambahkan fitur profile lanjutan:
   - header profile dengan avatar inisial + metric cards,
   - tab `Profile Settings` dan `Activity`,
   - upload/hapus foto profile,
   - activity feed dari habit/check-in/focus/notification.
10. User meminta animasi subtle saat navigasi antar halaman.
11. Ditambahkan transisi halaman internal.
12. User melaporkan efek “berkedip” pada halaman tujuan.
13. Transisi diperbaiki menjadi leave-only animation agar tidak flicker di halaman baru.
14. User meminta README diubah.
15. README di-rewrite total sesuai kondisi proyek saat ini.
16. User meminta section API endpoint lebih detail.
17. README ditambahkan dokumentasi endpoint API internal (route, payload, response shape).
18. User meminta panduan deploy shared hosting cPanel.
19. Diberikan langkah deploy runut dari build hingga cron scheduler.
20. User meminta dibuatkan file markdown memori chat (file ini).

## Perubahan Teknis Utama yang Sempat Diterapkan

- Konfigurasi DB lokal ke MySQL (`habit-tracker`) via `.env`.
- Migrasi dijalankan di MySQL XAMPP.
- UI habits form:
  - field color -> input type color,
  - active status -> toggle switch modern.
- Profile:
  - refactor tampilan profile agar konsisten desain aplikasi,
  - statistik profile (habit/focus),
  - foto profile (upload/remove) + storage link,
  - activity tab.
- Added migration:
  - `add_profile_photo_path_to_users_table`.
- Added subtle page transition:
  - awalnya enter+leave,
  - lalu diperbaiki menjadi leave-only untuk menghindari flicker.
- README:
  - diganti dari template default Laravel ke dokumentasi proyek aktual,
  - ditambah section API endpoint detail.

## Command Penting yang Digunakan (contoh)

- Migrate:
  - `/opt/lampp/bin/php artisan migrate`
- Run tests:
  - `/opt/lampp/bin/php artisan test`
- Build assets:
  - `npm run build`
- Storage link:
  - `/opt/lampp/bin/php artisan storage:link`

## Status Akhir

- Fitur utama (habits, focus session, notifications, profile) sudah terintegrasi.
- Profile sudah memiliki upload foto + activity feed.
- Navigasi antar halaman sudah punya animasi subtle tanpa flicker di halaman tujuan.
- README sudah terbarui dan memuat dokumentasi API internal lebih detail.

