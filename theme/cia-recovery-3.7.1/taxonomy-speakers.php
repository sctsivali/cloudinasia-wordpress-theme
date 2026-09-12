<?php
$term = get_queried_object();
$person = cia_people_directory_record($term->term_id);
$name = $person ? $person['name'] : $term->name;
global $wp_query, $post;
$event_records = array();
$article_records = array();
if ($person) {
    $event_ids = array_values(array_filter(array_map('absint', (array) ($person['speaker_event_ids'] ?? array()))));
    $article_ids = array_values(array_filter(array_map('absint', (array) ($person['article_mention_record_ids'] ?? array()))));
    if ($event_ids) {
        $event_records = get_posts(array('post_type'=>'events', 'post_status'=>'publish', 'post__in'=>$event_ids, 'posts_per_page'=>count($event_ids), 'meta_key'=>'event_start', 'orderby'=>'meta_value', 'meta_type'=>'DATETIME', 'order'=>'DESC'));
    }
    if ($article_ids) {
        $article_records = get_posts(array('post_type'=>'post', 'post_status'=>'publish', 'post__in'=>$article_ids, 'posts_per_page'=>count($article_ids), 'orderby'=>'date', 'order'=>'DESC'));
    }
} else {
    $event_ids = array();
    foreach ((array) $wp_query->posts as $related_candidate) {
        $related = get_post($related_candidate);
        if (!$related instanceof WP_Post || $related->post_status !== 'publish') { continue; }
        if ($related->post_type === 'post') { $article_records[] = $related; continue; }
        if ($related->post_type === 'event_session') {
            $parent_id = (int) get_post_meta($related->ID, '_cia_parent_event_id', true);
            $related = $parent_id ? get_post($parent_id) : null;
        }
        if ($related instanceof WP_Post && $related->post_type === 'events' && $related->post_status === 'publish' && !isset($event_ids[$related->ID])) {
            $event_ids[$related->ID] = true;
            $event_records[] = $related;
        }
    }
}
get_header();
?>
<div class="editorial-surface speaker-profile">
  <header class="editorial-page-hero"><div class="container speaker-profile-hero">
    <div class="speaker-profile-portrait"><?php if ($person && $person['portrait']): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/' . ltrim($person['portrait']['theme_path'],'/')); ?>" alt="Portrait <?php echo esc_attr($name); ?>" width="400" height="400"><?php else: cia_term_media($term->term_id); endif; ?></div>
    <div><span class="media-kicker section-brow">Profil</span><h1><?php echo esc_html($name); ?></h1><?php if ($person): ?><p class="people-metrics"><span><strong><?php echo esc_html($person['article_mention_records']); ?></strong> publikasi menyebut nama</span><span><strong><?php echo esc_html($person['speaker_appearances']); ?></strong> penampilan pembicara</span></p><?php endif; ?><?php if ($term->description): ?><div class="editorial-deck"><?php echo wp_kses_post(wpautop($term->description)); ?></div><?php endif; ?></div>
  </div></header>

  <section class="editorial-listing speaker-event-section" aria-labelledby="speaker-events-title"><div class="container">
    <div class="editorial-section-heading"><span class="media-kicker section-brow">Agenda dan rekaman</span><h2 id="speaker-events-title">Penampilan di Event</h2><p><?php echo esc_html((string) count($event_records)); ?> event terverifikasi</p></div>
    <?php if ($event_records): ?><ol class="speaker-event-timeline"><?php foreach ($event_records as $event_record): $post = $event_record; setup_postdata($post); $event_start = cia_event_datetime(get_the_ID(), 'event_start'); $event_venue = trim((string) get_post_meta(get_the_ID(), 'venue', true)); $event_has_media = has_post_thumbnail(get_the_ID()); ?>
      <li class="speaker-timeline-item">
        <?php if ($event_start): ?><time class="speaker-timeline-date" datetime="<?php echo esc_attr($event_start->format(DATE_W3C)); ?>"><strong><?php echo esc_html($event_start->format('d')); ?></strong><span><?php echo esc_html(wp_date('M Y', $event_start->getTimestamp(), cia_event_timezone())); ?></span></time><?php endif; ?>
        <span class="speaker-timeline-node" aria-hidden="true"></span>
        <article class="speaker-timeline-card<?php echo $event_has_media ? ' has-media' : ' no-media'; ?>">
          <?php if ($event_has_media): ?><a class="speaker-timeline-media" href="<?php the_permalink(); ?>"><?php cia_card_media(get_the_ID(), 'full'); ?></a><?php endif; ?>
          <div class="speaker-timeline-copy"><span class="media-kicker section-brow">Event</span><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php $event_date = cia_event_format_range(get_the_ID(), cia_home_language()); if ($event_date): ?><p class="speaker-related-meta"><?php echo esc_html($event_date); ?></p><?php endif; ?><?php if ($event_venue): ?><p class="speaker-timeline-venue"><?php echo esc_html($event_venue); ?></p><?php endif; ?></div>
        </article>
      </li>
    <?php endforeach; wp_reset_postdata(); ?></ol><?php else: ?><div class="editorial-empty">Belum ada penampilan event yang tertaut.</div><?php endif; ?>
  </div></section>

  <section class="editorial-listing speaker-article-section" aria-labelledby="speaker-articles-title"><div class="container">
    <div class="editorial-section-heading"><span class="media-kicker section-brow">Liputan editorial</span><h2 id="speaker-articles-title">Artikel yang Menyebut <?php echo esc_html($name); ?></h2><p><?php echo esc_html((string) count($article_records)); ?> artikel terbit</p></div>
    <?php if ($article_records): ?><div class="speaker-article-list"><?php foreach ($article_records as $article_index=>$article_record): $post = $article_record; setup_postdata($post); $article_has_media = has_post_thumbnail(get_the_ID()); $article_excerpt = wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 26, '…'); ?>
      <article class="speaker-article-row<?php echo $article_has_media ? ' has-media' : ' no-media'; ?>">
        <span class="speaker-article-index" aria-hidden="true"><?php echo esc_html(sprintf('%02d', $article_index + 1)); ?></span>
        <?php if ($article_has_media): ?><a class="speaker-article-media" href="<?php the_permalink(); ?>"><?php cia_card_media(get_the_ID(), 'full'); ?></a><?php endif; ?>
        <div class="speaker-article-copy"><div class="speaker-article-meta"><span>Artikel</span><time datetime="<?php echo esc_attr(get_post_time(DATE_W3C, true, get_the_ID())); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), cia_home_language())); ?></time></div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php if ($article_excerpt): ?><p class="speaker-article-excerpt"><?php echo esc_html($article_excerpt); ?></p><?php endif; ?><a class="speaker-article-read" href="<?php the_permalink(); ?>"><?php echo esc_html(cia_home_language() === 'en' ? 'Read article' : 'Baca artikel'); ?><span aria-hidden="true">→</span></a></div>
      </article>
    <?php endforeach; wp_reset_postdata(); ?></div><?php else: ?><div class="editorial-empty">Belum ada artikel yang menyebut figur ini.</div><?php endif; ?>
  </div></section>
</div>
<?php get_footer(); ?>
