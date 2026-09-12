# Cloud in Asia — WordPress Theme Redesign Handoff

**Status:** baseline teknis untuk tim desain
**Audit live:** 12 September 2026, 02:43 WIB
**Target:** `https://cloudinasia.com/`
**Tujuan dokumen:** memberi tim desain konteks yang cukup agar hasil Figma dapat diterjemahkan menjadi tema WordPress baru oleh ATLAS-Prime tanpa menebak struktur konten, state, integrasi, atau perilaku responsif.

> Dokumen ini merupakan baseline, bukan brief visual final. Struktur WordPress dan kontrak data di bawah harus dipertahankan. Arah visual yang belum diputuskan diberi label **Keputusan desain**.

---

## 1. Ringkasan eksekutif

Cloud in Asia saat ini menggunakan **tema WordPress klasik berbasis PHP**, bukan Full Site Editing/block theme.

- Tema aktif: `cia-recovery-3.7.1`
- Parent/child: tema tunggal; **bukan child theme**
- WordPress: `6.9.1`
- PHP-FPM: `8.3.29`
- Nginx: `1.26.3`
- Bahasa antarmuka publik: Bahasa Indonesia dan English
- Zona waktu editorial/event: `Asia/Jakarta`
- Struktur URL artikel: `/%postname%/`
- Homepage: page ID `29`, slug `home`
- Newsroom/blog: page ID `31`, slug `blog`
- Tema tidak memakai WordPress Navigation Menu atau widget/sidebar dinamis; header, topic navigation, social links, dan footer masih dirakit langsung di template PHP.
- Tema memiliki 108 file, sekitar 4,1 MB, termasuk 56 gambar raster, Lottie lokal, katalog orang, dan logo institusi.
- Tidak ada `theme.json`. Token desain tersebar di `style.css` dan beberapa stylesheet per fitur.

### Implikasi untuk redesign

1. Desainer harus mendesain **template dan komponen berdasarkan content model**, bukan hanya screenshot halaman.
2. Implementasi sebaiknya menjadi **rilis tema baru yang immutable**—misalnya `cia-redesign-4.0.0`—bukan menimpa tema production aktif.
3. Header/footer sebaiknya dipindahkan dari array hard-coded ke data terkelola, tetapi URL, label bilingual, aksesibilitas, dan fallback harus tetap terjaga.
4. Redesign harus mencakup ID, EN, light, dark, responsive, empty/error/loading, dan content stress cases.
5. Semua hubungan orang/institusi harus tetap berbasis bukti; penyebutan dalam artikel tidak boleh otomatis ditampilkan sebagai partner aktif.

---

## 2. Sumber kebenaran dan arsitektur deployment

### 2.1 Infrastruktur production

| Lapisan | Nilai publik |
|---|---|
| Isolasi aplikasi | Linux container |
| Model release | Versioned WordPress releases dengan pointer aktif |
| Media | Shared uploads, terpisah dari release aplikasi |
| Theme aktif | `cia-recovery-3.7.1` |
| User PHP/WordPress | `www-data` |
| Web server | Nginx |
| Application runtime | PHP-FPM 8.3 |
| Database | MariaDB |
| Edge | Cloudflare |

Nama host, project/container, dan path filesystem production sengaja tidak dipublikasikan. Media disimpan terpisah dari release aplikasi agar tidak hilang ketika release berganti.

### 2.2 Aturan implementasi/deployment redesign

- Jangan mengedit production sebagai workspace desain.
- Buat paket tema versi baru di workspace terisolasi.
- Freeze baseline source sebelum implementasi.
- Lint seluruh PHP dan JavaScript; validasi CSS dan asset paths.
- Deploy pertama ke staging, lalu QA desktop dan mobile.
- Sebelum aktivasi production: snapshot LXD dan backup database/config yang relevan.
- Aktivasi production harus atomic/reversible: tema lama tetap tersedia untuk rollback.
- Setelah aktivasi: verifikasi route matrix, REST API, login/SSO, form inquiry, analytics consent, cache, dan Core Web Vitals.
- Manifest release harus menyertakan daftar file, ukuran, permission, dan SHA-256 aktual.

### 2.3 Temuan integritas baseline

`RELEASE-MANIFEST.json` pada tema aktif menyatakan versi `3.7.1`, tetapi **9 file production tidak lagi cocok dengan checksum manifest**:

- `assets/cia-chrome.css`
- `footer.php`
- `functions.php`
- `header.php`
- `home.php`
- `page-about.php`
- `page-speakers.php`
- `page.php`
- `style.css`

Artinya production mengandung hotfix pascarilis. Sebelum redesign dimulai, copy live saat ini harus dijadikan baseline baru dan diberi manifest baru; jangan hanya mengambil paket `3.7.1` lama.

