<?php
if (!defined('ABSPATH')) { exit; }
function cia_recovery_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('responsive-embeds');
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
    add_image_size('cia-card', 720, 450, true);
    add_image_size('cia-speaker', 720, 720, true);
    register_nav_menus(array('primary' => __('Primary navigation', 'cia-recovery')));
}
add_action('after_setup_theme', 'cia_recovery_setup');
function cia_recovery_asset_version($relative_path) {
    $path = get_template_directory() . '/' . ltrim($relative_path, '/');
    $theme_version = wp_get_theme()->get('Version');
    return is_file($path) ? $theme_version . '-' . substr(hash_file('sha256', $path), 0, 12) : $theme_version;
}
function cia_recovery_assets() {
    wp_enqueue_style('cia-recovery', get_stylesheet_uri(), array(), cia_recovery_asset_version('style.css'));
    wp_enqueue_style('cia-theme-surfaces', get_template_directory_uri() . '/assets/cia-theme-surfaces.css', array('cia-recovery'), cia_recovery_asset_version('assets/cia-theme-surfaces.css'));
    wp_enqueue_style('cia-editorial-refinement', get_template_directory_uri() . '/assets/cia-editorial-refinement.css', array('cia-theme-surfaces'), cia_recovery_asset_version('assets/cia-editorial-refinement.css'));
    if (is_post_type_archive('events') || is_singular('events') || is_page('partners')) {
        wp_enqueue_style('cia-events-collaborators', get_template_directory_uri() . '/assets/events-collaborators.css', array('cia-editorial-refinement'), cia_recovery_asset_version('assets/events-collaborators.css'));
    }
    if (is_page('about')) {
        wp_enqueue_style('cia-about', get_template_directory_uri() . '/assets/cia-about.css', array('cia-editorial-refinement'), cia_recovery_asset_version('assets/cia-about.css'));
    }
    if (is_search()) {
        wp_enqueue_style('cia-search-archive', get_template_directory_uri() . '/assets/search-archive.css', array('cia-editorial-refinement'), cia_recovery_asset_version('assets/search-archive.css'));
    }
    wp_enqueue_style('cia-chrome', get_template_directory_uri() . '/assets/cia-chrome.css', array('cia-editorial-refinement'), cia_recovery_asset_version('assets/cia-chrome.css'));
    wp_enqueue_script('cia-theme-mode', get_template_directory_uri() . '/assets/theme-mode.js', array(), cia_recovery_asset_version('assets/theme-mode.js'), false);
    wp_enqueue_script('cia-chrome', get_template_directory_uri() . '/assets/cia-chrome.js', array('cia-theme-mode'), cia_recovery_asset_version('assets/cia-chrome.js'), true);
    if (is_front_page()) {
        wp_enqueue_script('cia-home-news-carousel', get_template_directory_uri() . '/assets/home-news-carousel.js', array(), cia_recovery_asset_version('assets/home-news-carousel.js'), true);
        wp_enqueue_script('cia-home-guide', get_template_directory_uri() . '/assets/home-guide.js', array(), cia_recovery_asset_version('assets/home-guide.js'), true);
        wp_localize_script('cia-home-guide', 'ciaHomeGuide', array(
            'analyticsEvent'=>'homepage_guide_search',
            'source'=>'https://guide.cloudin.asia/api',
        ));
    }
    if (is_home()) {
        wp_enqueue_script('cia-lottie', get_template_directory_uri() . '/assets/vendor/lottie-light.min.js', array(), '5.13.0', true);
        wp_enqueue_script('cia-blog-motion', get_template_directory_uri() . '/assets/blog-motion.js', array('cia-lottie'), cia_recovery_asset_version('assets/blog-motion.js'), true);
    }
    if (is_singular('post')) {
        wp_enqueue_script('cia-article-share', get_template_directory_uri() . '/assets/article-share.js', array(), cia_recovery_asset_version('assets/article-share.js'), true);
    }
    if (is_post_type_archive('events')) {
        wp_enqueue_script('cia-event-explore', get_template_directory_uri() . '/assets/event-explore.js', array(), cia_recovery_asset_version('assets/event-explore.js'), true);
        wp_enqueue_script('cia-event-map', get_template_directory_uri() . '/assets/event-map.js', array('cia-event-explore'), cia_recovery_asset_version('assets/event-map.js'), true);
    }
    if (is_singular('events')) {
        wp_enqueue_script('cia-event-gallery', get_template_directory_uri() . '/assets/event-gallery.js', array(), cia_recovery_asset_version('assets/event-gallery.js'), true);
        wp_enqueue_script('cia-event-detail', get_template_directory_uri() . '/assets/event-detail.js', array(), cia_recovery_asset_version('assets/event-detail.js'), true);
    }
    if (is_archive() && !is_post_type_archive('events')) {
        wp_enqueue_script('cia-archive-portal', get_template_directory_uri() . '/assets/archive-portal.js', array(), cia_recovery_asset_version('assets/archive-portal.js'), true);
    }
}
add_action('wp_enqueue_scripts', 'cia_recovery_assets');
function cia_home_language() {
    if (isset($_GET['lang'])) {
        $requested = sanitize_key(wp_unslash($_GET['lang']));
        if (in_array($requested, array('id', 'en'), true)) { return $requested; }
    }
    if (isset($_COOKIE['cia_lang'])) {
        $cookie = sanitize_key(wp_unslash($_COOKIE['cia_lang']));
        if (in_array($cookie, array('id', 'en'), true)) { return $cookie; }
    }
    return 'id';
}
function cia_persist_language_preference() {
    if (!isset($_GET['lang']) || headers_sent()) { return; }
    $language = cia_home_language();
    setcookie('cia_lang', $language, array(
        'expires' => time() + YEAR_IN_SECONDS,
        'path' => '/',
        'secure' => is_ssl(),
        'httponly' => false,
        'samesite' => 'Lax',
    ));
    $_COOKIE['cia_lang'] = $language;
}
add_action('init', 'cia_persist_language_preference', 1);
function cia_home_language_url($language) {
    $language = in_array($language, array('id', 'en'), true) ? $language : 'id';
    return add_query_arg('lang', $language, home_url('/'));
}
function cia_switch_language_url($language) {
    $language = in_array($language, array('id', 'en'), true) ? $language : 'id';
    if (is_singular('post') && function_exists('cia_translation_locale') && function_exists('cia_translation_pair')) {
        $post_id = get_queried_object_id();
        if (cia_translation_locale($post_id) === $language) { return get_permalink($post_id); }
        $pair_id = cia_translation_pair($post_id, true);
        if ($pair_id && cia_translation_locale($pair_id) === $language) { return get_permalink($pair_id); }
    }
    $request = wp_unslash($_SERVER['REQUEST_URI'] ?? '/');
    $path = (string) wp_parse_url($request, PHP_URL_PATH);
    $query = (string) wp_parse_url($request, PHP_URL_QUERY);
    $args = array();
    if ($query !== '') { parse_str($query, $args); }
    unset($args['lang']);
    $args['lang'] = $language;
    $url = home_url($path !== '' ? $path : '/');
    return add_query_arg($args, $url);
}
function cia_localized_url($url, $language = null) {
    $language = $language ?: cia_home_language();
    return $language === 'en' ? add_query_arg('lang', 'en', $url) : remove_query_arg('lang', $url);
}
/**
 * Translate the small set of editorial taxonomy labels that are genuinely
 * language-specific. Slugs and term IDs remain stable so existing archives,
 * feeds, and inbound links do not change.
 */
