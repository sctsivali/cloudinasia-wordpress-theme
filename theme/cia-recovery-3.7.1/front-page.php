<?php
get_header();
$lang = cia_home_language();
$t = $lang === 'en' ? array(
    'edition' => 'Latest News',
    'top_story' => 'Latest News',
    'latest' => 'Latest',
    'latest_note' => 'Reporting and analysis for people building, operating, and governing cloud infrastructure.',
    'read' => 'Read story',
    'all_editorial' => 'All editorial',
    'desk' => 'Editorial Desk',
    'desk_title' => 'Context before conclusions.',
    'desk_note' => 'Reporting on cloud, open source, infrastructure, and digital policy through an Indonesian and Southeast Asian lens.',
    'events_kicker' => 'Events Desk',
    'events_title' => 'Where reporting becomes industry conversation.',
    'events_note' => 'Activity archive ordered by verified event date—not publication date.',
    'all_events' => 'All events',
    'view_archive' => 'View archive',
    'collab_kicker' => 'Connected to the Ecosystem',
    'collab_title' => 'Industry, community, academia, and open source.',
    'collab_note' => 'Cloud in Asia documents and connects the people, institutions, and communities shaping Southeast Asia’s cloud ecosystem.',
    'evidence' => 'Editorial record',
    'topics' => 'Browse by topic',
    'topics_title' => 'Follow the issue, not the hype.',
    'empty' => 'Editorial curation is being prepared.',
    'date_missing' => 'Date under review',
    'roles' => array('Strategic collaboration and CROSS 2026 MoU record','CROSS Webinar ecosystem support record','Host campus record for CROSS Indonesia 2026')
) : array(
    'edition' => 'Berita Terkini',
    'top_story' => 'Berita Terkini',
    'latest' => 'Terkini',
    'latest_note' => 'Liputan dan analisis untuk mereka yang membangun, mengoperasikan, dan mengatur infrastruktur cloud.',
    'read' => 'Baca artikel',
    'all_editorial' => 'Semua editorial',
    'desk' => 'Meja Editorial',
    'desk_title' => 'Konteks dulu, baru kesimpulan.',
    'desk_note' => 'Liputan cloud, open source, infrastruktur, dan kebijakan digital dari sudut pandang Indonesia dan Asia Tenggara.',
    'events_kicker' => 'Agenda Kegiatan',
    'events_title' => 'Saat liputan bertemu percakapan industri.',
    'events_note' => 'Arsip kegiatan diurutkan berdasarkan tanggal pelaksanaan terverifikasi—bukan tanggal publikasi.',
    'all_events' => 'Semua events',
    'view_archive' => 'Lihat arsip',
    'collab_kicker' => 'Terhubung dengan Ekosistem',
    'collab_title' => 'Industri, komunitas, akademia, dan open source.',
    'collab_note' => 'Cloud in Asia mendokumentasikan dan menghubungkan orang, institusi, dan komunitas yang membentuk ekosistem cloud Asia Tenggara.',
    'evidence' => 'Bukti editorial',
    'topics' => 'Jelajahi topik',
    'topics_title' => 'Ikuti isunya, bukan sekadar hype-nya.',
    'empty' => 'Kurasi editorial sedang disiapkan.',
    'date_missing' => 'Tanggal dalam peninjauan',
    'roles' => array('Rekam kolaborasi strategis dan MoU CROSS 2026','Rekam dukungan ekosistem CROSS Webinar','Rekam kampus tuan rumah CROSS Indonesia 2026')
);
$collaborators = array(
    array('slug'=>'inti','name'=>'INTI','evidence'=>913),
    array('slug'=>'sivali','name'=>'Sivali Cloud Technology','evidence'=>797),
    array('slug'=>'unjaya','name'=>'Universitas Jenderal Achmad Yani Yogyakarta','evidence'=>877),
);
$editorial = new WP_Query(cia_editorial_query_args($lang, array('posts_per_page'=>5,'ignore_sticky_posts'=>true,'orderby'=>'date','order'=>'DESC')));
$stories = $editorial->posts;
$latest_editorial_date = !empty($stories) ? cia_editorial_date((int) $stories[0]->ID, $lang) : '';
$discussed = cia_home_discussed_entities(5, 10);
$guide_data = cia_guide_homepage_data();
$guide_metrics = isset($guide_data['metrics']) && is_array($guide_data['metrics']) ? $guide_data['metrics'] : array();
$guide_pulse = isset($guide_data['pulse']) && is_array($guide_data['pulse']) ? $guide_data['pulse'] : array();
$guide_checked = '';
if (!empty($guide_data['generated_at'])) {
    try {
        $guide_checked_at = new DateTimeImmutable($guide_data['generated_at']);
        $guide_checked_at = $guide_checked_at->setTimezone(new DateTimeZone('Asia/Jakarta'));
        $guide_checked = $guide_checked_at->format($lang === 'en' ? 'j M Y, H:i T' : 'j M Y, H:i') . ($lang === 'en' ? '' : ' WIB');
    } catch (Exception $error) { $guide_checked = ''; }
}
$future = $lang === 'en' ? array(
    'eyebrow'=>'Southeast Asia cloud intelligence', 'title'=>"Know Southeast Asia's cloud market.",
    'deck'=>'Independent intelligence on cloud providers, infrastructure, open source, pricing, AI infrastructure, and digital policy—connected in one evidence layer.',
    'latest'=>'Latest intelligence', 'guide'=>'Explore Cloud Guide',
) : array(
    'eyebrow'=>'Intelijen cloud Asia Tenggara', 'title'=>'Pahami pasar cloud. Bukan cuma mereknya.',
    'deck'=>'Data provider, infrastruktur, teknologi terbuka, harga, AI infrastructure, dan kebijakan digital—terhubung dalam satu lapisan intelijen independen.',
    'latest'=>'Intelijen terbaru', 'guide'=>'Jelajahi Cloud Guide',
);
$metric_labels = $lang === 'en' ? array(
    'providers_asean'=>'ASEAN-based providers','providers_total'=>'Providers indexed','plans_total'=>'Service plans','facilities_total'=>'Named facilities',
) : array(
    'providers_asean'=>'Provider berkantor di ASEAN','providers_total'=>'Provider terindeks','plans_total'=>'Paket layanan','facilities_total'=>'Fasilitas bernama',
);
$upcoming_events = new WP_Query(array(
    'post_type'=>'events','post_status'=>'publish','posts_per_page'=>1,
    'meta_key'=>'event_start','meta_value'=>(new DateTimeImmutable('now', cia_event_timezone()))->format('Y-m-d H:i:s'),
    'meta_compare'=>'>=','orderby'=>'meta_value','order'=>'ASC','no_found_rows'=>true,
));
?>
<div class="future-home" data-home-surface>
<section class="home-latest-carousel" id="latest" aria-labelledby="home-latest-title" data-news-carousel>
  <div class="container">
    <header class="home-news-heading">
      <div>
        <span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'Cloud in Asia editorial' : 'Editorial Cloud in Asia'); ?></span>
        <h1 id="home-latest-title"><?php echo esc_html($t['edition']); ?></h1>
        <p><?php echo esc_html($t['latest_note']); ?></p>
      </div>
      <div class="home-news-toolbar">
        <?php if ($latest_editorial_date): ?><p><?php echo esc_html(($lang === 'en' ? 'Last published · ' : 'Terakhir terbit · ') . $latest_editorial_date); ?></p><?php endif; ?>
        <?php if (count($stories) > 1): ?><div class="home-news-controls" hidden aria-label="<?php echo esc_attr($lang === 'en' ? 'News carousel controls' : 'Kontrol carousel berita'); ?>">
          <button type="button" data-news-carousel-prev aria-controls="home-news-track" aria-label="<?php echo esc_attr($lang === 'en' ? 'Previous story' : 'Berita sebelumnya'); ?>" disabled><span aria-hidden="true">←</span></button>
          <p data-news-carousel-status aria-live="polite" aria-atomic="true">1 / <?php echo esc_html((string) count($stories)); ?></p>
          <button type="button" data-news-carousel-next aria-controls="home-news-track" aria-label="<?php echo esc_attr($lang === 'en' ? 'Next story' : 'Berita berikutnya'); ?>"><span aria-hidden="true">→</span></button>
        </div><?php endif; ?>
        <a class="home-news-all" href="<?php echo esc_url(cia_localized_url(home_url('/blog/'), $lang)); ?>"><?php echo esc_html($t['all_editorial']); ?> <span aria-hidden="true">↗</span></a>
      </div>
    </header>
    <?php if (!empty($stories)): ?>
    <ol id="home-news-track" class="home-news-track" data-news-carousel-track tabindex="0" aria-label="<?php echo esc_attr($lang === 'en' ? 'Latest Cloud in Asia stories. Swipe or use arrow keys.' : 'Berita terkini Cloud in Asia. Geser atau gunakan tombol panah.'); ?>">
      <?php foreach ($stories as $story_index => $post): setup_postdata($post); $story_cats = get_the_category(); $story_excerpt = trim((string) get_the_excerpt()); $story_has_media = has_post_thumbnail(); ?>
      <li class="home-news-slide" data-news-carousel-slide<?php if ($story_index === 0): ?> aria-current="true"<?php endif; ?> aria-label="<?php echo esc_attr(sprintf($lang === 'en' ? 'Story %1$d of %2$d' : 'Berita %1$d dari %2$d', $story_index + 1, count($stories))); ?>">
        <article class="home-news-card<?php echo $story_has_media ? ' has-media' : ' no-media'; ?>">
          <?php if ($story_has_media): ?><a class="home-news-media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>"><?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'full', false, array('loading'=>$story_index === 0 ? 'eager' : 'lazy','decoding'=>'async','sizes'=>'(max-width: 680px) 88vw, (max-width: 1100px) 68vw, 760px')); ?></a><?php endif; ?>
          <div class="home-news-copy">
            <span class="home-news-number" aria-hidden="true"><?php echo esc_html(str_pad((string) ($story_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
            <div class="home-top-meta"><?php if ($story_cats): ?><a href="<?php echo esc_url(get_category_link($story_cats[0]->term_id)); ?>"><?php echo esc_html($story_cats[0]->name); ?></a><span aria-hidden="true">·</span><?php endif; ?><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time></div>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php if ($story_excerpt): ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($story_excerpt), 34, '…')); ?></p><?php endif; ?>
            <a class="home-top-read" href="<?php the_permalink(); ?>"><?php echo esc_html($t['read']); ?> <span aria-hidden="true">→</span></a>
          </div>
        </article>
      </li>
      <?php endforeach; wp_reset_postdata(); ?>
    </ol>
    <?php else: ?><div class="media-empty"><?php echo esc_html($t['empty']); ?></div><?php endif; ?>
  </div>
</section>

<section class="home-intelligence-hero" id="cloud-business-intelligence" aria-labelledby="cloud-business-intelligence-title">
  <div class="container home-future-grid">
    <div class="home-future-copy">
      <span class="home-future-eyebrow section-brow home-section-brow"><?php echo esc_html($future['eyebrow']); ?></span>
      <h2 id="cloud-business-intelligence-title"><?php echo esc_html($future['title']); ?></h2>
      <p><?php echo esc_html($future['deck']); ?></p>
      <div class="home-future-actions"><a class="home-future-primary" href="https://guide.cloudin.asia/start" rel="external"><?php echo esc_html($future['guide']); ?> <span aria-hidden="true">↗</span></a><a href="https://guide.cloudin.asia/updates" rel="external"><?php echo esc_html($future['latest']); ?> <span aria-hidden="true">↗</span></a></div>
      <p class="home-evidence-line"><?php echo esc_html($lang === 'en' ? 'Cloud claims are everywhere. Cloud in Asia tracks the evidence.' : 'Klaim cloud ada di mana-mana. Cloud in Asia melacak buktinya.'); ?></p>
    </div>
    <div class="home-future-motion"><div class="home-motion-frame">
      <img class="home-map-only" src="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-southeast-asia-map-only.png'); ?>" alt="<?php echo esc_attr($lang === 'en' ? 'Map of Southeast Asia' : 'Peta Asia Tenggara'); ?>" width="1000" height="820" decoding="async">
    </div></div>
  </div>
  <div class="container home-guide-metrics" aria-label="<?php echo esc_attr($lang === 'en' ? 'Current Guide snapshot' : 'Snapshot Guide terkini'); ?>">
    <?php foreach ($metric_labels as $key=>$label): ?><div><strong><?php echo esc_html(isset($guide_metrics[$key]) ? number_format_i18n((int) $guide_metrics[$key]) : '—'); ?></strong><span><?php echo esc_html($label); ?></span></div><?php endforeach; ?>
    <p><?php echo esc_html($lang === 'en' ? 'Guide API snapshot' : 'Snapshot Guide API'); ?><?php if ($guide_checked): ?> · <?php echo esc_html($lang === 'en' ? 'checked ' . $guide_checked : 'diperiksa ' . $guide_checked); ?><?php endif; ?></p>
  </div>
</section>

<section class="home-guide-explorer" aria-labelledby="guide-explorer-title">
  <div class="container home-guide-explorer-grid">
    <div class="home-guide-explorer-copy"><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'Explore the ecosystem' : 'Jelajahi ekosistem'); ?></span><h2 id="guide-explorer-title"><?php echo esc_html($lang === 'en' ? 'Start with the problem, not the brand.' : 'Mulai dari masalah, bukan dari merek.'); ?></h2><p><?php echo esc_html($lang === 'en' ? 'Look up a provider from the current Guide dataset, or continue with need, comparison, and technology paths.' : 'Cari provider dari dataset Guide terkini, atau lanjutkan lewat kebutuhan, perbandingan, dan teknologi.'); ?></p></div>
    <div class="home-guide-search-shell">
      <form data-guide-provider-search action="https://guide.cloudin.asia/arena" method="get" novalidate>
        <label for="guide-provider-search"><?php echo esc_html($lang === 'en' ? 'Find a provider' : 'Cari provider'); ?></label>
        <div><input id="guide-provider-search" data-guide-provider-input type="search" autocomplete="off" placeholder="<?php echo esc_attr($lang === 'en' ? 'Type a provider name…' : 'Ketik nama provider…'); ?>" aria-controls="guide-provider-results" aria-describedby="guide-provider-help"><button type="submit"><?php echo esc_html($lang === 'en' ? 'Find' : 'Cari'); ?></button></div>
        <p id="guide-provider-help"><?php echo esc_html(sprintf($lang === 'en' ? '%d provider records available. Exact matches open the provider record.' : '%d record provider tersedia. Nama yang cocok akan membuka record provider.', count((array) ($guide_data['providers'] ?? array())))); ?></p>
        <ul id="guide-provider-results" data-guide-provider-results aria-live="polite" hidden></ul>
      </form>
      <script type="application/json" data-guide-provider-data><?php echo wp_json_encode($guide_data['providers'] ?? array(), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?></script>
      <nav class="home-guide-paths" aria-label="<?php echo esc_attr($lang === 'en' ? 'Guide paths' : 'Jalur Guide'); ?>"><a href="https://guide.cloudin.asia/start" rel="external"><?php echo esc_html($lang === 'en' ? 'Start from needs' : 'Mulai dari kebutuhan'); ?></a><a href="https://guide.cloudin.asia/arena" rel="external"><?php echo esc_html($lang === 'en' ? 'Compare providers' : 'Bandingkan provider'); ?></a><a href="https://guide.cloudin.asia/tech" rel="external"><?php echo esc_html($lang === 'en' ? 'Explore technology' : 'Jelajahi teknologi'); ?></a></nav>
    </div>
  </div>
</section>

<section class="home-cloud-pulse" aria-labelledby="cloud-pulse-title">
  <?php $guide_pulse = $guide_data['pulse']; ?>
  <div class="container">
    <header class="home-pulse-heading"><div><span class="media-kicker section-brow home-section-brow">Cloud Pulse</span><h2 id="cloud-pulse-title"><?php echo esc_html($lang === 'en' ? 'What changed in the Guide this week?' : 'Apa yang berubah di Guide minggu ini?'); ?></h2></div><p><?php echo esc_html($lang === 'en' ? 'API signals from the last seven days. One provider may appear more than once.' : 'Sinyal API selama tujuh hari terakhir. Satu provider dapat muncul lebih dari sekali.'); ?></p></header>
    <div class="home-pulse-layout">
      <div class="home-pulse-stats"><div><strong><?php echo esc_html(number_format_i18n((int) ($guide_pulse['signals_total'] ?? 0))); ?></strong><span><?php echo esc_html($lang === 'en' ? 'data signals' : 'sinyal data'); ?></span></div><div><strong><?php echo esc_html(number_format_i18n((int) ($guide_pulse['discovered'] ?? 0))); ?></strong><span>discovered</span></div><div><strong><?php echo esc_html(number_format_i18n((int) ($guide_pulse['updated'] ?? 0))); ?></strong><span>updated</span></div><div><strong><?php echo esc_html(number_format_i18n((int) ($guide_pulse['providers_touched'] ?? 0))); ?></strong><span><?php echo esc_html($lang === 'en' ? 'providers touched' : 'provider tersentuh'); ?></span></div></div>
      <?php if (!empty($guide_pulse['latest'])): ?><ol class="home-pulse-feed"><?php foreach (array_slice($guide_pulse['latest'], 0, 3) as $signal): $signal_title = $lang === 'en' ? ($signal['title_en'] ?: $signal['title_id']) : ($signal['title_id'] ?: $signal['title_en']); ?><li><span><?php echo esc_html($signal['kind']); ?></span><a href="<?php echo esc_url($signal['url']); ?>" rel="external"><?php echo esc_html($signal_title); ?></a><time datetime="<?php echo esc_attr($signal['occurred_at']); ?>"><?php echo esc_html(substr($signal['occurred_at'], 0, 10)); ?></time></li><?php endforeach; ?></ol><?php endif; ?>
    </div>
    <a class="home-pulse-cta" href="https://guide.cloudin.asia/updates" rel="external"><?php echo esc_html($lang === 'en' ? 'View all Guide updates' : 'Lihat semua pembaruan Guide'); ?> <span aria-hidden="true">↗</span></a>
  </div>
</section>

<section class="home-intent-journeys" aria-labelledby="home-journeys-title">
  <div class="container">
    <header class="home-journeys-heading"><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'What are you trying to do?' : 'Apa yang ingin Anda lakukan?'); ?></span><h2 id="home-journeys-title"><?php echo esc_html($lang === 'en' ? 'Four ways into the ecosystem.' : 'Empat jalur memahami ekosistem.'); ?></h2></header>
    <div class="home-journeys-grid">
      <a href="https://guide.cloudin.asia/start" rel="external"><span>01</span><h3>Find Cloud</h3><p><?php echo esc_html($lang === 'en' ? 'Research providers from your actual workload, location, control, and exit needs.' : 'Cari provider dari kebutuhan workload, lokasi, kontrol, dan rencana keluar.'); ?></p><b><?php echo esc_html($lang === 'en' ? 'Explore providers' : 'Jelajahi provider'); ?> →</b></a>
      <a href="<?php echo esc_url(cia_localized_url(home_url('/category/private-cloud/'), $lang)); ?>"><span>02</span><h3>Build Cloud</h3><p><?php echo esc_html($lang === 'en' ? 'Learn how open infrastructure can be built and owned.' : 'Pelajari bagaimana infrastruktur terbuka dapat dibangun dan dimiliki.'); ?></p><b><?php echo esc_html($lang === 'en' ? 'Build your own cloud' : 'Bangun cloud sendiri'); ?> →</b></a>
      <a href="<?php echo esc_url(cia_localized_url(home_url('/category/cloud-technology/'), $lang)); ?>"><span>03</span><h3>Understand Cloud</h3><p><?php echo esc_html($lang === 'en' ? 'Understand infrastructure and cloud economics without vendor jargon.' : 'Pahami infrastruktur dan ekonomi cloud tanpa jargon vendor.'); ?></p><b><?php echo esc_html($lang === 'en' ? 'Start learning' : 'Mulai belajar'); ?> →</b></a>
      <a href="<?php echo esc_url(cia_localized_url(home_url('/blog/'), $lang)); ?>"><span>04</span><h3>Follow the Market</h3><p><?php echo esc_html($lang === 'en' ? 'Follow provider, infrastructure, policy, and open-source movement.' : 'Ikuti pergerakan provider, infrastruktur, kebijakan, dan open source.'); ?></p><b><?php echo esc_html($lang === 'en' ? 'Latest intelligence' : 'Intelijen terbaru'); ?> →</b></a>
    </div>
  </div>