---

## 3. Sejarah modifikasi tema yang relevan

Riwayat berikut direkonstruksi dari sesi sebelumnya, Supermemory, snapshot LXD, dan artefak tema live.

| Fase | Snapshot/rilis | Perubahan utama |
|---|---|---|
| Recovery awal | `pre-cia-recovery`, `pre-cia-recovery-1.0.1`, `1.0.2` | Pemulihan situs WordPress dan baseline tema aman. |
| Guide integration | `pre-guide-theme`, `pre-guide-theme-v2` | Integrasi Cloud Guide dan arah ecosystem/data. |
| Media recovery | `pre-generated-media`, `pre-generated-media-v2`, `pre-media-import` | Pemulihan gambar, derivative, dan media katalog. |
| Homepage ecosystem | `pre-ecosystem-home`, `pre-home-review-fixes`, `pre-data-theme-update` | Homepage editorial + ecosystem/Guide; perbaikan review dan data. |
| Bilingual | `pre-bilingual-v3` | Bahasa ID/EN, pairing artikel, routing lokal. |
| Events | `pre-event-explore-v3.1`, `pre-event-import` | Archive event, filter, detail event, content model event. |
| Guide brand | `pre-v3.1.1-guide-brand` | Penyelarasan visual/brand dengan Cloud Guide. |
| SEO dan link | `pre-theme-3.2.6`, `pre-broken-link-hotfix` | Canonical, schema, social metadata, dan perbaikan link. |
| Archive portal | `pre-archive-staging-20260829-1` | Archive/search dan pengelompokan konten editorial. |
| Footer | `pre-footer-3.3.1` | Footer editorial dan social links. |
| Events redesign | `pre-theme-3.4.0`, `pre-event-redesign-final` | Surface light/dark, event explorer, event inspector. |
| Article/mobile | `pre-theme-3.4.1` | Perbaikan single article dan layout mobile. |
| Speaker/events | `pre-theme-3.5.0` | Directory speaker, foto/nama, timeline event/session. |
| Global chrome/theme | `pre-theme-3.7.0-global`, `pre-theme-3.7.0-brow-system`, `pre-theme-3.7.0-search-archive` | Header/footer hitam, brow konsisten, mode System/Light/Dark, grouped search/archive. |
| Search language | `pre-theme-3.7.1-search-lang` | Perbaikan route dan filter bahasa di search. |
| Post-3.7.1 hotfix | `pre-favicon`, `pre-seo-routing-fix`, `pre-public-surface-fix`, `pre-content-ux-fix`, `pre-editorial-credibility`, `pre-inquiry-implementation`, `pre-ga4-implementation` | Favicon, routing SEO, hardening public surface, UX content, kredibilitas editorial, inquiry desk, dan analytics consent. File tema aktif mengalami drift dari manifest pada fase ini. |

### Prinsip desain yang sudah muncul dari iterasi sebelumnya

- Portal harus terasa editorial dan evidence-led, bukan landing page vendor.
- Cloud, open source, infrastruktur, kebijakan, opini, event, orang, dan institusi adalah jalur informasi yang berbeda tetapi saling terhubung.
- Search menggunakan hasil yang dikelompokkan, bukan satu daftar campuran tanpa konteks.
- Event hero harus mampu menampilkan lineup yang lengkap; speaker minimal foto dan nama.
- Brow/eyebrow memakai garis pendek, **bukan titik dekoratif**.
- Hindari decorative dots, pills berlebihan, glow, dan efek “AI-style”.
- Jangan menggunakan portrait generatif. Gunakan foto terverifikasi atau fallback non-fotorealistik yang netral.
- Peta harus transparan dan menyatu dengan surface, bukan kartu peta opaque yang tidak konsisten.
- Mode light dan dark harus lengkap dan konsisten; tidak boleh terjadi campuran surface yang tampak tidak disengaja.

---

## 4. Struktur tema aktif

### 4.1 Template utama

