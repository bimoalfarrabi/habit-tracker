<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head', ['title' => 'Ritme - Habit & Focus Tracker'])
</head>
<body>
    <div class="relative min-h-screen overflow-hidden bg-parchment">
        <div class="pointer-events-none absolute -left-20 top-0 h-72 w-72 rounded-full bg-terracotta/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-24 top-16 h-80 w-80 rounded-full bg-[#d3bda6]/20 blur-3xl"></div>

        <header class="relative border-b border-borderCream/80 bg-ivory/80 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 lg:px-8">
                <a href="{{ route('home') }}" class="text-3xl font-semibold tracking-wide text-ink">Ritme</a>

                <nav class="hidden items-center gap-4 text-sm text-warmText md:flex">
                    <a href="#preview" data-smooth-scroll class="transition hover:text-ink">Preview</a>
                    <a href="#stories" data-smooth-scroll class="transition hover:text-ink">Stories</a>
                    <a href="#features" data-smooth-scroll class="transition hover:text-ink">Features</a>
                    <a href="#how-it-works" data-smooth-scroll class="transition hover:text-ink">How it works</a>
                </nav>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary-warm">Open Dashboard</a>
                    @else
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-secondary-warm">Log in</a>
                        @endif

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary-warm">Start Free</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <main class="relative mx-auto max-w-7xl px-4 pb-10 pt-8 lg:px-8 lg:pt-12" data-page-transition>
            <section class="grid items-center gap-8 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <span class="badge-soft">{{ $welcomeContent->hero_badge }}</span>
                    <h1 class="mt-4 font-serifDisplay text-5xl leading-[0.95] text-ink md:text-6xl lg:text-7xl">
                        {{ $welcomeContent->hero_title }}<br>
                        <span class="text-terracotta">{{ $welcomeContent->hero_highlight }}</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-warmText md:text-lg">
                        {{ $welcomeContent->hero_description }}
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary-warm">Lanjut ke Dashboard</a>
                        @else
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-primary-warm">{{ $welcomeContent->hero_primary_cta_text }}</a>
                            @endif
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="btn-secondary-warm">{{ $welcomeContent->hero_secondary_cta_text }}</a>
                            @endif
                        @endauth
                    </div>

                    <div class="mt-8 flex flex-wrap gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">
                        <span class="rounded-full bg-ivory px-3 py-1.5">Daily & Weekly Habits</span>
                        <span class="rounded-full bg-ivory px-3 py-1.5">Todo List + Deadline</span>
                        <span class="rounded-full bg-ivory px-3 py-1.5">Focus Timer</span>
                        <span class="rounded-full bg-ivory px-3 py-1.5">Reminder Email + Telegram</span>
                    </div>
                </div>

                <div class="card-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Today Snapshot</p>
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between rounded-soft border border-borderCream bg-ivory p-3">
                            <p class="text-sm text-warmText">Drink Water</p>
                            <span class="rounded-full bg-[#e7f2eb] px-2.5 py-1 text-xs font-semibold text-[#1f6f45]">Completed</span>
                        </div>
                        <div class="flex items-center justify-between rounded-soft border border-borderCream bg-ivory p-3">
                            <p class="text-sm text-warmText">30 min Reading</p>
                            <span class="rounded-full bg-[#f2ecdf] px-2.5 py-1 text-xs font-semibold text-[#7b6232]">In Progress</span>
                        </div>
                        <div class="rounded-soft border border-borderCream bg-charcoal p-4 text-ivory">
                            <p class="text-xs uppercase tracking-[0.14em] text-[#d0cbc1]">Focus Session</p>
                            <p class="mt-2 text-4xl font-semibold">24:18</p>
                            <p class="mt-1 text-sm text-[#d0cbc1]">Stay with one task at a time.</p>
                        </div>
                        <div class="rounded-soft border border-borderCream bg-ivory p-3">
                            <p class="text-xs uppercase tracking-[0.14em] text-mutedText">Notification Settings</p>
                            <p class="mt-1 text-sm text-warmText">Email: ON · Telegram: ON</p>
                            <p class="mt-1 text-xs text-mutedText">Chat ID dan bot token tersimpan aman per akun.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="preview" class="mt-12 scroll-mt-24 rounded-hero border border-borderCream bg-[#f8f5ee] p-5 md:p-7">
                <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-end">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Product Preview</p>
                        <h2 class="mt-2 text-4xl text-ink">{{ $welcomeContent->preview_title }}</h2>
                        <p class="mt-2 max-w-2xl text-sm text-warmText">{{ $welcomeContent->preview_description }}</p>
                    </div>
                    <span class="badge-soft">Desktop + Mobile Ready</span>
                </div>

                <div class="grid gap-4 lg:grid-cols-[1.45fr_0.55fr]">
                    <div class="overflow-hidden rounded-card border border-borderCream bg-ivory shadow-whisper">
                        <div class="flex items-center justify-between border-b border-borderCream bg-[#f1eee5] px-4 py-2.5">
                            <div class="flex items-center gap-1.5">
                                <span class="h-2.5 w-2.5 rounded-full bg-[#d77f69]"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#d7ba69]"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-[#88b07c]"></span>
                            </div>
                            <p class="text-xs font-medium text-mutedText">ritme.app/dashboard</p>
                        </div>

                        <div class="space-y-4 p-4 md:p-5">
                            <div class="grid gap-3 sm:grid-cols-4">
                                <div class="rounded-soft border border-borderCream bg-white p-3">
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-mutedText">Active</p>
                                    <p class="mt-2 text-2xl font-semibold text-ink">6</p>
                                </div>
                                <div class="rounded-soft border border-borderCream bg-white p-3">
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-mutedText">Completed</p>
                                    <p class="mt-2 text-2xl font-semibold text-ink">4</p>
                                </div>
                                <div class="rounded-soft border border-borderCream bg-white p-3">
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-mutedText">Streak</p>
                                    <p class="mt-2 text-2xl font-semibold text-ink">12d</p>
                                </div>
                                <div class="rounded-soft border border-borderCream bg-white p-3">
                                    <p class="text-[11px] uppercase tracking-[0.12em] text-mutedText">Focus</p>
                                    <p class="mt-2 text-2xl font-semibold text-ink">95m</p>
                                </div>
                            </div>

                            <div class="grid gap-3 md:grid-cols-[1.3fr_0.7fr]">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between rounded-soft border border-borderCream bg-white px-3 py-2.5">
                                        <div>
                                            <p class="text-sm font-semibold text-ink">Morning Journaling</p>
                                            <p class="text-xs text-mutedText">Target 1x / day</p>
                                        </div>
                                        <span class="rounded-full bg-[#e7f2eb] px-2.5 py-1 text-xs font-semibold text-[#1f6f45]">Done</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-soft border border-borderCream bg-white px-3 py-2.5">
                                        <div>
                                            <p class="text-sm font-semibold text-ink">Workout 20 min</p>
                                            <p class="text-xs text-mutedText">Target 5x / week</p>
                                        </div>
                                        <span class="rounded-full bg-[#f2ecdf] px-2.5 py-1 text-xs font-semibold text-[#7b6232]">Progress</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-soft border border-borderCream bg-white px-3 py-2.5">
                                        <div>
                                            <p class="text-sm font-semibold text-ink">Read 10 pages</p>
                                            <p class="text-xs text-mutedText">Target 1x / day</p>
                                        </div>
                                        <span class="rounded-full bg-sand px-2.5 py-1 text-xs font-semibold text-warmText">Pending</span>
                                    </div>
                                    <div class="flex items-center justify-between rounded-soft border border-borderCream bg-white px-3 py-2.5">
                                        <div>
                                            <p class="text-sm font-semibold text-ink">Submit sprint recap</p>
                                            <p class="text-xs text-mutedText">Todo · Due today · 16:00</p>
                                        </div>
                                        <span class="rounded-full bg-[#f2ecdf] px-2.5 py-1 text-xs font-semibold text-[#7b6232]">Due Soon</span>
                                    </div>
                                </div>

                                <div class="rounded-soft border border-charcoal bg-charcoal p-4 text-ivory">
                                    <p class="text-xs uppercase tracking-[0.14em] text-[#d0cbc1]">Running Focus</p>
                                    <p class="mt-2 text-4xl font-semibold">14:52</p>
                                    <p class="mt-2 text-xs text-[#d0cbc1]">Current task: Sprint planning doc</p>
                                    <div class="mt-4 h-2 rounded-full bg-[#5f5d58]">
                                        <div class="h-2 w-3/5 rounded-full bg-terracotta"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="rounded-card border border-borderCream bg-ivory p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">Notification Feed</p>
                            <ul class="mt-3 space-y-2">
                                <li class="rounded-soft border border-borderCream bg-white p-3 text-xs text-warmText">Reminder: Stretching time in 10 min.</li>
                                <li class="rounded-soft border border-borderCream bg-white p-3 text-xs text-warmText">Todo due soon: Submit sprint recap (16:00).</li>
                                <li class="rounded-soft border border-borderCream bg-white p-3 text-xs text-warmText">Great job! 3 habits completed today.</li>
                                <li class="rounded-soft border border-borderCream bg-white p-3 text-xs text-warmText">Telegram reminder delivered successfully.</li>
                            </ul>
                        </div>

                        <div class="rounded-card border border-borderCream bg-ivory p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">Reminder Settings</p>
                            <p class="mt-2 text-sm text-warmText">Atur channel notifikasi sesuai preferensi: email, Telegram, atau keduanya.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="stories" class="mt-12 scroll-mt-24">
                <div class="rounded-hero border border-borderCream bg-ivory p-5 md:p-7">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Trusted Workflows</p>
                    <h2 class="mt-2 text-4xl text-ink">{{ $welcomeContent->stories_title }}</h2>
                    <p class="mt-2 max-w-2xl text-sm text-warmText">{{ $welcomeContent->stories_description }}</p>

                    <div class="mt-5 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-soft border border-borderCream bg-white px-3 py-2 text-sm font-semibold text-ink">Student Routine</div>
                        <div class="rounded-soft border border-borderCream bg-white px-3 py-2 text-sm font-semibold text-ink">Freelance Studio</div>
                        <div class="rounded-soft border border-borderCream bg-white px-3 py-2 text-sm font-semibold text-ink">Remote Team</div>
                        <div class="rounded-soft border border-borderCream bg-white px-3 py-2 text-sm font-semibold text-ink">Solo Founder</div>
                    </div>
                </div>

                @php
                    $testimonials = [
                        ['quote' => 'Checklist harian jadi lebih jelas, dan saya lebih konsisten karena progress-nya kelihatan setiap hari.', 'author' => 'Nadia · Student'],
                        ['quote' => 'Focus session bantu tim kecil kami ngukur deep work, bukan cuma jam online.', 'author' => 'Ardi · Product Lead'],
                        ['quote' => 'Notifikasi reminder-nya pas, cukup mengingatkan tanpa bikin stres.', 'author' => 'Maya · Freelancer'],
                        ['quote' => 'Dashboard-nya bikin saya cepat tahu kebiasaan mana yang mulai kendor minggu ini.', 'author' => 'Rafi · Solo Founder'],
                        ['quote' => 'Setelah pakai Ritme, rapat harian tim jadi lebih fokus ke output, bukan sibuk update status.', 'author' => 'Alya · Ops Manager'],
                    ];
                @endphp

                <div class="mt-4 space-y-3">
                    <div class="testimonial-marquee">
                        <div class="testimonial-track">
                            @foreach (array_merge($testimonials, $testimonials) as $item)
                                <article class="testimonial-item">
                                    <p class="text-sm italic leading-6 text-warmText">“{{ $item['quote'] }}”</p>
                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">{{ $item['author'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="testimonial-marquee">
                        <div class="testimonial-track is-reverse">
                            @foreach (array_merge($testimonials, $testimonials) as $item)
                                <article class="testimonial-item">
                                    <p class="text-sm italic leading-6 text-warmText">“{{ $item['quote'] }}”</p>
                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">{{ $item['author'] }}</p>
                                </article>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            <section id="features" class="mt-14 scroll-mt-24 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="md:col-span-2 xl:col-span-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Features</p>
                    <h2 class="mt-2 text-4xl text-ink">{{ $welcomeContent->features_title }}</h2>
                </div>

                <article class="card-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Habit Management</p>
                    <h2 class="mt-2 text-3xl text-ink">Simple, measurable habits</h2>
                    <p class="mt-2 text-sm leading-6 text-warmText">Buat habit harian/mingguan, set target count, reminder time, warna, dan status aktif.</p>
                </article>

                <article class="card-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Todo List</p>
                    <h2 class="mt-2 text-3xl text-ink">Plan tasks with due date</h2>
                    <p class="mt-2 text-sm leading-6 text-warmText">Kelola todo dengan prioritas, due date, reminder time, dan status selesai/pending.</p>
                </article>

                <article class="card-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Focus Session</p>
                    <h2 class="mt-2 text-3xl text-ink">Track real focus time</h2>
                    <p class="mt-2 text-sm leading-6 text-warmText">Mulai/berhenti sesi fokus, catat interruptions, dan lihat ringkasan fokus harianmu.</p>
                </article>

                <article class="card-soft p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">Notification Settings</p>
                    <h2 class="mt-2 text-3xl text-ink">Email & Telegram channels</h2>
                    <p class="mt-2 text-sm leading-6 text-warmText">Konfigurasi pengiriman reminder per akun, termasuk chat ID dan bot token Telegram.</p>
                </article>
            </section>

            <section id="how-it-works" class="mt-12 scroll-mt-24 rounded-hero border border-borderCream bg-ivory p-6 md:p-8">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-mutedText">How it works</p>
                <h2 class="mt-2 text-4xl text-ink">{{ $welcomeContent->how_it_works_title }}</h2>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-soft border border-borderCream bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">Step 1</p>
                        <h3 class="mt-2 text-2xl text-ink">Buat habit & todo</h3>
                        <p class="mt-1 text-sm text-warmText">Tentukan target kecil plus tugas prioritas harian.</p>
                    </div>
                    <div class="rounded-soft border border-borderCream bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">Step 2</p>
                        <h3 class="mt-2 text-2xl text-ink">Atur reminder channel</h3>
                        <p class="mt-1 text-sm text-warmText">Pilih email, Telegram, atau keduanya dari halaman settings.</p>
                    </div>
                    <div class="rounded-soft border border-borderCream bg-white p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-mutedText">Step 3</p>
                        <h3 class="mt-2 text-2xl text-ink">Review progres</h3>
                        <p class="mt-1 text-sm text-warmText">Pantau dashboard, fokus, streak, dan notifikasi masuk.</p>
                    </div>
                </div>
            </section>

            <section class="mt-10 rounded-hero border border-charcoal bg-charcoal p-6 text-ivory md:p-8">
                <h2 class="text-4xl">{{ $welcomeContent->final_cta_title }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-[#d0cbc1]">{{ $welcomeContent->final_cta_description }}</p>

                <div class="mt-5 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-secondary-warm">Go to Dashboard</a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary-warm">Create Account</a>
                        @endif
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="btn-secondary-warm">Log In</a>
                        @endif
                    @endauth
                </div>
            </section>
        </main>

        <footer class="border-t border-borderCream bg-ivory/60">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-5 text-xs text-mutedText lg:px-8">
                <p>© {{ now()->year }} Ritme. Build your consistency gently.</p>
                <p>{{ $welcomeContent->footer_note }}</p>
            </div>
        </footer>
    </div>
</body>
</html>
