<?php
if (!defined('ABSPATH')) { exit; }
get_header();
$lang = cia_home_language();
$copy = $lang === 'en' ? array(
    'kicker'=>'Events','title'=>'Where ideas become shared practice.','intro'=>'Explore Cloud in Asia gatherings across webinars, meetups, and conferences. Past events are presented as a living timeline of the people and organizations that shaped each conversation.','upcoming'=>'Upcoming','upcoming_empty'=>'No upcoming event has been published yet.','past'=>'Past events','past_intro'=>'Browse the complete event record, newest first.','format'=>'Format','all_formats'=>'All formats','collaborator'=>'Collaborator','all_collaborators'=>'All collaborators','search'=>'Search events','search_placeholder'=>'Topic, title, or collaborator','apply'=>'Apply filters','clear'=>'Clear','results'=>'events shown','no_results'=>'No events match these filters.','read'=>'View event','with'=>'With','all_years'=>'Complete timeline','map_kicker'=>'Event footprint','map_title'=>'Where the conversations happened.','map_intro'=>'An interactive view of physical venues recorded in published event data. Virtual events remain in the timeline but are not assigned a geographic point.','map_all'=>'All locations','map_past'=>'Past','map_upcoming'=>'Upcoming','map_focus'=>'Focus locations','map_overview'=>'Indonesia overview','map_events'=>'in-person events','map_locations'=>'locations','map_city'=>'City-level location','map_unmapped'=>'event(s) have no verified map location and remain in the timeline','map_virtual'=>'virtual event(s) remain available in the timeline.'
) : array(
    'kicker'=>'Event','title'=>'Tempat gagasan menjadi praktik bersama.','intro'=>'Jelajahi kegiatan Cloud in Asia dalam format webinar, meetup, dan konferensi. Event terdahulu disusun sebagai timeline hidup tentang orang dan organisasi yang membentuk setiap percakapan.','upcoming'=>'Akan datang','upcoming_empty'=>'Belum ada event mendatang yang dipublikasikan.','past'=>'Event terdahulu','past_intro'=>'Telusuri seluruh rekam event, mulai dari yang terbaru.','format'=>'Format','all_formats'=>'Semua format','collaborator'=>'Kolaborator','all_collaborators'=>'Semua kolaborator','search'=>'Cari event','search_placeholder'=>'Topik, judul, atau kolaborator','apply'=>'Terapkan filter','clear'=>'Hapus','results'=>'event ditampilkan','no_results'=>'Tidak ada event yang cocok dengan filter ini.','read'=>'Lihat event','with'=>'Bersama','all_years'=>'Timeline lengkap','map_kicker'=>'Jejak event','map_title'=>'Di mana percakapan berlangsung.','map_intro'=>'Peta interaktif venue fisik yang tercatat dalam data event terpublikasi. Event virtual tetap tersedia di timeline tetapi tidak diberi titik geografis.','map_all'=>'Semua lokasi','map_past'=>'Terdahulu','map_upcoming'=>'Akan datang','map_focus'=>'Fokus lokasi','map_overview'=>'Ikhtisar Indonesia','map_events'=>'event tatap muka','map_locations'=>'lokasi','map_city'=>'Lokasi tingkat kota','map_unmapped'=>'event belum memiliki lokasi peta terverifikasi dan tetap tersedia di timeline','map_virtual'=>'event virtual tetap tersedia dalam timeline.'
);
$taxonomies = array('sponsors','community_support','education-partner');
$collaborator_name_overrides = array('kementrian-komunikasi-dan-digital'=>'Kementerian Komunikasi dan Digital');
$collaborator_options = array();
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array('taxonomy'=>$taxonomy,'hide_empty'=>true));
    if (is_wp_error($terms)) { continue; }
    foreach ($terms as $term) {
        $key = $taxonomy . ':' . $term->slug;
        $collaborator_options[$key] = $collaborator_name_overrides[$term->slug] ?? $term->name;
    }
}
natcasesort($collaborator_options);
$format_options = array('webinar'=>'Webinar','meetup'=>'Meetup','conference'=>'Conference','regional-event'=>'Regional event');
$requested_format = isset($_GET['event_format']) ? sanitize_key(wp_unslash($_GET['event_format'])) : '';
$requested_format = isset($format_options[$requested_format]) ? $requested_format : '';
$requested_collaborator = isset($_GET['event_collaborator']) ? sanitize_text_field(wp_unslash($_GET['event_collaborator'])) : '';
$requested_collaborator = isset($collaborator_options[$requested_collaborator]) ? $requested_collaborator : '';
$requested_search = isset($_GET['event_search']) ? sanitize_text_field(wp_unslash($_GET['event_search'])) : '';
$now = current_time('mysql');
$session_source_ids = function_exists('cia_event_session_source_ids') ? cia_event_session_source_ids() : array();
$upcoming = new WP_Query(array('post_type'=>'events','post_status'=>'publish','posts_per_page'=>6,'post__not_in'=>$session_source_ids,'meta_key'=>'event_start','meta_value'=>$now,'meta_compare'=>'>=','meta_type'=>'DATETIME','orderby'=>'meta_value','order'=>'ASC'));
$past = new WP_Query(array('post_type'=>'events','post_status'=>'publish','posts_per_page'=>-1,'post__not_in'=>$session_source_ids,'meta_key'=>'event_start','meta_value'=>$now,'meta_compare'=>'<','meta_type'=>'DATETIME','orderby'=>'meta_value','order'=>'DESC'));
$make_record = static function($post) use ($taxonomies, $collaborator_name_overrides, $requested_format, $requested_collaborator, $requested_search) {
    $start_raw = (string) get_post_meta($post->ID, 'event_start', true);
    $timestamp = strtotime($start_raw) ?: get_post_time('U', true, $post);
    $venue = trim((string) get_post_meta($post->ID, 'venue', true));
    $location = cia_event_location_for_venue($venue);
    $type_terms = get_the_terms($post, 'event_type');
    $type_terms = is_array($type_terms) ? $type_terms : array();
    $type_slugs = wp_list_pluck($type_terms, 'slug');
    $collaborators = array();
    $collaborator_values = array();
    foreach ($taxonomies as $taxonomy) {
        $terms = get_the_terms($post, $taxonomy);
        if (!is_array($terms)) { continue; }
        foreach ($terms as $term) {
            $collaborators[$taxonomy . ':' . $term->slug] = $collaborator_name_overrides[$term->slug] ?? $term->name;
            $collaborator_values[] = $taxonomy . ':' . $term->slug;
        }
    }
    $search_haystack = strtolower(wp_strip_all_tags($post->post_title . ' ' . $venue . ' ' . implode(' ', $collaborators)));
    $visible = (!$requested_format || in_array($requested_format, $type_slugs, true))
        && (!$requested_collaborator || in_array($requested_collaborator, $collaborator_values, true))
        && (!$requested_search || strpos($search_haystack, strtolower($requested_search)) !== false);
    return array('post'=>$post,'timestamp'=>$timestamp,'venue'=>$venue,'location'=>$location,'types'=>$type_terms,'type_slugs'=>$type_slugs,'collaborators'=>$collaborators,'collaborator_values'=>$collaborator_values,'search'=>$search_haystack,'visible'=>$visible);
};
$upcoming_records = array_map($make_record, $upcoming->posts);
$past_by_year = array();
$past_records = array();
foreach ($past->posts as $event_post) {
    $record = $make_record($event_post);
    $past_records[] = $record;
    $year = wp_date('Y', $record['timestamp']);
    if (!isset($past_by_year[$year])) { $past_by_year[$year] = array(); }
    $past_by_year[$year][] = $record;
}
$map_locations = array(); $map_virtual_count = 0; $map_unmapped = 0; $map_status_counts = array('past'=>0,'upcoming'=>0);
$virtual_venues = array('zoom','zoom meeting','youtube');
foreach (array('upcoming'=>$upcoming_records,'past'=>$past_records) as $status=>$records) {
    foreach ($records as $record) {
        $venue_key = strtolower(trim($record['venue']));
        $virtual = in_array($venue_key, $virtual_venues, true) || preg_match('~^https?://~', $venue_key);
        if ($virtual) { $map_virtual_count++; continue; }
        if (!$record['location']) { $map_unmapped++; continue; }
        $location = $record['location']; $location_id = sanitize_key($location['id']);
        if (!isset($map_locations[$location_id])) { $map_locations[$location_id] = array('location'=>$location,'events'=>array(),'statuses'=>array(),'counts'=>array('past'=>0,'upcoming'=>0)); }
        $map_locations[$location_id]['events'][] = array('post'=>$record['post'],'timestamp'=>$record['timestamp'],'status'=>$status);
        $map_locations[$location_id]['statuses'][$status] = $status;
        $map_locations[$location_id]['counts'][$status]++;
        $map_status_counts[$status]++;
    }
}
uasort($map_locations, static function($a,$b){ return strcasecmp(($a['location']['city'] ?? '').($a['location']['label'] ?? ''),($b['location']['city'] ?? '').($b['location']['label'] ?? '')); });
$map_event_count = $map_status_counts['past'] + $map_status_counts['upcoming'];
$initial_count = 0;
foreach ($past_by_year as $records) { foreach ($records as $record) { if ($record['visible']) { $initial_count++; } } }
?>
<main class="editorial-surface events-explore" data-event-timeline data-results="<?php echo esc_attr($initial_count); ?>">
  <header class="events-explore-hero">
    <div class="events-explore-hero-inner">
      <p class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></p>
      <h1><?php echo esc_html($copy['title']); ?></h1>
      <p class="events-explore-deck"><?php echo esc_html($copy['intro']); ?></p>
      <a class="events-jump" href="#past-events"><?php echo esc_html($copy['all_years']); ?> <span aria-hidden="true">↓</span></a>
    </div>
  </header>

  <section class="events-upcoming" aria-labelledby="upcoming-events-title">
    <div class="events-section-heading"><p class="media-kicker section-brow"><?php echo esc_html($copy['upcoming']); ?></p><h2 id="upcoming-events-title"><?php echo esc_html($copy['upcoming']); ?></h2></div>
    <?php if ($upcoming_records) : ?>
      <div class="events-upcoming-list">
        <?php foreach ($upcoming_records as $record) : $event_post = $record['post']; ?>
          <article class="event-upcoming-item">
            <time datetime="<?php echo esc_attr(wp_date('c', $record['timestamp'])); ?>"><strong><?php echo esc_html(wp_date('d', $record['timestamp'])); ?></strong><span><?php echo esc_html(wp_date('M Y', $record['timestamp'])); ?></span></time>
            <div><p class="event-type-line"><?php echo esc_html(implode(' · ', wp_list_pluck($record['types'], 'name'))); ?></p><h3><a href="<?php echo esc_url(cia_localized_url(get_permalink($event_post), $lang)); ?>"><?php echo esc_html(get_the_title($event_post)); ?></a></h3><?php if ($record['venue']) : ?><p><?php echo esc_html($record['venue']); ?></p><?php endif; ?></div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php else : ?><p class="events-empty-note"><?php echo esc_html($copy['upcoming_empty']); ?></p><?php endif; wp_reset_postdata(); ?>
  </section>

  <section class="events-map" data-event-map data-focus-viewbox="115 116 65 36" data-overview-viewbox="0 0 475 180" data-events-label="<?php echo esc_attr($copy['map_events']); ?>" data-locations-label="<?php echo esc_attr($copy['map_locations']); ?>" aria-labelledby="event-map-title">
    <header class="events-map-heading"><p class="media-kicker section-brow"><?php echo esc_html($copy['map_kicker']); ?></p><h2 id="event-map-title"><?php echo esc_html($copy['map_title']); ?></h2><p><?php echo esc_html($copy['map_intro']); ?></p></header>
    <div class="event-map-status" role="group" aria-label="<?php echo esc_attr($copy['map_kicker']); ?>">
      <button type="button" data-event-map-status="all" aria-pressed="true"><?php echo esc_html($copy['map_all']); ?> <strong><?php echo esc_html($map_event_count); ?></strong></button>
      <button type="button" data-event-map-status="past" aria-pressed="false"><?php echo esc_html($copy['map_past']); ?> <strong><?php echo esc_html($map_status_counts['past']); ?></strong></button>
      <button type="button" data-event-map-status="upcoming" aria-pressed="false" <?php disabled($map_status_counts['upcoming'],0); ?>><?php echo esc_html($copy['map_upcoming']); ?> <strong><?php echo esc_html($map_status_counts['upcoming']); ?></strong></button>
    </div>
    <div class="event-map-layout">
      <div class="event-map-canvas">
        <svg class="event-map-svg" data-event-map-svg viewBox="115 116 65 36" role="img" aria-labelledby="event-map-svg-title event-map-svg-desc">
          <title id="event-map-svg-title"><?php echo esc_html($copy['map_title']); ?></title><desc id="event-map-svg-desc"><?php echo esc_html($copy['map_intro']); ?></desc>
          <image href="<?php echo esc_url(get_template_directory_uri().'/assets/maps/indonesia-outline.svg'); ?>" x="0" y="0" width="475" height="180"></image>
          <?php foreach($map_locations as $location_id=>$group): $location=$group['location']; $x=((float)$location['lon']-94)*10; $y=(6.5-(float)$location['lat'])*10; $statuses=implode(' ',array_keys($group['statuses'])); ?>
            <g class="event-map-marker" data-event-map-marker="<?php echo esc_attr($location_id); ?>" data-event-statuses="<?php echo esc_attr($statuses); ?>" role="button" tabindex="0" aria-controls="event-location-<?php echo esc_attr($location_id); ?>" aria-pressed="false" transform="translate(<?php echo esc_attr(number_format($x,3,'.','')); ?> <?php echo esc_attr(number_format($y,3,'.','')); ?>)"><title><?php echo esc_html($location['label'].' · '.count($group['events']).' '.$copy['map_events']); ?></title><circle class="event-map-marker-ring" r="0.72"></circle><circle class="event-map-marker-core" r="0.32"></circle></g>
          <?php endforeach; ?>
        </svg>
        <div class="event-map-view" role="group" aria-label="Map view"><button type="button" data-event-map-view="focus" aria-pressed="true"><?php echo esc_html($copy['map_focus']); ?></button><button type="button" data-event-map-view="overview" aria-pressed="false"><?php echo esc_html($copy['map_overview']); ?></button></div>
        <p class="event-map-summary" aria-live="polite"><strong data-event-map-event-count><?php echo esc_html($map_event_count); ?></strong> <?php echo esc_html($copy['map_events']); ?> · <strong data-event-map-location-count><?php echo esc_html(count($map_locations)); ?></strong> <?php echo esc_html($copy['map_locations']); ?></p>
      </div>
      <div class="event-map-locations" aria-label="<?php echo esc_attr($copy['map_locations']); ?>">
        <?php foreach($map_locations as $location_id=>$group): $location=$group['location']; $statuses=implode(' ',array_keys($group['statuses'])); ?>
          <article class="event-map-location" id="event-location-<?php echo esc_attr($location_id); ?>" data-event-map-location="<?php echo esc_attr($location_id); ?>" data-event-statuses="<?php echo esc_attr($statuses); ?>" data-past-count="<?php echo esc_attr($group['counts']['past']); ?>" data-upcoming-count="<?php echo esc_attr($group['counts']['upcoming']); ?>">
            <header><p><?php echo esc_html($location['city']); ?><?php if(($location['precision'] ?? '')==='city'): ?> · <?php echo esc_html($copy['map_city']); ?><?php endif; ?></p><h3><?php echo esc_html($location['label']); ?></h3><strong data-event-map-card-count><?php echo esc_html(count($group['events'])); ?></strong></header>
            <ul><?php foreach($group['events'] as $mapped_event): ?><li data-event-map-event-status="<?php echo esc_attr($mapped_event['status']); ?>"><time datetime="<?php echo esc_attr(wp_date('c',$mapped_event['timestamp'])); ?>"><?php echo esc_html(wp_date('d M Y',$mapped_event['timestamp'])); ?></time><a href="<?php echo esc_url(cia_localized_url(get_permalink($mapped_event['post']),$lang)); ?>"><?php echo esc_html(get_the_title($mapped_event['post'])); ?></a></li><?php endforeach; ?></ul>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
    <aside class="event-map-unmapped"><?php if($map_virtual_count): ?><p><strong><?php echo esc_html($map_virtual_count); ?></strong> <?php echo esc_html($copy['map_virtual']); ?></p><?php endif; ?><?php if($map_unmapped): ?><p><strong><?php echo esc_html($map_unmapped); ?></strong> <?php echo esc_html($copy['map_unmapped']); ?>.</p><?php endif; ?></aside>
  </section>

  <section id="past-events" class="events-past" aria-labelledby="past-events-title">
    <div class="events-section-heading events-past-heading"><div><p class="media-kicker section-brow"><?php echo esc_html($copy['past']); ?></p><h2 id="past-events-title"><?php echo esc_html($copy['past']); ?></h2></div><p><?php echo esc_html($copy['past_intro']); ?></p></div>
    <form class="events-filter" method="get" action="<?php echo esc_url(get_post_type_archive_link('events')); ?>" data-event-filter>
      <?php if ($lang === 'en') : ?><input type="hidden" name="lang" value="en"><?php endif; ?>
      <fieldset class="events-format-filter"><legend><?php echo esc_html($copy['format']); ?></legend>
        <label><input type="radio" name="event_format" value="" <?php checked($requested_format, ''); ?>><span><?php echo esc_html($copy['all_formats']); ?></span></label>
        <?php foreach ($format_options as $slug=>$label) : ?><label><input type="radio" name="event_format" value="<?php echo esc_attr($slug); ?>" <?php checked($requested_format, $slug); ?>><span><?php echo esc_html($label); ?></span></label><?php endforeach; ?>
      </fieldset>
      <label class="events-filter-field"><span><?php echo esc_html($copy['collaborator']); ?></span><select name="event_collaborator"><option value=""><?php echo esc_html($copy['all_collaborators']); ?></option><?php foreach ($collaborator_options as $value=>$label) : ?><option value="<?php echo esc_attr($value); ?>" <?php selected($requested_collaborator, $value); ?>><?php echo esc_html($label); ?></option><?php endforeach; ?></select></label>
      <label class="events-filter-field events-search-field"><span><?php echo esc_html($copy['search']); ?></span><input type="search" name="event_search" value="<?php echo esc_attr($requested_search); ?>" placeholder="<?php echo esc_attr($copy['search_placeholder']); ?>"></label>
      <div class="events-filter-actions"><button type="submit"><?php echo esc_html($copy['apply']); ?></button><a href="<?php echo esc_url(cia_localized_url(get_post_type_archive_link('events'), $lang)); ?>" data-event-clear><?php echo esc_html($copy['clear']); ?></a></div>
    </form>
    <p class="events-result-count" aria-live="polite"><strong data-event-result-count><?php echo esc_html($initial_count); ?></strong> <?php echo esc_html($copy['results']); ?></p>

    <div class="event-timeline">
      <?php foreach ($past_by_year as $year=>$records) : $year_visible = array_filter($records, static fn($record)=>$record['visible']); ?>
        <section class="event-timeline-year" data-event-year="<?php echo esc_attr($year); ?>" <?php echo $year_visible ? '' : 'hidden'; ?>>
          <h3 class="event-timeline-year-label"><?php echo esc_html($year); ?></h3>
          <div class="event-timeline-year-items">
          <?php foreach ($records as $record) : $event_post = $record['post']; ?>
            <article class="event-timeline-item" data-event-types="<?php echo esc_attr(implode(' ', $record['type_slugs'])); ?>" data-event-collaborators="<?php echo esc_attr(implode(' ', $record['collaborator_values'])); ?>" data-event-search="<?php echo esc_attr($record['search']); ?>" <?php echo $record['visible'] ? '' : 'hidden'; ?>>
              <time class="event-timeline-date" datetime="<?php echo esc_attr(wp_date('c', $record['timestamp'])); ?>"><strong><?php echo esc_html(wp_date('d', $record['timestamp'])); ?></strong><span><?php echo esc_html(wp_date('M', $record['timestamp'])); ?></span></time>
              <div class="event-timeline-content">
                <?php if ($record['types']) : ?><p class="event-type-line"><?php echo esc_html(implode(' · ', wp_list_pluck($record['types'], 'name'))); ?></p><?php endif; ?>
                <h4><a href="<?php echo esc_url(cia_localized_url(get_permalink($event_post), $lang)); ?>"><?php echo esc_html(get_the_title($event_post)); ?></a></h4>
                <?php if ($record['venue']) : ?><p class="event-venue"><?php echo esc_html($record['venue']); ?></p><?php endif; ?>
                <?php if ($record['collaborators']) : ?><p class="event-collaborator-line"><span><?php echo esc_html($copy['with']); ?></span> <?php echo esc_html(implode(' · ', $record['collaborators'])); ?></p><?php endif; ?>
                <a class="event-read-link" href="<?php echo esc_url(cia_localized_url(get_permalink($event_post), $lang)); ?>"><?php echo esc_html($copy['read']); ?> <span aria-hidden="true">→</span></a>
              </div>
            </article>
          <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>
    <p class="events-no-results" data-event-empty <?php echo $initial_count ? 'hidden' : ''; ?>><?php echo esc_html($copy['no_results']); ?></p>
  </section>
</main>
<?php get_footer();