| File | Tanggung jawab |
|---|---|
| `front-page.php` | Homepage: latest carousel, Guide intelligence, provider search, Cloud Pulse, intent journeys, discussed entities, editorial desk, events, collaborators, topics. |
| `home.php` | Newsroom/blog dengan lima desk editorial: Cloud, Open Source, Infrastruktur, Kebijakan, Opini. |
| `single.php` | Single article modern, metadata, media, article body, context/related content. |
| `archive.php` | Archive editorial umum. |
| `search.php` | Search terkelompok lintas scope. |
| `page.php` | Halaman generik dan policy pages. |
| `page-about.php` | Tentang Cloud in Asia dan konteks organisasi/editorial. |
| `page-partners.php` | Directory kolaborator/institusi dengan evidence. |
| `page-speakers.php` | Directory people/speaker. |
| `archive-events.php` | Event explorer, filter, current/past event cards. |
| `single-events.php` | Event inspector: detail, registration state, gallery, people, agenda/session. |
| `taxonomy-speakers.php` | Profil speaker dan timeline keterlibatan event. |
| `single-cfp_call.php` | Detail call for proposal/sponsor/volunteer. |
| `index.php` | Fallback minimum. |
| `header.php` | Global chrome, desktop navigation, mobile drawer, search, language, theme mode, social links. |
| `footer.php` | Footer bilingual, policy links, social links, back-to-top. |
| `functions.php` | Setup tema, enqueue assets, localization, Guide API data, event helpers, SEO/schema/social meta, archive/search logic. |

### 4.2 Asset dan behavior modules

| Asset/module | Fungsi |
|---|---|
| `style.css` | Stylesheet besar yang masih memuat beberapa generasi layout dan token. |
| `assets/cia-chrome.css` | Header, mobile drawer, footer, focus state, sticky offsets. |
| `assets/cia-theme-surfaces.css` | Semantic light/dark content surfaces. |
| `assets/archive-portal.css` + `archive.js` | Archive portal dan filtering/load-more. |
| `assets/search-archive.css` + `search-archive.js` | Grouped search UI dan pagination/interactions. |
| `assets/events-collaborators.css` | Event dan collaborator-specific surfaces. |
| `assets/event-filter.js` | Filtering event archive. |
| `assets/event-media.js` | Gallery/media event. |
| `assets/theme-mode.js` | `system`, `light`, `dark`; preference disimpan di `localStorage` key `cia-theme-mode`. |
| `assets/header-menu.js` | Mobile drawer, focus/interaction header. |
| `assets/site.js` | Interaksi global termasuk back-to-top. |
| `assets/news-carousel.js` | Carousel berita homepage. |
| `assets/blog-motion.js` | Lottie newsroom dengan fallback statis. |
| `assets/guide-explorer.js` | Provider lookup terhadap dataset Guide. |
| `assets/vendor/lottie-light.min.js` | Runtime Lottie yang disimpan lokal. |

### 4.3 Keterbatasan struktural saat ini

- `functions.php` sudah sekitar 60 KB dan memiliki banyak domain concern.
- `style.css` sekitar 115 KB dengan beberapa set token historis yang tumpang tindih.
- Breakpoint belum konsisten.
- Navigation/footer masih hard-coded.
- People portrait dan partner logo disimpan langsung dalam paket tema; ini menyulitkan update konten tanpa release tema.
- Homepage memiliki beberapa record collaborator/evidence yang hard-coded.
- Tidak ada component library formal, template parts, atau `theme.json` sebagai kontrak token.

**Rekomendasi implementasi:** pecah helper berdasarkan domain (`inc/editorial.php`, `inc/events.php`, `inc/seo.php`, `inc/guide.php`), gunakan template parts, dan buat satu layer token canonical. Perubahan arsitektur ini dilakukan saat implementasi, bukan oleh desainer.

---

## 5. Content model WordPress

### 5.1 Tipe konten

| Post type | Status publik | Isi saat audit | Field inti/UI |
|---|---:|---:|---|
| `post` | Ya | 116 published | title, slug, content, excerpt, featured image, categories, tags, locale/pairing, SEO. |
| `page` | Ya | 21 published | title, content, slug, optional dedicated template. |
| `events` | Ya; archive `/events/` | 32 published | title, content, featured image, dates, venue, registration, map, webinar, gallery, taxonomies. |
| `cfp_call` | Ya; archive `/opportunities/` | 1 published | parent event, type, opening/closing time, CTA, bilingual title/summary/content. |
| `event_session` | Tidak memiliki public route sendiri | 16 published | parent event, day, start/end, room, moderator, speakers, type, ordering. Ditampilkan di event induk. |
| `cia_inquiry` | Private/admin only | Tidak untuk desain publik kecuali form | Form inquiry publik dan workflow internal. |

### 5.2 Taxonomy

| Taxonomy | Dipakai oleh | Makna/desain |
|---|---|---|
| `category` | Post | Desk/topik editorial. Dominan: Berita, Open Source, Cloud, Cloud Technology, Review, Infrastruktur, Opini, Private Cloud, Cloud Computing, Linux, Kebijakan. |
| `post_tag` | Post | Tag bebas; jangan dijadikan navigation primer tanpa kurasi. |
| `event_type` | Events | Conference, Regional Event, Webinar, Meetup. |
| `speakers` | Events + Event Sessions | Identitas orang; membutuhkan portrait, nama, optional website/profile, event history. |
| `sponsors` | Events | Sponsor event; tidak sama dengan collaborator/partner organisasi. |
| `community_support` | Events | Community supporter. |
| `education-partner` | Events | Institusi pendidikan. |
| `editorial_team` | Post | Anggota editorial; saat audit term tersedia tetapi belum mempunyai post count publik. |