</section>

<?php if (!empty($discussed['institutions']) || !empty($discussed['people'])): ?>
<section class="home-discussed-section" aria-labelledby="home-discussed-title">
  <div class="container">
    <div class="home-discussed-intro">
      <div><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'In the current conversation' : 'Dalam percakapan terkini'); ?></span><h2 id="home-discussed-title"><?php echo esc_html($lang === 'en' ? 'Discussed across Cloud in Asia.' : 'Sedang dibahas di Cloud in Asia.'); ?></h2></div>
      <p><?php echo esc_html($lang === 'en' ? 'Based on explicit mentions in the latest audited articles—not an active partnership ranking.' : 'Berdasarkan penyebutan dalam artikel terbaru—bukan peringkat partner aktif.'); ?> <small><?php echo esc_html(sprintf($lang === 'en' ? '%d source articles' : '%d artikel sumber', (int) $discussed['source_posts'])); ?></small></p>
    </div>
    <div class="home-discussed-grid">
      <section class="home-discussed-panel home-discussed-institutions" aria-labelledby="home-discussed-institutions-title">
        <header><span aria-hidden="true">01</span><h3 id="home-discussed-institutions-title"><?php echo esc_html($lang === 'en' ? 'Institutions mentioned' : 'Institusi disebut'); ?></h3></header>
        <div class="home-discussed-list"><?php foreach ($discussed['institutions'] as $item): $mark = $item['mark']; ?><a href="<?php echo esc_url($item['url']); ?>">
          <span class="home-discussed-mark<?php echo $mark['type'] === 'initials' ? ' is-fallback' : (!empty($mark['variant']) && $mark['variant'] === 'dark' ? ' is-dark' : ''); ?>"><?php if ($mark['type'] === 'image'): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/' . $mark['path']); ?>" alt="" loading="eager"><?php else: ?><b aria-hidden="true"><?php echo esc_html($mark['text']); ?></b><?php endif; ?></span>
          <span><strong><?php echo esc_html($item['name']); ?></strong><small><?php echo esc_html(sprintf($lang === 'en' ? '%d recent article(s)' : '%d artikel terbaru', (int) $item['articles'])); ?></small></span><i aria-hidden="true">↗</i>
        </a><?php endforeach; ?></div>
      </section>
      <section class="home-discussed-panel home-discussed-people" aria-labelledby="home-discussed-people-title">
        <header><span aria-hidden="true">02</span><h3 id="home-discussed-people-title"><?php echo esc_html($lang === 'en' ? 'People mentioned' : 'Figur disebut'); ?></h3></header>
        <div class="home-discussed-list"><?php foreach ($discussed['people'] as $item): ?><a href="<?php echo esc_url($item['url']); ?>">
          <?php if (!empty($item['portrait'])): ?><img class="home-discussed-portrait" src="<?php echo esc_url(get_template_directory_uri() . '/' . $item['portrait']); ?>" alt="" loading="eager"><?php else: ?><span class="home-discussed-portrait is-fallback" aria-hidden="true"><?php echo esc_html(cia_entity_initials($item['name'])); ?></span><?php endif; ?>
          <span><strong><?php echo esc_html($item['name']); ?></strong><small><?php echo esc_html(sprintf($lang === 'en' ? '%d recent article(s)' : '%d artikel terbaru', (int) $item['articles'])); ?></small></span><i aria-hidden="true">↗</i>
        </a><?php endforeach; ?></div>
      </section>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="media-editorial-desk">
  <div class="container">
    <div class="media-desk-intro"><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($t['desk']); ?></span><h2><?php echo esc_html($t['desk_title']); ?></h2><p><?php echo esc_html($t['desk_note']); ?></p></div>
    <?php
    $desk_query = new WP_Query(cia_editorial_query_args($lang, array('posts_per_page'=>6,'offset'=>5,'ignore_sticky_posts'=>true)));
    $desk_posts = $desk_query->posts;
    if ($desk_posts):
      $desk_lead = array_shift($desk_posts);
      $post = $desk_lead;
      setup_postdata($post);
      $desk_read_label = $lang === 'en' ? 'Read article' : 'Baca artikel';
      $desk_list_label = $lang === 'en' ? 'More from the editorial desk' : 'Lainnya dari meja editorial';
      $desk_lead_label = $lang === 'en' ? 'Editorial pick' : 'Pilihan redaksi';
    ?>
    <div class="media-desk-layout">
      <article class="media-desk-lead">
        <div class="media-desk-lead-copy">
          <div class="media-desk-lead-eyebrow"><span><?php echo esc_html($desk_lead_label); ?></span><span aria-hidden="true">01</span></div>
          <div class="media-byline"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j M Y')); ?></time><?php $cats = get_the_category(); if ($cats): ?><span><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?></div>
          <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          <?php $desk_excerpt = trim((string) get_the_excerpt()); if ($desk_excerpt): ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($desk_excerpt), 28, '…')); ?></p><?php endif; ?>
          <a class="media-text-link" href="<?php the_permalink(); ?>"><?php echo esc_html($desk_read_label); ?> <span aria-hidden="true">→</span></a>
        </div>
      </article>
      <ol class="media-desk-list" aria-label="<?php echo esc_attr($desk_list_label); ?>">
        <?php foreach ($desk_posts as $desk_index => $post): setup_postdata($post); ?>
        <li>
          <article class="media-desk-row">
            <span class="media-desk-number" aria-hidden="true"><?php echo esc_html(str_pad((string) ($desk_index + 2), 2, '0', STR_PAD_LEFT)); ?></span>
            <div>
              <div class="media-byline"><time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j M Y')); ?></time><?php $cats = get_the_category(); if ($cats): ?><span><?php echo esc_html($cats[0]->name); ?></span><?php endif; ?></div>
              <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            </div>
            <span class="media-desk-arrow" aria-hidden="true">↗</span>
          </article>
        </li>
        <?php endforeach; ?>
      </ol>
    </div>
    <?php wp_reset_postdata(); else: ?><div class="media-empty"><?php echo esc_html($t['empty']); ?></div><?php endif; ?>
  </div>