function cia_localized_term_name($term, $language = null) {
    if (!($term instanceof WP_Term) || $term->taxonomy !== 'category') { return $term instanceof WP_Term ? $term->name : ''; }
    $language = $language ?: cia_home_language();
    if ($language !== 'en') { return $term->name; }
    $labels = array(
        'berita'=>'News',
        'infrastruktur'=>'Infrastructure',
        'kebijakan'=>'Policy',
        'opini'=>'Opinion',
        'tutorial'=>'Tutorials',
        'uncategorized'=>'Uncategorized',
    );
    return $labels[$term->slug] ?? $term->name;
}
function cia_localize_single_term($term, $taxonomy) {
    if (is_admin() || cia_home_language() !== 'en' || !($term instanceof WP_Term) || $taxonomy !== 'category') { return $term; }
    $localized = clone $term;
    $localized->name = cia_localized_term_name($term, 'en');
    return $localized;
}
add_filter('get_term', 'cia_localize_single_term', 20, 2);
function cia_localize_term_collection($terms) {
    if (is_admin() || cia_home_language() !== 'en' || !is_array($terms)) { return $terms; }
    foreach ($terms as $index=>$term) {
        if (!($term instanceof WP_Term) || $term->taxonomy !== 'category') { continue; }
        $localized = clone $term;
        $localized->name = cia_localized_term_name($term, 'en');
        $terms[$index] = $localized;
    }
    return $terms;
}
add_filter('get_terms', 'cia_localize_term_collection', 20, 1);
function cia_collaborators_alias_redirect() {
    if (!is_404()) { return; }
    $path = trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($path !== 'collaborators') { return; }
    wp_safe_redirect(cia_localized_url(home_url('/partners/'), cia_home_language()), 301);
    exit;
}
add_action('template_redirect', 'cia_collaborators_alias_redirect');
function cia_editorial_query_args($language, $overrides = array()) {
    $language = in_array($language, array('id', 'en'), true) ? $language : 'id';
    return array_merge(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'meta_query' => array(array('key'=>'_cia_locale', 'value'=>$language, 'compare'=>'=')),
    ), $overrides);
}
function cia_filter_editorial_archives_by_locale($query) {
    if (is_admin() || !$query->is_main_query()) { return; }
    if (!($query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date())) { return; }
    $query->set('post_type', 'post');
    $query->set('meta_query', array(array('key'=>'_cia_locale', 'value'=>cia_home_language(), 'compare'=>'=')));
}
add_action('pre_get_posts', 'cia_filter_editorial_archives_by_locale');
function cia_home_dateline($language = 'id') {
    $now = new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta'));
    if ($language === 'en') { return $now->format('l, j F Y'); }
    $days = array(1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu');
    $months = array(1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember');
    return $days[(int) $now->format('N')] . ', ' . $now->format('j') . ' ' . $months[(int) $now->format('n')] . ' ' . $now->format('Y');
}
function cia_guide_safe_url($value, $provider_id = '') {
    $value = trim((string) $value);
    if (str_starts_with($value, '/')) { $value = 'https://guide.cloudin.asia' . $value; }
    $host = strtolower((string) wp_parse_url($value, PHP_URL_HOST));
    $scheme = strtolower((string) wp_parse_url($value, PHP_URL_SCHEME));
    if ($scheme === 'https' && $host === 'guide.cloudin.asia') { return $value; }
    $provider_id = preg_replace('/[^a-zA-Z0-9._-]/', '', (string) $provider_id);
    return $provider_id !== '' ? 'https://guide.cloudin.asia/provider/' . rawurlencode($provider_id) : 'https://guide.cloudin.asia/';
}
function cia_guide_homepage_build($providers_payload, $buildings_payload, $updates_payload, $generated_at = null) {
    $providers = isset($providers_payload['providers']) && is_array($providers_payload['providers']) ? $providers_payload['providers'] : array();
    $buildings = isset($buildings_payload['buildings']) && is_array($buildings_payload['buildings']) ? $buildings_payload['buildings'] : array();
    $updates = isset($updates_payload['updates']) && is_array($updates_payload['updates']) ? $updates_payload['updates'] : array();
    if (!$providers || !isset($providers_payload['count']) || !isset($buildings_payload['count'])) { return null; }
    $now = $generated_at instanceof DateTimeImmutable ? $generated_at : new DateTimeImmutable('now', new DateTimeZone('UTC'));
    $now = $now->setTimezone(new DateTimeZone('UTC'));
    $cutoff = $now->sub(new DateInterval('P7D'));
    $provider_rows = array();
    $providers_asean = 0;
    $plans_total = 0;
    foreach ($providers as $row) {
        if (!is_array($row)) { continue; }
        $id = trim((string) ($row['id'] ?? ''));
        $name = trim(wp_strip_all_tags((string) ($row['name'] ?? '')));
        if ($id === '' || $name === '') { continue; }
        $provider_rows[] = array('id'=>$id, 'name'=>$name, 'url'=>cia_guide_safe_url((string) ($row['website_path'] ?? ''), $id));
        $providers_asean += !empty($row['is_local_asean']) ? 1 : 0;
        $plans_total += max(0, (int) ($row['tier_count'] ?? 0));
    }
    usort($provider_rows, static fn($a, $b) => strcasecmp($a['name'], $b['name']));
    $pulse_rows = array();
    foreach ($updates as $row) {
        if (!is_array($row)) { continue; }
        $kind = sanitize_key((string) ($row['kind'] ?? ''));
        if (!in_array($kind, array('discovered','updated'), true)) { continue; }
        try { $occurred = new DateTimeImmutable((string) ($row['occurred_at'] ?? ''), new DateTimeZone('UTC')); } catch (Exception $error) { continue; }
        $occurred = $occurred->setTimezone(new DateTimeZone('UTC'));
        if ($occurred < $cutoff || $occurred > $now) { continue; }
        $provider_id = trim((string) ($row['provider_id'] ?? ''));
        $pulse_rows[] = array(
            'provider_id'=>$provider_id,
            'kind'=>$kind,
            'occurred_at'=>$occurred->format('Y-m-d\TH:i:s\Z'),
            'title_id'=>trim(wp_strip_all_tags((string) ($row['title_id'] ?? ''))),
            'title_en'=>trim(wp_strip_all_tags((string) ($row['title_en'] ?? ''))),
            'summary_id'=>trim(wp_strip_all_tags((string) ($row['summary_id'] ?? ''))),
            'summary_en'=>trim(wp_strip_all_tags((string) ($row['summary_en'] ?? ''))),
            'url'=>cia_guide_safe_url((string) ($row['href'] ?? ''), $provider_id),
        );
    }
    usort($pulse_rows, static fn($a, $b) => strcmp($b['occurred_at'], $a['occurred_at']));
    $provider_ids = array_filter(array_column($pulse_rows, 'provider_id'));
    return array(
        'schema'=>'cia.guide_homepage.v1',
        'source'=>'https://guide.cloudin.asia/api',
        'generated_at'=>$now->format('Y-m-d\TH:i:s\Z'),
        'metrics'=>array(
            'providers_total'=>(int) $providers_payload['count'],
            'providers_asean'=>$providers_asean,
            'plans_total'=>$plans_total,
            'facilities_total'=>(int) $buildings_payload['count'],
        ),
        'pulse'=>array(
            'window_days'=>7,
            'signals_total'=>count($pulse_rows),
            'providers_touched'=>count(array_unique($provider_ids)),
            'discovered'=>count(array_filter($pulse_rows, static fn($row) => $row['kind'] === 'discovered')),
            'updated'=>count(array_filter($pulse_rows, static fn($row) => $row['kind'] === 'updated')),
            'latest'=>array_slice($pulse_rows, 0, 6),
        ),
        'providers'=>$provider_rows,
    );
}
function cia_guide_homepage_valid($data) {
    return is_array($data)
        && ($data['schema'] ?? '') === 'cia.guide_homepage.v1'
        && ($data['source'] ?? '') === 'https://guide.cloudin.asia/api'
        && isset($data['metrics'], $data['pulse'], $data['providers'])
        && is_array($data['metrics']) && is_array($data['pulse']) && is_array($data['providers']);
}
function cia_guide_homepage_data() {
    static $resolved = null;
    if ($resolved !== null) { return $resolved; }
    $cache_key = 'cia_guide_homepage_v1';
    $cached = get_transient($cache_key);
    if (cia_guide_homepage_valid($cached)) { $cached['delivery'] = 'live-cache'; return $resolved = $cached; }
    $payloads = array();
    foreach (array('providers','buildings','updates') as $resource) {
        $response = wp_remote_get('https://guide.cloudin.asia/api/' . $resource, array(
            'timeout'=>2,
            'redirection'=>0,
            'sslverify'=>true,
            'headers'=>array('Accept'=>'application/json','User-Agent'=>'Cloud-in-Asia-homepage/1.0'),
        ));
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) { $payloads = array(); break; }
        $body = (string) wp_remote_retrieve_body($response);
        if ($body === '' || strlen($body) > 1500000) { $payloads = array(); break; }
        $decoded = json_decode($body, true);
        if (!is_array($decoded)) { $payloads = array(); break; }
        $payloads[$resource] = $decoded;
    }
    if (count($payloads) === 3) {
        $live = cia_guide_homepage_build($payloads['providers'], $payloads['buildings'], $payloads['updates']);
        if (cia_guide_homepage_valid($live)) {
            set_transient($cache_key, $live, 15 * MINUTE_IN_SECONDS);
            $live['delivery'] = 'live';
            return $resolved = $live;
        }
    }
    $path = get_template_directory() . '/assets/data/guide-homepage-snapshot.json';
    $fallback = is_readable($path) ? json_decode((string) file_get_contents($path), true) : array();
    if (cia_guide_homepage_valid($fallback)) { $fallback['delivery'] = 'snapshot'; return $resolved = $fallback; }
    return $resolved = array('schema'=>'cia.guide_homepage.v1','source'=>'https://guide.cloudin.asia/api','generated_at'=>'','metrics'=>array(),'pulse'=>array(),'providers'=>array(),'delivery'=>'unavailable');
}
function cia_editorial_date($post_id = 0, $language = null) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $language = $language ?: ((is_singular('post') && function_exists('cia_translation_locale')) ? cia_translation_locale($post_id) : cia_home_language());
    $timestamp = get_post_timestamp($post_id);
    if (!$timestamp) { return ''; }
    $months_id = array(1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember');
    $months_en = array(1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December');
    $month = ($language === 'en' ? $months_en : $months_id)[(int) wp_date('n', $timestamp)];
    return ($language === 'en')
        ? $month . ' ' . wp_date('j, Y', $timestamp)
        : wp_date('j', $timestamp) . ' ' . $month . ' ' . wp_date('Y', $timestamp);
}
function cia_people_directory_data() {
    static $data = null;
    if ($data !== null) { return $data; }
    $path = get_template_directory() . '/assets/data/speaker-directory.json';
    if (!is_readable($path)) { return array('records'=>array()); }
    $decoded = json_decode((string) file_get_contents($path), true);
    $data = is_array($decoded) && isset($decoded['records']) && is_array($decoded['records']) ? $decoded : array('records'=>array());
    return $data;
}
function cia_people_directory_record($person_id) {
    foreach (cia_people_directory_data()['records'] as $record) {
        if ((int) $record['person_id'] === (int) $person_id) { return $record; }
    }
    return null;
}
function cia_partner_logo_catalog() {
    static $catalog = null;
    if ($catalog !== null) { return $catalog; }
    $catalog = array();
    $directory = get_template_directory() . '/assets/partner-logos/';
    $path = $directory . 'catalog.json';
    $rows = is_readable($path) ? json_decode((string) file_get_contents($path), true) : array();
    foreach (is_array($rows) ? $rows : array() as $row) {
        $slug = sanitize_key((string) ($row['slug'] ?? ''));
        $file = (string) ($row['file'] ?? '');
        $sha256 = strtolower((string) ($row['sha256'] ?? ''));
        if (!$slug || !preg_match('/^[a-z0-9._-]+\.(?:png|svg)$/', $file) || !preg_match('/^[a-f0-9]{64}$/', $sha256)) { continue; }
        $asset = $directory . $file;
        if (!is_file($asset) || !hash_equals($sha256, hash_file('sha256', $asset))) { continue; }
        $catalog[$slug] = $row;
    }
    return $catalog;
}
function cia_partner_logo_record($slug) {
    $aliases = array('kementrian-komunikasi-dan-digital'=>'komdigi');
    $key = $aliases[$slug] ?? $slug;
    $catalog = cia_partner_logo_catalog();
    return $catalog[$key] ?? null;
}
function cia_event_locations_data() {
    static $data = null;
    if ($data !== null) { return $data; }
    $path = get_template_directory() . '/assets/data/event-locations.json';
    $decoded = is_readable($path) ? json_decode((string) file_get_contents($path), true) : array();
    $data = is_array($decoded) && isset($decoded['records']) && is_array($decoded['records']) ? $decoded : array('records'=>array());
    return $data;
}
function cia_event_location_for_venue($venue) {
    $venue = trim(wp_strip_all_tags((string) $venue));
    if ($venue === '') { return null; }
    $normalize = static function($value) {
        $value = trim((string) $value);
        return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
    };
    $needle = $normalize($venue);
    foreach (cia_event_locations_data()['records'] as $record) {
        foreach ((array) ($record['aliases'] ?? array()) as $alias) {
            if ($normalize($alias) === $needle) { return $record; }
        }
    }
    return null;
}
function cia_home_alternate_languages() {
    $shared_surface = is_front_page() || is_home() || is_category() || is_page(array('about','speakers','partners','editorial-policy','corrections-policy','disclosure','privacy-policy','terms-of-use','contact'));
    if (!$shared_surface) { return; }
    $request = wp_unslash($_SERVER['REQUEST_URI'] ?? '/');
    $path = (string) wp_parse_url($request, PHP_URL_PATH);
    $base = home_url($path !== '' ? $path : '/');
    $id_url = remove_query_arg('lang', $base);
    $en_url = add_query_arg('lang', 'en', $base);
    echo '<link rel="alternate" hreflang="id" href="' . esc_url($id_url) . '">' . "\n";
    echo '<link rel="alternate" hreflang="en" href="' . esc_url($en_url) . '">' . "\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($id_url) . '">' . "\n";
}
add_action('wp_head', 'cia_home_alternate_languages', 2);
function cia_seo_canonical_url() {
    if (is_404() || is_search()) { return ''; }
    if (is_front_page()) { return cia_localized_url(home_url('/'), cia_home_language()); }
    if (is_singular('post')) { return get_permalink(); }
    if (is_singular()) { return cia_localized_url(get_permalink(), cia_home_language()); }
    if (is_home()) {
        $url = get_option('page_for_posts') ? get_permalink((int) get_option('page_for_posts')) : home_url('/');
        return cia_localized_url($url, cia_home_language());
    }
    if (is_post_type_archive()) { return get_post_type_archive_link(get_query_var('post_type')); }
    if (is_category() || is_tag() || is_tax()) {
        $link = get_term_link(get_queried_object());
        if (is_wp_error($link)) { return ''; }
        $url = $link;
        return cia_localized_url($url, cia_home_language());
    }
    return '';
}
function cia_seo_head() {
    $canonical = cia_seo_canonical_url();
    if ($canonical) { echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n"; }
    $description = cia_seo_meta_description();
    if ($description) { echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n"; }
}
function cia_seo_meta_description() {
    if (is_front_page()) { return cia_home_language() === 'en' ? "Independent intelligence on Southeast Asia's cloud providers, infrastructure, open source, pricing, AI infrastructure, and digital policy." : 'Intelijen independen tentang provider cloud, infrastruktur, open source, harga, AI infrastructure, dan kebijakan digital Asia Tenggara.'; }
    if (is_home()) { return cia_home_language() === 'en' ? 'The latest Cloud in Asia reporting and analysis on cloud, open source, infrastructure, and digital sovereignty.' : 'Berita dan analisis terbaru Cloud in Asia tentang cloud, open source, infrastruktur, dan kedaulatan digital.'; }
    if (is_page('about')) { return cia_home_language() === 'en' ? 'Cloud in Asia is a media and ecosystem platform connecting communities, industry, talent, and cloud technology across Asia.' : 'Cloud in Asia adalah media dan ecosystem platform yang menghubungkan komunitas, industri, talenta, dan teknologi cloud di Asia.'; }
    if (is_page('speakers')) { return cia_home_language() === 'en' ? 'A directory of event speakers and statement sources explicitly recorded in Cloud in Asia publications.' : 'Direktori pembicara event dan sumber pernyataan yang tercatat secara eksplisit dalam publikasi Cloud in Asia.'; }
    if (is_page('partners')) { return cia_home_language() === 'en' ? 'Organizations recorded in published Cloud in Asia event data, grouped by documented role without implying an active relationship.' : 'Organisasi dalam data event terpublikasi Cloud in Asia, dikelompokkan menurut peran tercatat tanpa menyiratkan hubungan aktif.'; }
    if (is_page()) {
        $page_id = get_queried_object_id();
        $meta_key = cia_home_language() === 'en' ? '_cia_description_en' : '_cia_description_id';
        $page_description = trim((string) get_post_meta($page_id, $meta_key, true));
        if ($page_description !== '') { return $page_description; }
    }
    if (is_singular()) {
        $post = get_queried_object();
        if (!$post instanceof WP_Post) { return ''; }
        $text = has_excerpt($post) ? get_the_excerpt($post) : wp_strip_all_tags(strip_shortcodes($post->post_content));
        if ($text === '') { $text = get_the_title($post); }
        return wp_trim_words(preg_replace('/\s+/', ' ', $text), 28, '…');
    }
    if (is_post_type_archive('events')) { return 'Agenda webinar, meetup, conference, dan workshop Cloud in Asia dengan metadata acara yang telah dipulihkan.'; }
    if (is_archive()) {
        $description = wp_strip_all_tags(get_the_archive_description());
        return $description ?: wp_strip_all_tags(get_the_archive_title()) . (cia_home_language() === 'en' ? ' — Cloud in Asia archive.' : ' — arsip Cloud in Asia.');
    }
    return '';
}
function cia_structured_data() {
    $graph = array();
    if (is_front_page()) {
        $graph[] = array('@type'=>'WebSite','@id'=>home_url('/#website'),'url'=>home_url('/'),'name'=>'Cloud in Asia','inLanguage'=>cia_home_language());
        $graph[] = array('@type'=>'Organization','@id'=>home_url('/#organization'),'name'=>'Cloud in Asia','url'=>home_url('/'));
    } elseif (is_singular('post')) {
        $id = get_queried_object_id();
        $graph[] = array('@type'=>'NewsArticle','@id'=>get_permalink($id).'#article','headline'=>get_the_title($id),'url'=>get_permalink($id),'datePublished'=>get_post_time(DATE_W3C, true, $id),'dateModified'=>get_post_modified_time(DATE_W3C, true, $id),'inLanguage'=>function_exists('cia_translation_locale') ? cia_translation_locale($id) : 'id','publisher'=>array('@id'=>home_url('/#organization')));
    } elseif (is_singular('events')) {
        $id = get_queried_object_id();
        $start = cia_event_datetime($id, 'event_start');
        if ($start) {
            $event = array('@type'=>'Event','@id'=>get_permalink($id).'#event','name'=>get_the_title($id),'url'=>get_permalink($id),'startDate'=>$start->format(DATE_W3C),'eventStatus'=>'https://schema.org/EventScheduled');
            $end = cia_event_datetime($id, 'event_end');
            if ($end) { $event['endDate'] = $end->format(DATE_W3C); }
            $venue = trim((string) get_post_meta($id, 'venue', true));
            if ($venue !== '') {
                $online = preg_match('/\b(zoom|online|webinar|virtual)\b/i', $venue);
                $event['eventAttendanceMode'] = $online ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode';
                $event['location'] = $online ? array('@type'=>'VirtualLocation','name'=>$venue,'url'=>get_permalink($id)) : array('@type'=>'Place','name'=>$venue);
            }
            $graph[] = $event;
        }
    }
    if (!$graph) { return; }
    echo '<script type="application/ld+json">' . wp_json_encode(array('@context'=>'https://schema.org','@graph'=>$graph), JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}
add_action('wp_head', 'cia_seo_head', 3);
add_action('wp_head', 'cia_structured_data', 4);
function cia_social_meta() {
    $policy_pages = array('about','editorial-policy','corrections-policy','disclosure','privacy-policy','terms-of-use','contact');
    if (is_page($policy_pages)) {
        $title = wp_strip_all_tags(get_the_title(get_queried_object_id()));
        if (cia_home_language() === 'en') {
            $english_title = trim((string) get_post_meta(get_queried_object_id(), '_cia_title_en', true));
            if ($english_title !== '') { $title = $english_title; }
        }
        $url = cia_seo_canonical_url();
        $description = cia_seo_meta_description();
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:locale" content="' . esc_attr(cia_home_language() === 'en' ? 'en_US' : 'id_ID') . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        if ($description) { echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n"; }
        echo '<meta name="twitter:card" content="summary">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        return;
    }
    if (!is_singular('post')) { return; }
    $post_id = get_queried_object_id();
    $attachment_id = (int) get_post_thumbnail_id($post_id);
    if (!$attachment_id) { return; }
    $image_url = wp_get_attachment_image_url($attachment_id, 'full');
    if (!$image_url) { return; }
    $metadata = wp_get_attachment_metadata($attachment_id);
    $width = is_array($metadata) ? (int) ($metadata['width'] ?? 0) : 0;
    $height = is_array($metadata) ? (int) ($metadata['height'] ?? 0) : 0;
    $locale = function_exists('cia_translation_locale') ? cia_translation_locale($post_id) : 'id';
    $og_locale = $locale === 'en' ? 'en_US' : 'id_ID';
    $title = wp_strip_all_tags(get_the_title($post_id));
    $url = get_permalink($post_id);
    $description = cia_seo_meta_description();
    echo '<meta property="og:type" content="article">' . "\n";
    echo '<meta property="og:locale" content="' . esc_attr($og_locale) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    if ($description) { echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n"; }
    echo '<meta property="og:image" content="' . esc_url($image_url) . '">' . "\n";
    if ($width > 0) { echo '<meta property="og:image:width" content="' . esc_attr((string) $width) . '">' . "\n"; }
    if ($height > 0) { echo '<meta property="og:image:height" content="' . esc_attr((string) $height) . '">' . "\n"; }
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($image_url) . '">' . "\n";
}
add_action('wp_head', 'cia_social_meta', 5);
function cia_localized_document_titles($parts) {
    $lang = cia_home_language();
    if (is_page('about')) { $parts['title'] = $lang === 'en' ? 'About Cloud in Asia' : 'Tentang Cloud in Asia'; }
    elseif (is_page('speakers')) { $parts['title'] = $lang === 'en' ? 'Speakers' : 'Pembicara'; }
    elseif (is_page('partners')) { $parts['title'] = $lang === 'en' ? 'Collaborators' : 'Kolaborator'; }
    elseif (is_page()) {
        $english_title = trim((string) get_post_meta(get_queried_object_id(), '_cia_title_en', true));
        if ($lang === 'en' && $english_title !== '') { $parts['title'] = $english_title; }
    }
    return $parts;
}
add_filter('document_title_parts', 'cia_localized_document_titles', 20);
function cia_robots_policy($robots) {
    if (!(bool) get_option('blog_public')) { return $robots; }
    $internal_pages = array('sample-page','cloudin-asia-components','speakers-page-component','education-partner-page-component');
    $is_internal_page = is_page() && in_array(get_post_field('post_name', get_queried_object_id()), $internal_pages, true);
    if (is_tag() || is_author() || is_date() || is_attachment() || is_search() || $is_internal_page) {
        unset($robots['index'], $robots['nofollow']);
        $robots['noindex'] = true;
        $robots['follow'] = true;
    }
    return $robots;
}
add_filter('wp_robots', 'cia_robots_policy', 20);
function cia_redirect_legacy_policy_pages() {
    $path = trim((string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
    $targets = array('privacy-policy-2'=>'privacy-policy', 'disclaimer'=>'disclosure');
    if (!isset($targets[$path])) { return; }
    $primary = get_page_by_path($targets[$path], OBJECT, 'page');
    if ($primary instanceof WP_Post && $primary->post_status === 'publish') {
        wp_safe_redirect(cia_localized_url(get_permalink($primary), cia_home_language()), 301, 'Cloud in Asia policy consolidation');
        exit;
    }
}
add_action('template_redirect', 'cia_redirect_legacy_policy_pages', 5);
function cia_remove_core_canonical() { remove_action('wp_head', 'rel_canonical'); }
add_action('after_setup_theme', 'cia_remove_core_canonical', 20);
function cia_register_event_type_taxonomy() {
    register_taxonomy('event_type', array('events'), array(
        'labels' => array(
            'name' => __('Event Types', 'cia-recovery'),
            'singular_name' => __('Event Type', 'cia-recovery'),
        ),
        'public' => true,
        'show_in_rest' => true,
        'hierarchical' => false,
        'rewrite' => array('slug' => 'event-format', 'with_front' => false),
        'show_admin_column' => true,
    ));
}
add_action('init', 'cia_register_event_type_taxonomy');
function cia_event_type_link($slug) {
    $term = get_term_by('slug', $slug, 'event_type');
    if ($term && !is_wp_error($term)) {
        $link = get_term_link($term);
        if (!is_wp_error($link)) { return $link; }
    }
    return cia_archive_link('events', '/events/') . '#event-' . sanitize_html_class($slug);
}
function cia_event_timezone() {
    return new DateTimeZone('Asia/Jakarta');
}
function cia_event_datetime($post_id, $meta_key) {
    $raw = trim((string) get_post_meta($post_id, $meta_key, true));
    if ($raw === '') { return null; }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $raw, cia_event_timezone());
    $errors = DateTimeImmutable::getLastErrors();
    if (!$date || (is_array($errors) && ($errors['warning_count'] || $errors['error_count'])) || $date->format('Y-m-d H:i:s') !== $raw) { return null; }
    return $date;
}
function cia_event_safe_url($value) {
    $value = trim((string) $value);
    if ($value === '' || !wp_http_validate_url($value)) { return ''; }
    $scheme = strtolower((string) wp_parse_url($value, PHP_URL_SCHEME));
    return in_array($scheme, array('http','https'), true) ? $value : '';
}
function cia_event_registration_state($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $now = new DateTimeImmutable('now', cia_event_timezone());
    $opens = cia_event_datetime($post_id, 'registration_open');
    $closes = cia_event_datetime($post_id, 'registration_closed');
    $url = cia_event_safe_url(get_post_meta($post_id, 'registration_link', true));
    if ($closes && $now > $closes) { return 'closed'; }
    if ($opens && $now < $opens) { return 'scheduled'; }
    return $url ? 'open' : 'unavailable';
}
function cia_event_format_range($post_id = null, $language = 'id') {
    $post_id = $post_id ?: get_the_ID();
    $start = cia_event_datetime($post_id, 'event_start');
    $end = cia_event_datetime($post_id, 'event_end');
    if (!$start) { return ''; }
    if ($language === 'en') {
        $from = $start->format('D, j M Y · H:i');
        return $end ? $from . '–' . $end->format($start->format('Y-m-d') === $end->format('Y-m-d') ? 'H:i T' : 'D, j M Y · H:i T') : $from . ' WIB';
    }
    $months = array(1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des');
    $from = $start->format('j') . ' ' . $months[(int) $start->format('n')] . ' ' . $start->format('Y') . ' · ' . $start->format('H:i');
    return $end ? $from . '–' . ($start->format('Y-m-d') === $end->format('Y-m-d') ? $end->format('H:i') . ' WIB' : $end->format('j') . ' ' . $months[(int) $end->format('n')] . ' ' . $end->format('Y') . ' · ' . $end->format('H:i') . ' WIB') : $from . ' WIB';
}
function cia_event_display_date($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $date = cia_event_datetime($post_id, 'event_start');
    return $date ? wp_date(get_option('date_format'), $date->getTimestamp(), cia_event_timezone()) : '';
}
function cia_attachment_file_available($attachment_id) {
    $relative = get_post_meta($attachment_id, '_wp_attached_file', true);
    if (!$relative || strpos($relative, '..') !== false || str_starts_with($relative, '/')) { return false; }
    $uploads = wp_get_upload_dir();
    return isset($uploads['basedir']) && file_exists(trailingslashit($uploads['basedir']) . $relative);
}
function cia_card_media($post_id = null, $size = 'cia-card') {
    $post_id = $post_id ?: get_the_ID();
    $thumbnail_id = get_post_thumbnail_id($post_id);
    if ($thumbnail_id && cia_attachment_file_available($thumbnail_id)) {
        echo '<div class="card-media">' . get_the_post_thumbnail($post_id, $size, array('loading'=>'lazy')) . '</div>';
        return;
    }
    echo '<div class="cia-media-placeholder" aria-hidden="true"><span>Cloud in Asia</span></div>';
}
function cia_event_gallery_ids($post_id) {
    $featured_id = (int) get_post_thumbnail_id($post_id);
    $media_ids = array();
    if ($featured_id && cia_attachment_file_available($featured_id)) { $media_ids[] = $featured_id; }
    $alternatives = get_post_meta($post_id, '_cia_media_gallery_ids', true);
    if (!is_array($alternatives)) { $alternatives = array(); }
    foreach ($alternatives as $attachment_id) {
        $attachment_id = absint($attachment_id);
        if (!$attachment_id || $attachment_id === $featured_id) { continue; }
        $mime_type = (string) get_post_mime_type($attachment_id);
        $approval = (string) get_post_meta($attachment_id, '_cia_media_approval_status', true);
        if (!in_array($approval, array('approved_for_staging','approved_for_publication'), true)) { continue; }
        if (!str_starts_with($mime_type, 'image/') || !cia_attachment_file_available($attachment_id)) { continue; }
        $media_ids[] = $attachment_id;
    }
    return array_values(array_unique($media_ids));
}
function cia_event_media_gallery($post_id = null, $size = 'full') {
    $post_id = $post_id ?: get_the_ID();
    $media_ids = cia_event_gallery_ids($post_id);
    if (count($media_ids) < 2) {
        cia_card_media($post_id, $size);
        return;
    }
    $label = cia_home_language() === 'en' ? 'Event visual gallery' : 'Galeri visual event';
    echo '<section class="cia-event-gallery" data-cia-event-gallery aria-label="' . esc_attr($label) . '" aria-roledescription="carousel">';
    echo '<div class="cia-event-gallery-viewport" data-cia-gallery-viewport tabindex="0"><ol class="cia-event-gallery-list">';
    foreach ($media_ids as $index => $attachment_id) {
        $caption = wp_get_attachment_caption($attachment_id);
        echo '<li class="cia-event-gallery-slide" data-cia-gallery-slide><figure>';
        echo wp_get_attachment_image($attachment_id, $size, false, array('loading' => $index === 0 ? 'eager' : 'lazy'));
        if ($caption) { echo '<figcaption>' . esc_html($caption) . '</figcaption>'; }
        echo '</figure></li>';
    }
    echo '</ol></div>';
    echo '<div class="cia-event-gallery-controls">';
    echo '<button type="button" class="cia-gallery-prev" data-cia-gallery-prev aria-label="Previous image" hidden>←</button>';
    echo '<span class="cia-event-gallery-status" data-cia-gallery-status aria-live="polite">1 / ' . esc_html((string) count($media_ids)) . '</span>';
    echo '<button type="button" class="cia-gallery-next" data-cia-gallery-next aria-label="Next image" hidden>→</button>';
    echo '</div></section>';
}
function cia_term_media($term_id) {
    $attachment_id = (int) get_term_meta($term_id, 'z_taxonomy_image_id', true);
    if ($attachment_id && cia_attachment_file_available($attachment_id)) {
        echo '<div class="card-media">' . wp_get_attachment_image($attachment_id, 'cia-speaker', false, array('loading'=>'lazy')) . '</div>';
        return;
    }
    echo '<div class="cia-media-placeholder" aria-hidden="true"><span>Speaker</span></div>';
}
function cia_excerpt_length() { return 24; }
add_filter('excerpt_length', 'cia_excerpt_length', 99);
function cia_archive_link($post_type, $fallback) {
    $link = get_post_type_archive_link($post_type);
    return $link ?: home_url($fallback);
}
function cia_filter_missing_content_images($content) {
    $uploads = wp_get_upload_dir();
    if (empty($uploads['basedir']) || stripos($content, '/wp-content/uploads/') === false) { return $content; }
    return preg_replace_callback(
        '~<img\\b[^>]*\\bsrc=["\\\'][^"\\\']*/wp-content/uploads/([^"\\\'?#]+)[^>]*>~i',
        function ($match) use ($uploads) {
            $relative = rawurldecode(ltrim($match[1], '/'));
            if (str_contains($relative, '..') || str_starts_with($relative, '/')) { return '<div class="cia-content-image-missing" role="note">Image under recovery</div>'; }
            if (file_exists(trailingslashit($uploads['basedir']) . $relative)) { return $match[0]; }
            return '<div class="cia-content-image-missing" role="note">Image under recovery</div>';
        },
        $content
    );
}
add_filter('the_content', 'cia_filter_missing_content_images', 8);
function cia_internal_url_post_id($href) {
    $post_id = url_to_postid($href);
    if ($post_id) { return $post_id; }
    $path = trim((string) wp_parse_url($href, PHP_URL_PATH), '/');
    if (preg_match('~^events/([^/]+)~', $path, $match)) {
        $post = get_page_by_path($match[1], OBJECT, 'events');
        return $post instanceof WP_Post ? (int) $post->ID : 0;
    }
    return 0;
}
function cia_unwrap_blocked_attachment_links($content) {
    if (stripos($content, '<a') === false) { return $content; }
    return preg_replace_callback(
        '~<a\\b[^>]*\\bhref=["\\\']([^"\\\']+)["\\\'][^>]*>(.*?)</a>~is',
        function ($match) {
            $href = html_entity_decode($match[1], ENT_QUOTES, 'UTF-8');
            $host = strtolower((string) wp_parse_url($href, PHP_URL_HOST));
            if (in_array($host, array('cloudinasia.com','www.cloudinasia.com','cloudin.asia','www.cloudin.asia'), true)) {
                $path = (string) wp_parse_url($href, PHP_URL_PATH);
                $href = home_url($path ?: '/');
            }
            $attachment_id = cia_internal_url_post_id($href);
            if (!$attachment_id) { return $match[0]; }
            if (get_post_status($attachment_id) !== 'publish') { return $match[2]; }
            if (get_post_type($attachment_id) !== 'attachment') { return $match[0]; }
            return $match[2];
        },
        $content
    );
}
add_filter('the_content', 'cia_unwrap_blocked_attachment_links', 99);
function cia_normalize_content_headings($content) {
    if (stripos($content, '<h1') === false) { return $content; }
    $content = preg_replace('~<h1(\\s[^>]*)?>~i', '<h2$1>', $content);
    return preg_replace('~</h1>~i', '</h2>', $content);
}
add_filter('the_content', 'cia_normalize_content_headings', 100);

function cia_archive_contributor_name($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $author_id = (int) get_post_field('post_author', $post_id);
    $login = $author_id ? (string) get_the_author_meta('user_login', $author_id) : '';
    $display = $author_id ? trim((string) get_the_author_meta('display_name', $author_id)) : '';
    if ($display === '' || $login === 'atlas-recovery-admin' || $display === 'atlas-recovery-admin') {
        return 'Redaksi Cloud in Asia';
    }
    return $display;
}

function cia_archive_mentions_data() {
    static $data = null;
    if ($data !== null) { return $data; }
    $path = get_template_directory() . '/assets/data/article-mentions.json';
    if (!is_readable($path)) { return $data = array(); }
    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded) || ($decoded['source'] ?? '') !== 'audited_published_corpus' || !isset($decoded['posts']) || !is_array($decoded['posts'])) {
        return $data = array();
    }
    return $data = $decoded['posts'];
}

function cia_archive_mentions($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $names = cia_archive_mentions_data()[(string) $post_id] ?? array();
    if (!is_array($names)) { return array(); }
    $clean = array();
    foreach (array_slice($names, 0, 12) as $name) {
        $name = trim(wp_strip_all_tags((string) $name));
        if ($name !== '') { $clean[] = $name; }
    }
    return array_values(array_unique($clean));
}

function cia_archive_institutions_data() {
    static $data = null;
    if ($data !== null) { return $data; }
    $path = get_template_directory() . '/assets/data/article-institutions.json';
    if (!is_readable($path)) { return $data = array(); }
    $decoded = json_decode((string) file_get_contents($path), true);
    if (!is_array($decoded)
        || ($decoded['schema'] ?? '') !== 'cia.article_institutions.v1'
        || ($decoded['source'] ?? '') !== 'audited_published_corpus'
        || ($decoded['relationship_scope'] ?? '') !== 'mention_only_not_partnership'
        || !isset($decoded['posts'])
        || !is_array($decoded['posts'])) {
        return $data = array();
    }
    return $data = $decoded['posts'];
}

function cia_archive_institutions($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $names = cia_archive_institutions_data()[(string) $post_id] ?? array();
    if (!is_array($names)) { return array(); }
    $clean = array();
    foreach ($names as $name) {
        $name = trim(wp_strip_all_tags((string) $name));
        if ($name !== '') { $clean[] = $name; }
    }
    return array_values(array_unique($clean));
}

function cia_home_discussed_entities($limit = 5, $recent_posts = 10) {
    $institution_posts = cia_archive_institutions_data();
    $people_posts = cia_archive_mentions_data();
    $ids = array_values(array_unique(array_merge(array_keys($institution_posts), array_keys($people_posts))));
    if (!$ids) return array('institutions'=>array(), 'people'=>array(), 'source_posts'=>0);

    $posts = get_posts(array(
        'post_type'=>'post', 'post_status'=>'publish', 'post__in'=>array_map('intval', $ids),
        'posts_per_page'=>(int) $recent_posts, 'orderby'=>'date', 'order'=>'DESC',
        'ignore_sticky_posts'=>true, 'no_found_rows'=>true,
    ));
    $institutions = array();
    $people = array();
    $total = count($posts);
    foreach ($posts as $rank=>$post) {
        $post_id = (string) $post->ID;
        $weight = max(1, $total - $rank);
        foreach (($institution_posts[$post_id] ?? array()) as $name) {
            if (!isset($institutions[$name])) $institutions[$name] = array('name'=>$name, 'articles'=>0, 'score'=>0);
            $institutions[$name]['articles']++;
            $institutions[$name]['score'] += $weight;
        }
        foreach (cia_article_people($post->ID) as $person) {
            $name = $person['name'];
            if (!isset($people[$name])) $people[$name] = array_merge($person, array('articles'=>0, 'score'=>0));
            $people[$name]['articles']++;
            $people[$name]['score'] += $weight;
        }
    }
    $sort = static function ($a, $b) {
        if ($a['score'] === $b['score']) return strcasecmp($a['name'], $b['name']);
        return $b['score'] <=> $a['score'];
    };
    usort($institutions, $sort);
    usort($people, $sort);
    foreach ($institutions as &$item) {
        $item['mark'] = cia_institution_mark($item['name']);
        $item['url'] = get_search_link($item['name']);
    }
    unset($item);
    foreach ($people as &$item) $item['url'] = get_search_link($item['name']);
    unset($item);
    return array(
        'institutions'=>array_slice($institutions, 0, (int) $limit),
        'people'=>array_slice($people, 0, (int) $limit),
        'source_posts'=>$total,
    );
}

function cia_article_people($post_id = 0) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $names = cia_archive_mentions($post_id);
    if (!$names) { return array(); }
    $directory = cia_people_directory_data();
    $records = isset($directory['records']) && is_array($directory['records']) ? $directory['records'] : array();
    $by_name = array();
    foreach ($records as $record) {
        $name = trim((string) ($record['name'] ?? ''));
        if ($name !== '') { $by_name[strtolower($name)] = $record; }
    }
    $people = array();
    foreach ($names as $name) {
        $record = $by_name[strtolower($name)] ?? array();
        $portrait = '';
        $candidate = isset($record['portrait']['theme_path']) ? (string) $record['portrait']['theme_path'] : '';
        if (preg_match('~^assets/people/[a-zA-Z0-9._-]+\\.png$~', $candidate)
            && is_file(get_template_directory() . '/' . $candidate)) {
            $portrait = $candidate;
        }
        $people[] = array(
            'name' => $name,
            'portrait' => $portrait,
            'affiliation' => trim((string) ($record['affiliation_note'] ?? '')),
        );
    }
    return $people;
}

function cia_entity_initials($name) {
    $words = preg_split('/[^\\p{L}\\p{N}]+/u', trim((string) $name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$words) { return '•'; }
    $initials = '';
    foreach (array_slice($words, 0, 2) as $word) {
        $initials .= function_exists('mb_substr') ? mb_substr($word, 0, 1, 'UTF-8') : substr($word, 0, 1);
    }
    return function_exists('mb_strtoupper') ? mb_strtoupper($initials, 'UTF-8') : strtoupper($initials);
}

function cia_institution_mark($name) {
    $logos = array(
        'PT Sivali Catur Lestari (Sivali Cloud Technology)' => 'assets/collaborators/sivali.png',
        'Sivali Cloud Technology' => 'assets/collaborators/sivali.png',
        'Universitas Jenderal Achmad Yani Yogyakarta' => 'assets/collaborators/unjaya.png',
        'INTI' => 'assets/collaborators/inti.png',
    );
    $relative = $logos[(string) $name] ?? '';
    if ($relative !== '' && is_file(get_template_directory() . '/' . $relative)) {
        return array('type'=>'image', 'path'=>$relative, 'alt'=>(string) $name, 'variant'=>strpos($relative, 'sivali.png') !== false ? 'dark' : 'light');
    }
    return array('type'=>'initials', 'text'=>cia_entity_initials($name), 'alt'=>'');
}

function cia_archive_internal_reference_counts($language = 'id') {
    $key = 'cia_archive_refs_' . sanitize_key($language) . '_v1';
    $cached = get_transient($key);
    if (is_array($cached)) { return $cached; }
    $query = new WP_Query(cia_editorial_query_args($language, array(
        'posts_per_page' => 150,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    )));
    $counts = array();
    foreach ($query->posts as $post) {
        if (!preg_match_all('~href=["\']([^"\']+)["\']~i', (string) $post->post_content, $matches)) { continue; }
        foreach ($matches[1] as $href) {
            $target = cia_internal_url_post_id(html_entity_decode($href, ENT_QUOTES, 'UTF-8'));
            if ($target > 0 && get_post_type($target) === 'post' && get_post_status($target) === 'publish') {
                $counts[$target] = ($counts[$target] ?? 0) + 1;
            }
        }
    }
    arsort($counts, SORT_NUMERIC);
    set_transient($key, $counts, 6 * HOUR_IN_SECONDS);
    return $counts;
}

function cia_archive_popular_posts($language = 'id', $limit = 5, $category_id = 0) {
    $counts = cia_archive_internal_reference_counts($language);
    $category_id = max(0, (int) $category_id);
    if (!$counts) { return array(); }
    if ($category_id > 0) {
        $eligible_ids = get_posts(array(
            'post_type'=>'post', 'post_status'=>'publish',
            'post__in'=>array_map('intval', array_keys($counts)),
            'category__in'=>array($category_id),
            'posts_per_page'=>-1, 'fields'=>'ids', 'no_found_rows'=>true,
        ));
        $eligible = array_fill_keys(array_map('intval', $eligible_ids), true);
        $counts = array_filter($counts, static fn($count, $post_id) => isset($eligible[(int) $post_id]), ARRAY_FILTER_USE_BOTH);
    }
    $ranked = array_slice($counts, 0, max(1, (int) $limit), true);
    if (!$ranked) { return array(); }
    $posts = get_posts(array('post_type'=>'post','post_status'=>'publish','post__in'=>array_map('intval', array_keys($ranked)),'posts_per_page'=>count($ranked),'orderby'=>'post__in'));
    $by_id = array();
    foreach ($posts as $post) { $by_id[(int) $post->ID] = $post; }
    $result = array();
    foreach ($ranked as $post_id => $count) {
        if (isset($by_id[(int) $post_id])) { $result[] = array('post'=>$by_id[(int) $post_id], 'internal_reference_count'=>(int) $count); }
    }
    return $result;
}

function cia_archive_icon($name) {
    $paths = array(
        'arrow' => '<path d="M5 12h14M14 6l6 6-6 6"/>',
        'person' => '<circle cx="12" cy="8" r="3"/><path d="M5 20c.7-4 3-6 7-6s6.3 2 7 6"/>',
        'filter' => '<path d="M4 6h16M7 12h10M10 18h4"/>',
    );
    if (!isset($paths[$name])) { return ''; }
    return '<svg class="archive-icon" aria-hidden="true" focusable="false" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">' . $paths[$name] . '</svg>';
}

function cia_search_supported_scopes() {
    return array('all','articles','events','speakers','collaborators');
}

function cia_search_archive_results($raw_query, $language = 'id', $scope = 'all', $paged = 1, $per_page = 12) {
    $query = trim(sanitize_text_field(wp_strip_all_tags((string) $raw_query)));
    $language = in_array($language, array('id','en'), true) ? $language : 'id';
    $scope = in_array($scope, cia_search_supported_scopes(), true) ? $scope : 'all';
    $paged = max(1, absint($paged));
    $per_page = max(1, min(24, absint($per_page)));
    $limit = $scope === 'all' ? 6 : $per_page;
    $groups = array(
        'articles'=>array('items'=>array(),'total'=>0,'pages'=>0),
        'events'=>array('items'=>array(),'total'=>0,'pages'=>0),
        'speakers'=>array('items'=>array(),'total'=>0,'pages'=>0),
        'collaborators'=>array('items'=>array(),'total'=>0,'pages'=>0),
    );
    if ($query === '') { return array('query'=>'','scope'=>$scope,'page'=>$paged,'groups'=>$groups,'total'=>0,'pages'=>0); }

    if ($scope === 'all' || $scope === 'articles') {
        $articles = new WP_Query(cia_editorial_query_args($language, array(
            's'=>$query,'post_status'=>'publish','posts_per_page'=>$limit,
            'paged'=>$scope === 'articles' ? $paged : 1,'ignore_sticky_posts'=>true,
        )));
        $groups['articles'] = array('items'=>$articles->posts,'total'=>(int) $articles->found_posts,'pages'=>(int) $articles->max_num_pages);
    }

    if ($scope === 'all' || $scope === 'events') {
        $excluded = function_exists('cia_event_session_source_ids') ? array_map('absint', cia_event_session_source_ids()) : array();
        $events = new WP_Query(array(
            'post_type'=>'events','post_status'=>'publish','s'=>$query,
            'posts_per_page'=>$limit,'paged'=>$scope === 'events' ? $paged : 1,
            'post__not_in'=>$excluded,'orderby'=>'date','order'=>'DESC',
        ));
        $groups['events'] = array('items'=>$events->posts,'total'=>(int) $events->found_posts,'pages'=>(int) $events->max_num_pages);
    }

    if ($scope === 'all' || $scope === 'speakers') {
        $speaker_terms = get_terms(array('taxonomy'=>'speakers','hide_empty'=>true,'search'=>$query,'orderby'=>'name','order'=>'ASC'));
        $speaker_terms = is_wp_error($speaker_terms) ? array() : array_values($speaker_terms);
        $speaker_total = count($speaker_terms);
        $speaker_offset = $scope === 'speakers' ? ($paged - 1) * $per_page : 0;
        $groups['speakers'] = array(
            'items'=>array_slice($speaker_terms, $speaker_offset, $limit),
            'total'=>$speaker_total,
            'pages'=>$speaker_total ? (int) ceil($speaker_total / $per_page) : 0,
        );
    }

    if ($scope === 'all' || $scope === 'collaborators') {
        $roles = $language === 'en'
            ? array('sponsors'=>'Sponsor','community_support'=>'Community supporter','education-partner'=>'Education partner')
            : array('sponsors'=>'Sponsor','community_support'=>'Pendukung komunitas','education-partner'=>'Mitra pendidikan');
        $collaborators = array();
        foreach ($roles as $taxonomy=>$role) {
            $terms = get_terms(array('taxonomy'=>$taxonomy,'hide_empty'=>true,'search'=>$query,'orderby'=>'name','order'=>'ASC'));
            if (is_wp_error($terms)) { continue; }
            foreach ($terms as $term) {
                $display_name = $term->slug === 'kementrian-komunikasi-dan-digital' ? 'Kementerian Komunikasi dan Digital' : $term->name;
                $collaborators[] = array(
                    'term'=>$term,'taxonomy'=>$taxonomy,'role'=>$role,'name'=>$display_name,
                    'url'=>cia_localized_url(home_url('/partners/'), $language) . '#partner-' . sanitize_html_class($taxonomy . '-' . $term->slug),
                );
            }
        }
        usort($collaborators, static fn($a, $b) => strcasecmp($a['name'], $b['name']));
        $collaborator_total = count($collaborators);
        $collaborator_offset = $scope === 'collaborators' ? ($paged - 1) * $per_page : 0;
        $groups['collaborators'] = array(
            'items'=>array_slice($collaborators, $collaborator_offset, $limit),
            'total'=>$collaborator_total,
            'pages'=>$collaborator_total ? (int) ceil($collaborator_total / $per_page) : 0,
        );
    }

    $total = array_sum(array_map(static fn($group) => (int) $group['total'], $groups));
    $pages = $scope === 'all' ? 1 : (int) ($groups[$scope]['pages'] ?? 0);
    return array('query'=>$query,'scope'=>$scope,'page'=>$paged,'groups'=>$groups,'total'=>$total,'pages'=>$pages);
}

function cia_search_pagination($query, $scope, $language, $current, $total_pages) {
    $current = max(1, absint($current));
    $total_pages = max(0, absint($total_pages));
    if ($total_pages <= 1) { return; }
    $labels = $language === 'en' ? array('label'=>'Search result pages','previous'=>'Previous','next'=>'Next') : array('label'=>'Halaman hasil pencarian','previous'=>'Sebelumnya','next'=>'Berikutnya');
    $url = static function($page) use ($query, $scope, $language) {
        $args = array('s'=>$query,'scope'=>$scope);
        if ($language === 'en') { $args['lang'] = 'en'; }
        if ($page > 1) { $args['paged'] = $page; }
        return add_query_arg($args, home_url('/'));
    };
    echo '<nav class="search-pagination" aria-label="' . esc_attr($labels['label']) . '">';
    if ($current > 1) { echo '<a class="search-pagination-direction" href="' . esc_url($url($current - 1)) . '">← ' . esc_html($labels['previous']) . '</a>'; }
    echo '<div>';
    for ($page = 1; $page <= $total_pages; $page++) {
        if ($total_pages > 9 && $page > 2 && $page < $current - 1) { if ($page === 3) { echo '<span aria-hidden="true">…</span>'; } continue; }
        if ($total_pages > 9 && $page > $current + 1 && $page < $total_pages - 1) { if ($page === $current + 2) { echo '<span aria-hidden="true">…</span>'; } continue; }
        echo '<a href="' . esc_url($url($page)) . '"' . ($page === $current ? ' aria-current="page"' : '') . '>' . esc_html((string) $page) . '</a>';
    }
    echo '</div>';
    if ($current < $total_pages) { echo '<a class="search-pagination-direction" href="' . esc_url($url($current + 1)) . '">' . esc_html($labels['next']) . ' →</a>'; }
    echo '</nav>';
}