### 5.3 Field event

- `event_start`, `event_end`: datetime `Asia/Jakarta`
- `venue`
- `registration_open`, `registration_closed`
- `registration_link`
- `webinar_link`
- `google_map_link`
- `_cia_media_gallery_ids`
- Featured image
- Taxonomy: event type, speakers, sponsors, community support, education partner

State desain minimum:

- upcoming / ongoing / past
- registration not open / open / closing soon / closed
- online / offline / hybrid
- venue absent
- registration URL absent
- no speaker / one speaker / large lineup
- no agenda / one day / multiple days
- no gallery / gallery available

### 5.4 Field Event Session

- `_cia_parent_event_id`
- `_cia_session_day`
- `_cia_session_start`, `_cia_session_end`
- `_cia_session_room`
- `_cia_session_moderator`
- `_cia_session_speakers_text`
- taxonomy `speakers`
- `_cia_session_type`
- `menu_order`
- optional featured image

Session tidak boleh didesain sebagai halaman publik mandiri; card/row-nya hidup di agenda event induk.

### 5.5 Field Call for Papers/Opportunity

- `_cia_parent_event_id`
- `_cia_call_type`: `proposal`, `sponsor`, atau `volunteer`
- `_cia_call_open`, `_cia_call_close`
- `_cia_call_cta_url`
- `_cia_call_title_id`, `_cia_call_title_en`
- `_cia_call_summary_id`, `_cia_call_summary_en`
- `_cia_call_content_id`, `_cia_call_content_en`

State minimum: scheduled, open, closing soon, closed, CTA unavailable.

### 5.6 Artikel bilingual

- `_cia_locale`: `id` atau `en`
- `_cia_translation_pair_id`
- `_cia_translation_status`
- `_cia_primary_category`
- translation review/provenance metadata

Desain harus mendukung:

- artikel memiliki pasangan bahasa;
- artikel belum memiliki pasangan bahasa;
- language switch kembali ke homepage bahasa tujuan jika pasangan tidak tersedia;
- label tanggal dan metadata terlokalisasi;
- string UI tidak tercampur ID/EN dalam satu mode.

### 5.7 People dan institution media

Term speakers/sponsors/community/education dapat memiliki:

- `z_taxonomy_image_id`
- `website_link`

Media attachment dapat memiliki:

- approval status
- SHA-256
- source reference

Implikasi desain: logo dan portrait harus memiliki fallback, attribution/provenance tidak boleh hilang dari workflow, dan gambar yang belum disetujui tidak boleh diasumsikan siap publikasi.

---

## 6. Route dan template yang wajib didesain

| Prioritas | Surface | Contoh route | Template/data |
|---:|---|---|---|
| P0 | Homepage ID/EN | `/`, `/?lang=en` | `front-page.php` |
| P0 | Newsroom/blog | `/blog/`, `/blog/?lang=en` | `home.php` |
| P0 | Single article | `/{slug}/`, `/en/{slug}/` | `single.php` |
| P0 | Search grouped | `/?s=cloud`, `/?s=cloud&lang=en` | `search.php` |
| P0 | Event archive | `/events/` | `archive-events.php` |
| P0 | Single event | `/events/{slug}/` | `single-events.php` |
| P0 | Speaker directory | `/speakers/` | `page-speakers.php` |
| P0 | Speaker profile | `/speakers/{slug}/` | `taxonomy-speakers.php` |
| P1 | Category/archive | `/category/open-source/` | `archive.php`/category hierarchy |
| P1 | Collaborators | `/partners/` | `page-partners.php` |
| P1 | About | `/about/` | `page-about.php` |
| P1 | Contact/inquiry | `/contact/` | `page.php` + Inquiry Desk form |
| P1 | Editorial policies | `/editorial-policy/`, `/corrections-policy/`, `/disclosure/`, `/privacy-policy/`, `/terms-of-use/` | `page.php` |
| P1 | Opportunity | `/opportunities/`, single opportunity | CPT `cfp_call` |
| P2 | 404/empty archive | invalid route | fallback/404 design diperlukan |

Route sample yang diperiksa saat audit menjawab HTTP 200; URL event-session legacy melakukan redirect ke anchor session pada event induk.

---

## 7. Komponen yang harus ada di Figma

### 7.1 Global