</section>

<section class="media-event-ledger">
  <div class="container">
    <?php if ($upcoming_events->have_posts()): ?>
      <div class="media-section-heading media-event-heading"><div><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'Upcoming' : 'Akan Datang'); ?></span><h2><?php echo esc_html($lang === 'en' ? 'Meet the ecosystem.' : 'Temui ekosistemnya.'); ?></h2><p><?php echo esc_html($lang === 'en' ? 'The next event with a verified future activity date.' : 'Event berikutnya dengan tanggal pelaksanaan mendatang yang terverifikasi.'); ?></p></div><a href="<?php echo esc_url(cia_archive_link('events','/events/')); ?>"><?php echo esc_html($t['all_events']); ?> <span aria-hidden="true">→</span></a></div>
      <div class="event-ledger-rows"><?php while ($upcoming_events->have_posts()): $upcoming_events->the_post(); ?><section class="event-ledger-group"><div class="event-ledger-type"><span><?php echo esc_html($lang === 'en' ? 'Next event' : 'Event berikutnya'); ?></span></div><div class="event-ledger-items"><article><time><?php echo esc_html(cia_event_display_date() ?: $t['date_missing']); ?></time><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><span aria-hidden="true">↗</span></article></div></section><?php endwhile; wp_reset_postdata(); ?></div>
    <?php else: ?>
      <div class="media-section-heading media-event-heading"><div><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($lang === 'en' ? 'Watch & Learn' : 'Tonton & Pelajari'); ?></span><h2><?php echo esc_html($lang === 'en' ? 'Conversations become durable knowledge.' : 'Percakapan menjadi pengetahuan yang bertahan.'); ?></h2><p><?php echo esc_html($lang === 'en' ? 'Browse past events, sessions, speakers, and material from the cloud ecosystem.' : 'Jelajahi arsip event, sesi, speaker, dan materi dari ekosistem cloud.'); ?></p></div><a href="<?php echo esc_url(cia_archive_link('events','/events/')); ?>"><?php echo esc_html($lang === 'en' ? 'Browse sessions' : 'Jelajahi sesi'); ?> <span aria-hidden="true">→</span></a></div>
    <?php endif; ?>
  </div>