- Skip link
- Header desktop tiga lapis atau pengganti yang setara
- Logo
- Primary navigation
- Topic navigation
- Search input
- Language switch ID/EN
- Theme switch System/Light/Dark
- Social links
- Mobile app-style drawer/menu
- Footer: brand statement, explore, topics, policies, contact, social links, back-to-top
- Cookie/analytics consent state bila banner digunakan

### 7.2 Editorial

- Section brow tanpa titik dekoratif
- Story card: with image / no image
- Lead story
- Compact story row
- Category/tag label
- Dateline/date/byline
- Featured image
- Article header
- Article body typography: paragraph, H2, H3, link, blockquote, list, figure, caption, table, embed, code, footnote
- Related/context panel
- Pagination dan load-more
- Empty archive
- Search group header dan result item per scope
- No-results state dan suggested path

### 7.3 Events

- Event card
- Date tile
- Event status
- Filter/search controls
- Event hero
- Detail fact panel
- Registration CTA/state
- Venue/map link
- Online/webinar link
- Speaker lineup: photo + name minimum
- Sponsor/community/education group yang berbeda secara semantik
- Agenda day tabs/sections
- Session row/card
- Gallery
- Opportunity/CFP card dan detail

### 7.4 People dan institutions

- Portrait verified
- Initial/fallback portrait
- Speaker directory item
- Speaker profile hero
- Speaker event timeline
- Institution/sponsor logo plate
- Long-name and extreme-logo-ratio state
- Evidence/reference link
- “Mentioned” state yang tidak menyiratkan partnership

### 7.5 Inquiry form

Field publik: name, email, category, article URL, subject, message, consent, submit.

Kategori: correction, editorial/story lead, partnership, event/community, privacy, technical, other.

State minimum:

- default
- focus
- valid
- field error
- form error
- submitting
- success dengan reference ID
- rate-limited/generic failure
- disabled

Jangan menampilkan detail workflow internal atau data pribadi di desain publik.

---

## 8. Design tokens dan variabel yang harus diputuskan

### 8.1 Warna semantic baseline

Current production memakai nilai berikut. Tim desain boleh mengubah nilainya, tetapi harus mempertahankan semantic role dan menyediakan light/dark pair.

| Token semantic | Light/current | Dark/current | Penggunaan |
|---|---|---|---|
| `color.page` | `#f3f1eb` | `#141414` | Canvas utama |
| `color.surface` | `#ffffff` | `#1b1b1b` | Card/content surface |
| `color.surface.raised` | `#ece9e1` | `#222222` | Raised/secondary surface |
| `color.text.strong` | `#171917` | `#f4f4f4` | Heading/strong text |
| `color.text.body` | `#30332f` | `#dedede` | Body copy |
| `color.text.muted` | `#5a5f59` | `#b4b8b5` | Metadata/supporting copy |
| `color.border` | `#c7cac4` | `#444844` | Dividers/borders |
| `color.input` | `#fafafa` | `#242424` | Input background |
| `color.link` | `#166d50` | `#75d9b0` | Content links |
| `color.accent.mint` | `#60bd91` | `#60bd91`/adjusted | Primary editorial accent |
| `color.accent.yellow` | `#ffde59` | `#ffde59`/adjusted | Focus/secondary accent |
| `color.chrome.bg` | `#0a0a0a` | `#0a0a0a` | Current header/footer |
| `color.chrome.ink` | `#f4f4f4` | `#f4f4f4` | Chrome text |

**Keputusan desain:** apakah header/footer tetap hitam pada dua mode atau ikut berubah. Apa pun pilihannya, halaman tidak boleh terlihat sebagai campuran mode yang tidak disengaja.

### 8.2 Typography

Current stack:

- UI sans: `Ubuntu`, lalu system sans fallback
- Editorial serif: `Georgia`, `Times New Roman`, serif
- Metadata/brow: `ui-monospace`, `SFMono-Regular`, `Menlo`, monospace

Tim desain harus menyerahkan:

- font family dan lisensi/hosting;
- fallback stack;
- available weights;
- type scale desktop/mobile;
- line-height dan letter-spacing;
- measure artikel;
- treatment untuk long Indonesian/English headlines;
- numerals, date tiles, labels, captions, code, dan footnotes.

Catatan: `Ubuntu` belum dijamin tersedia pada perangkat pengguna. Jika harus presisi, pilih self-hosted webfont yang legal atau gunakan system font sebagai keputusan eksplisit.

### 8.3 Layout baseline

| Variabel | Current | Catatan redesign |
|---|---:|---|
| Main container | `1240px` | Satukan dengan chrome. |
| Chrome container | `1280px` | Saat ini berbeda 40px dari main. |
| Article measure | `760px` | Pertahankan kisaran nyaman untuk editorial. |
| Article grid | content sampai `820px` + context rail | Harus collapse di tablet/mobile. |
| Header desktop | 84 + 56 + 42 px | Current tiga row; boleh disederhanakan. |
| Mobile header | 60 px | Minimum touch target tetap 44px. |
| Sticky z-index | header `80`, menu `90`, drawer `120` | Serahkan z-index scale formal. |

### 8.4 Breakpoint

Current code menggunakan banyak breakpoint: 380, 520, 575/580, 620, 680/700, 760, 782, 800, 899/900, 980, 1024/1035, 1100, dan 1180 px. Ini technical debt.

Tim desain harus mendefinisikan breakpoint canonical, misalnya:

- mobile: 390/430 reference frame;
- tablet: 768;
- small desktop: 1024;
- wide desktop: 1280/1440.

Breakpoint final harus dipilih berdasarkan **layout failure**, bukan nama device. Setiap komponen wajib punya behavior di antara frame, bukan hanya screenshot statis.

### 8.5 Spacing, radius, shadow, motion

Current radius tersebar di 0, 8, 10, 12, 16, 18, dan 999 px. Redesign perlu satu scale:

- spacing: tentukan base unit dan token `space.1` sampai `space.n`;
- radius: tentukan `none`, `sm`, `md`, `lg`; hindari pill `999px` kecuali kontrol yang benar-benar memerlukannya;
- border: 1px normal, 2–4px accent bila bermakna;
- shadow: satu scale semantic; jangan memakai glow;
- motion: duration/easing untuk menu, carousel, filters, hover, dan gallery;
- wajib ada reduced-motion variant dan static fallback untuk Lottie.

### 8.6 Figma variable naming yang disarankan

```text
CIA/color/{light|dark}/{page|surface|raised|text-strong|text-body|text-muted|border|link}
CIA/color/global/{mint|yellow|success|warning|error|focus}
CIA/type/{display|headline|title|body|caption|metadata}/{desktop|mobile}
CIA/space/{0|1|2|3|4|5|6|8|10|12|16}
CIA/radius/{none|sm|md|lg}
CIA/layout/{content|max|article|rail|gutter}
CIA/motion/{fast|normal|slow}
CIA/z/{content|sticky|header|popover|drawer|modal}
```

---

## 9. Image dan asset specification

### 9.1 Baseline asset saat ini

- Logo utama: 675 × 152, rasio 4.44:1
- Peta Asia Tenggara: 1000 × 820, rasio 1.22:1
- Editorial signal fallback: 720 × 520
- Portrait speaker: dominan 400 × 400, grayscale
- Partner/institution logos: rasio sangat beragam, sekitar 1:1 sampai lebih dari 7:1

### 9.2 Spesifikasi yang harus diberikan desain

| Asset | Rekomendasi kontrak |
|---|---|
| Story featured image | Tentukan master crop, minimal source, focal point, dan fallback. Sediakan 16:9 serta behavior crop untuk card/hero. |
| Speaker portrait | 1:1; minimum 800 × 800 source bila tersedia; tampilkan sebagai square/circle sesuai component; tidak generatif. |
| Institution logo | SVG preferred; PNG transparan fallback; gunakan `object-fit: contain`; siapkan white/dark variants bila perlu. |
| Hero illustration/map | SVG/WebP/AVIF atau transparent PNG; wajib punya fallback statis dan alt strategy. |
| Social preview | 1200 × 630. |
| Icon | SVG inline/sprite; jangan memakai screenshot atau raster kecil. |

WordPress saat ini hanya mendaftarkan image sizes bawaan: thumbnail 150×150, medium 300×300, medium_large 768, large 1024. Redesign harus menentukan custom crops yang diperlukan sebelum implementasi agar tidak mengandalkan `full` image di semua surface.

Designer harus menandai setiap image component dengan:

- ratio;
- min/max height;
- `cover` vs `contain`;
- focal point;
- lazy/eager priority;
- alt/decorative rule;
- no-image fallback;
- dark mode treatment.

---

## 10. Localization dan copy

- Semua global navigation, button, empty state, error state, event state, dan accessibility label harus tersedia dalam ID dan EN.
- Jangan memperpendek copy hanya agar cocok di satu frame; uji heading dua sampai tiga baris dan label panjang.
- Route English menggunakan parameter/slug yang telah ditangani oleh theme/workflow; desain tidak boleh mengasumsikan semua konten memiliki pasangan.
- Date/event formatting harus mengacu pada `Asia/Jakarta` dan locale aktif.
- Guide tetap bernama **Guide**; jangan diterjemahkan menjadi produk berbeda.

---

## 11. Integrasi yang tidak boleh rusak

### 11.1 WordPress REST/API dan automation