</section>

<section class="media-collaborators">
  <div class="container">
    <div class="media-collab-heading"><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($t['collab_kicker']); ?></span><h2><?php echo esc_html($t['collab_title']); ?></h2><p><?php echo esc_html($t['collab_note']); ?></p></div>
    <div class="media-collab-list">
      <?php foreach ($collaborators as $i => $item): ?><article class="media-collab-item collaborator-<?php echo esc_attr($item['slug']); ?>"><div><h3><?php echo esc_html($item['name']); ?></h3><p><?php echo esc_html($t['roles'][$i]); ?></p><a href="<?php echo esc_url(get_permalink($item['evidence'])); ?>"><?php echo esc_html($t['evidence']); ?> <span aria-hidden="true">→</span></a></div></article><?php endforeach; ?>
    </div>
  </div>
</section>

<section class="media-topics">
  <div class="container"><span class="media-kicker section-brow home-section-brow"><?php echo esc_html($t['topics']); ?></span><h2><?php echo esc_html($t['topics_title']); ?></h2><div class="media-topic-list"><?php foreach (get_categories(array('orderby'=>'count','order'=>'DESC','number'=>12,'hide_empty'=>true)) as $category): ?><a href="<?php echo esc_url(cia_localized_url(get_category_link($category), $lang)); ?>"><span><?php echo esc_html($category->name); ?></span><small><?php echo esc_html($category->count); ?></small></a><?php endforeach; ?></div></div>
</section>
</div>
<?php get_footer(); ?>