- `post`, `page`, events, calls, sessions, dan taxonomy yang relevan tersedia melalui WordPress/REST sesuai registration.
- Theme mengonsumsi Cloud Guide melalui `https://guide.cloudin.asia/api` dan dataset cache lokal.
- Redesign tidak boleh membuat content model bergantung pada DOM/CSS class tertentu untuk automation.
- Bot/integrasi memakai mekanisme machine credential terpisah; human login memakai SSO WordPress yang berlaku. Jangan merancang authentication publik baru di theme.

### 11.2 SEO

Production memakai Yoast SEO dan custom theme/MU-plugin logic untuk:

- canonical URL;
- meta description;
- document title;
- Open Graph/social metadata;
- structured data;
- robots policy;
- redirect legacy policy pages;
- author sitemap privacy.

`wp_head()` dan `wp_footer()` wajib tetap ada. Implementasi harus menghindari duplicate canonical/schema/OG antara Yoast dan custom logic.

Design requirement:

- hanya satu H1 per page;
- heading hierarchy konsisten;
- breadcrumb bila dipilih harus punya visual dan schema yang selaras;
- byline, datePublished/dateModified, author/editorial attribution, correction/disclosure links harus punya tempat jelas;
- social preview tidak bergantung pada screenshot halaman.

### 11.3 Analytics dan consent

MU plugin `CIA GA4 Consent` mengatur analytics berbasis consent. Redesign harus menyediakan state consent yang accessible dan tidak menghambat konten utama. Jangan hard-code analytics script di template desain/tema.

### 11.4 Inquiry/contact

Plugin `CIA Inquiry Desk 1.1.4` menyisipkan form pada Contact page. Stylesheet plugin saat ini terpisah dan belum memakai token tema secara penuh. Redesign harus memasukkan form ke component system tanpa mengubah nama/semantik field atau privacy consent.

### 11.5 Security/login

Production memiliki SSO Authentik, WebAuthn, 2FA, brute-force protection, dan public-surface hardening. Redesign tidak boleh:

- membuat form login custom publik;
- mengekspos username/author archive yang sengaja diproteksi;
- menampilkan data Inquiry Desk;
- menaruh secret/token/API credential di theme atau Figma.

### 11.6 Cache/performance

- Cloudflare berada di edge.
- Tidak ada page-cache plugin aktif yang menjadi kontrak desain.
- Asset versioning saat ini berbasis theme/version/filemtime.
- Lottie disimpan lokal dan punya fallback statis.

Target implementasi harus mengikuti Core Web Vitals saat berlaku: LCP, INP, CLS. Hero asset, font, JavaScript, dan carousel harus didesain agar progressive enhancement tetap menghasilkan halaman yang berguna ketika JavaScript gagal.

---

## 12. Accessibility acceptance criteria

- WCAG 2.2 AA sebagai baseline.
- Contrast diuji untuk light/dark dan semua accent state.
- Touch target minimal 44 × 44 px.
- Semua interaksi keyboard-operable.
- Focus-visible jelas; current baseline memakai yellow outline 2px.
- Mobile drawer mengelola focus, Escape, close button, dan body scroll.
- Search, filter, carousel, tabs, gallery, theme switch, dan language switch memiliki accessible name/state.
- Jangan mengandalkan warna saja untuk status.
- `prefers-reduced-motion` didukung.
- Portrait/logo memiliki alt strategy; decorative graphic memakai empty alt.
- Error form terhubung ke field dan diumumkan screen reader.
- Heading hierarchy dan landmark (`header`, `nav`, `main`, `article`, `aside`, `footer`) jelas.

---

## 13. Format handoff yang dibutuhkan dari tim desain

### 13.1 File Figma

Page minimum:

1. `00 Foundations`
2. `01 Components`
3. `02 Global Chrome`
4. `03 Homepage`
5. `04 Newsroom + Archives`
6. `05 Article`
7. `06 Search`
8. `07 Events + Sessions + Calls`
9. `08 People + Institutions`
10. `09 Pages + Policies + Contact`
11. `10 Responsive + States`
12. `11 Prototype + Notes`

### 13.2 Naming component

Gunakan struktur yang dapat dipetakan ke kode:

```text
CIA/Header/Desktop
CIA/Header/Mobile
CIA/StoryCard/{Image|NoImage}/{Light|Dark}
CIA/EventCard/{Upcoming|Past}/{Image|NoImage}
CIA/SpeakerCard/{Photo|Fallback}
CIA/SearchResult/{Article|Event|Speaker|Institution}
CIA/FormField/{Default|Focus|Error|Disabled}
```

### 13.3 Setiap frame/template wajib mencantumkan

- route/template target;
- sumber data WordPress;
- component variants;
- responsive behavior;
- empty/error/loading states;
- image ratio/crop;
- ID/EN copy;
- light/dark behavior;
- interaction/motion notes;
- accessibility notes;
- conditional visibility rules.

### 13.4 Asset export

- SVG untuk logo/icon/illustration vector.
- WebP/AVIF + source PNG/JPG untuk raster.
- Jangan outline text pada logo tanpa source editable.
- Sertakan lisensi dan provenance asset.
- Jangan mengirim secret, credential, staging cookie, atau user data.

---

## 14. Definition of Ready untuk implementasi

ATLAS-Prime dapat mulai mengimplementasikan ketika tersedia:

- [ ] Art direction disetujui.
- [ ] Semua P0 template ada untuk desktop dan mobile.
- [ ] Light/dark/system behavior didefinisikan.
- [ ] Tokens Figma complete dan tidak duplikatif.
- [ ] Typography family/weights/licensing final.
- [ ] Header/footer/navigation behavior final.
- [ ] Story card, event, speaker, institution, search, dan form variants lengkap.
- [ ] Content stress tests: judul panjang, tanpa gambar, lineup besar, logo ekstrem, kosong, error.
- [ ] ID dan EN copy/state tersedia.
- [ ] Asset export dan provenance lengkap.
- [ ] Accessibility notes dan keyboard flows tersedia.
- [ ] Keputusan classic theme v4 vs hybrid `theme.json` disetujui.

---

## 15. Definition of Done implementasi

- [ ] Tema baru dipaketkan sebagai versi baru; tema lama tidak ditimpa.
- [ ] Tidak ada PHP syntax error; seluruh 26 file PHP terkait theme/MU/inquiry tetap lolos lint.
- [ ] Manifest SHA-256 cocok 100% dengan artefak release.
- [ ] Semua P0/P1 route lolos smoke test ID/EN, desktop/mobile.
- [ ] Header/footer/search/menu bekerja tanpa dan dengan JavaScript.
- [ ] Mode System/Light/Dark konsisten dan preference tersimpan.
- [ ] Artikel, event, session, CFP, speaker, collaborator, dan inquiry form menggunakan data yang benar.
- [ ] Tidak ada inferred partnership dari sekadar mention.
- [ ] Tidak ada portrait generatif, dots dekoratif, pills berlebihan, atau glow.
- [ ] SEO tags/schema tidak terduplikasi.
- [ ] REST API dan Guide API tetap sehat.
- [ ] Consent-gated analytics tetap berfungsi.
- [ ] WCAG 2.2 AA checks dan keyboard QA selesai.
- [ ] Core Web Vitals serta image/font loading diperiksa.
- [ ] Staging QA dan approval selesai sebelum production.
- [ ] Snapshot/rollback production tersedia dan diuji secara prosedural.

---

## 16. Keputusan yang masih dibutuhkan dari Ryo/tim desain

1. Apakah arah redesign mempertahankan black editorial chrome atau membuat header/footer mode-aware?
2. Apakah tetap memakai Ubuntu + Georgia, atau pindah ke pasangan typeface baru?
3. Apakah implementasi tetap classic PHP theme atau hybrid dengan `theme.json`?
4. Apakah navigation harus dikelola dari WordPress Admin atau tetap curated di kode?
5. Apakah homepage tetap menampilkan semua modul Guide/Cloud Pulse atau disederhanakan?
6. Apakah archive/search tetap grouped editorial portal, dan scope apa yang paling penting?
7. Apakah area sponsor, collaborator, community, education, dan mentions perlu sistem visual yang benar-benar terpisah?
8. Apakah ada ad inventory, newsletter, membership, atau paywall yang perlu disiapkan?
9. Apakah author/editorial team akan memiliki public profile terpisah dari speaker?
10. Apakah partner/portrait asset akan dipindahkan dari theme ke WordPress Media/content model?
11. Breakpoint canonical, grid, spacing scale, radius scale, dan final type scale.
12. Target browser/device dan performance budget final.

---

## 17. Referensi audit

- Audit read-only terhadap WordPress production dalam lingkungan container terisolasi.
- Tema live `cia-recovery-3.7.1`, bukan paket lama.
- Snapshot LXD historis dari fase recovery sampai GA4/inquiry.
- WordPress CLI inventory untuk theme, plugin, post type, taxonomy, option, dan runtime.
- Pemeriksaan file/checksum tema aktif.
- Riwayat sesi dan Supermemory Cloud in Asia.
- Smoke test publik untuk homepage, blog, events, speakers, partners, about, search, article, dan event route.

**Catatan keamanan:** seluruh credential, connection string, cookie, token, dan secret sengaja tidak dimasukkan dalam dokumen ini.
