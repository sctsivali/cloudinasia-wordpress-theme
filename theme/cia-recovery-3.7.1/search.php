<?php
if (!defined('ABSPATH')) { exit; }

$lang = cia_home_language();
$query = trim(sanitize_text_field(get_search_query(false)));
$scope_raw = isset($_GET['scope']) ? sanitize_key(wp_unslash($_GET['scope'])) : 'all';
$scope = in_array($scope_raw, cia_search_supported_scopes(), true) ? $scope_raw : 'all';
$page_raw = isset($_GET['paged']) ? absint($_GET['paged']) : absint(get_query_var('paged'));
$paged = max(1, $page_raw);
$results = cia_search_archive_results($query, $lang, $scope, $paged, 12);

$copy = $lang === 'en' ? array(
    'kicker'=>'Search the archive','title'=>'Find knowledge, events, and people.','deck'=>'Search published articles, verified events, speakers, and collaborators recorded by Cloud in Asia.','label'=>'Search Cloud in Asia','placeholder'=>'Article, event, speaker, or collaborator','submit'=>'Search','summary'=>'results across the archive','all'=>'All','articles'=>'Articles','events'=>'Events','speakers'=>'Speakers','collaborators'=>'Collaborators','article'=>'Article','event'=>'Event','speaker'=>'Speaker','collaborator'=>'Collaborator','read'=>'Read article','view_event'=>'Open event','view_profile'=>'Open profile','view_partner'=>'Open directory record','see_all'=>'See all','empty_title'=>'No matching archive record yet.','empty_body'=>'Try a shorter name, organization, topic, or event title. You can also browse the archive directly.','browse_articles'=>'Browse articles','browse_events'=>'Browse events','browse_speakers'=>'Browse speakers','browse_partners'=>'Browse collaborators','unknown_date'=>'Date not recorded','evidence'=>'Recorded in the event archive',
) : array(
    'kicker'=>'Pencarian arsip','title'=>'Temukan pengetahuan, event, dan orang.','deck'=>'Cari artikel terbit, event terverifikasi, speakers, dan kolaborator yang tercatat di Cloud in Asia.','label'=>'Cari di Cloud in Asia','placeholder'=>'Artikel, event, speaker, atau kolaborator','submit'=>'Cari','summary'=>'hasil di seluruh arsip','all'=>'Semua','articles'=>'Artikel','events'=>'Event','speakers'=>'Speakers','collaborators'=>'Kolaborator','article'=>'Artikel','event'=>'Event','speaker'=>'Speaker','collaborator'=>'Kolaborator','read'=>'Baca artikel','view_event'=>'Buka event','view_profile'=>'Buka profil','view_partner'=>'Buka catatan direktori','see_all'=>'Lihat semua','empty_title'=>'Belum ada catatan arsip yang cocok.','empty_body'=>'Coba nama yang lebih pendek, organisasi, topik, atau judul event. Anda juga dapat membuka arsip secara langsung.','browse_articles'=>'Jelajahi artikel','browse_events'=>'Jelajahi event','browse_speakers'=>'Jelajahi speakers','browse_partners'=>'Jelajahi kolaborator','unknown_date'=>'Tanggal belum tercatat','evidence'=>'Tercatat dalam arsip event',
);

$scope_url = static function($target) use ($query, $lang) {
    $args = array('s'=>$query,'scope'=>$target);
    if ($lang === 'en') { $args['lang'] = 'en'; }
    return add_query_arg($args, home_url('/'));
};
$scope_labels = array('all'=>$copy['all'],'articles'=>$copy['articles'],'events'=>$copy['events'],'speakers'=>$copy['speakers'],'collaborators'=>$copy['collaborators']);
$total = (int) $results['total'];

get_header();
?>
<main class="editorial-surface search-archive" data-search-groups="search-group-articles search-group-events search-group-speakers search-group-collaborators">
  <header class="search-archive-hero">
    <div class="container search-archive-hero-grid">
      <div><p class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></p><h1><?php echo esc_html($copy['title']); ?></h1><p><?php echo esc_html($copy['deck']); ?></p></div>
      <form class="search-archive-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <label for="archive-search-query"><?php echo esc_html($copy['label']); ?></label>
        <div><input id="archive-search-query" type="search" name="s" value="<?php echo esc_attr($query); ?>" placeholder="<?php echo esc_attr($copy['placeholder']); ?>" autocomplete="off"><button type="submit"><?php echo esc_html($copy['submit']); ?> <span aria-hidden="true">→</span></button></div>
        <input type="hidden" name="scope" value="<?php echo esc_attr($scope); ?>"><?php if ($lang === 'en'): ?><input type="hidden" name="lang" value="en"><?php endif; ?>
      </form>
    </div>
  </header>

  <section class="search-archive-main" aria-labelledby="search-results-title">
    <div class="container">
      <div class="search-archive-summary"><div><span><?php echo esc_html(number_format_i18n($total)); ?></span><p id="search-results-title"><?php echo esc_html($copy['summary']); ?><?php if ($query !== ''): ?> “<strong><?php echo esc_html($query); ?></strong>”<?php endif; ?></p></div></div>
      <nav class="search-scope-nav" aria-label="<?php echo esc_attr($copy['kicker']); ?>">
        <?php foreach ($scope_labels as $key=>$label): $count = $key === 'all' ? $total : (int) $results['groups'][$key]['total']; ?>
          <a href="<?php echo esc_url($scope_url($key)); ?>"<?php echo $scope === $key ? ' aria-current="page"' : ''; ?>><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html(number_format_i18n($count)); ?></strong></a>
        <?php endforeach; ?>
      </nav>

      <?php if ($total > 0):
        $visible_groups = $scope === 'all' ? array('articles','events','speakers','collaborators') : array($scope);
        foreach ($visible_groups as $group_key):
          $group = $results['groups'][$group_key];
          if (!$group['items'] && $scope === 'all') { continue; }
      ?>
        <section id="search-group-<?php echo esc_attr($group_key); ?>" class="search-result-group" aria-labelledby="search-group-<?php echo esc_attr($group_key); ?>-title">
          <header class="search-result-group-heading"><div><p class="media-kicker section-brow"><?php echo esc_html($scope_labels[$group_key]); ?></p><h2 id="search-group-<?php echo esc_attr($group_key); ?>-title"><?php echo esc_html($scope_labels[$group_key]); ?></h2></div><div><strong><?php echo esc_html(number_format_i18n((int) $group['total'])); ?></strong><?php if ($scope === 'all' && (int) $group['total'] > count($group['items'])): ?><a href="<?php echo esc_url($scope_url($group_key)); ?>"><?php echo esc_html($copy['see_all']); ?> →</a><?php endif; ?></div></header>
          <div class="search-result-list">
            <?php foreach ($group['items'] as $item):
              if ($group_key === 'articles' || $group_key === 'events'):
                $item_id = (int) $item->ID;
                $is_event = $group_key === 'events';
                $kind = $is_event ? $copy['event'] : $copy['article'];
                $date = $is_event ? cia_event_format_range($item_id, $lang) : cia_editorial_date($item_id, $lang);
                $excerpt = wp_trim_words(wp_strip_all_tags(get_the_excerpt($item)), 28, '…');
                $venue = $is_event ? trim((string) get_post_meta($item_id, 'venue', true)) : '';
            ?>
              <article class="search-result-card search-result-<?php echo esc_attr($group_key); ?>">
                <a class="search-result-media" href="<?php echo esc_url(get_permalink($item_id)); ?>" tabindex="-1" aria-hidden="true"><?php cia_card_media($item_id, 'full'); ?></a>
                <div class="search-result-copy"><div class="search-result-meta"><span><?php echo esc_html($kind); ?></span><time><?php echo esc_html($date ?: $copy['unknown_date']); ?></time><?php if ($venue !== ''): ?><span><?php echo esc_html($venue); ?></span><?php endif; ?></div><h3><a href="<?php echo esc_url(get_permalink($item_id)); ?>"><?php echo esc_html(get_the_title($item)); ?></a></h3><?php if ($excerpt !== ''): ?><p><?php echo esc_html($excerpt); ?></p><?php endif; ?><a class="search-result-action" href="<?php echo esc_url(get_permalink($item_id)); ?>"><?php echo esc_html($is_event ? $copy['view_event'] : $copy['read']); ?> →</a></div>
              </article>
            <?php elseif ($group_key === 'speakers'):
                $term_link = get_term_link($item);
                if (is_wp_error($term_link)) { continue; }
                $person = cia_people_directory_record((int) $item->term_id);
                $portrait = (string) ($person['portrait']['theme_path'] ?? '');
                $has_portrait = preg_match('~^assets/people/[a-zA-Z0-9._-]+\.png$~', $portrait) && is_file(get_template_directory() . '/' . $portrait);
                $affiliation = trim((string) ($person['affiliation_note'] ?? ''));
            ?>
              <article class="search-result-card search-result-entity"><a class="search-entity-mark" href="<?php echo esc_url($term_link); ?>" tabindex="-1" aria-hidden="true"><?php if ($has_portrait): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/' . $portrait); ?>" alt="" width="400" height="400" loading="lazy" decoding="async"><?php else: ?><span><?php echo esc_html(cia_entity_initials($item->name)); ?></span><?php endif; ?></a><div class="search-result-copy"><div class="search-result-meta"><span><?php echo esc_html($copy['speaker']); ?></span><span><?php echo esc_html(number_format_i18n((int) $item->count)); ?> event</span></div><h3><a href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($item->name); ?></a></h3><?php if ($affiliation !== ''): ?><p><?php echo esc_html($affiliation); ?></p><?php elseif ($item->description): ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($item->description), 28, '…')); ?></p><?php endif; ?><a class="search-result-action" href="<?php echo esc_url($term_link); ?>"><?php echo esc_html($copy['view_profile']); ?> →</a></div></article>
            <?php else:
                $term = $item['term'];
                $logo = cia_partner_logo_record($term->slug);
                $logo_file = (string) ($logo['file'] ?? '');
            ?>
              <article class="search-result-card search-result-entity"><a class="search-entity-mark search-collaborator-mark" href="<?php echo esc_url($item['url']); ?>" tabindex="-1" aria-hidden="true"><?php if ($logo_file !== ''): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/partner-logos/' . $logo_file); ?>" alt="" loading="lazy" decoding="async"><?php else: ?><span><?php echo esc_html(cia_entity_initials($item['name'])); ?></span><?php endif; ?></a><div class="search-result-copy"><div class="search-result-meta"><span><?php echo esc_html($copy['collaborator']); ?></span><span><?php echo esc_html($item['role']); ?></span></div><h3><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['name']); ?></a></h3><p><?php echo esc_html($copy['evidence']); ?> · <?php echo esc_html(number_format_i18n((int) $term->count)); ?> event</p><a class="search-result-action" href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($copy['view_partner']); ?> →</a></div></article>
            <?php endif; endforeach; ?>
          </div>
        </section>
      <?php endforeach; cia_search_pagination($query, $scope, $lang, $paged, (int) $results['pages']); ?>
      <?php else: ?>
        <section class="search-empty-state"><p class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></p><h2><?php echo esc_html($copy['empty_title']); ?></h2><p><?php echo esc_html($copy['empty_body']); ?></p><div><a href="<?php echo esc_url(cia_localized_url(home_url('/blog/'), $lang)); ?>"><?php echo esc_html($copy['browse_articles']); ?></a><a href="<?php echo esc_url(cia_localized_url(get_post_type_archive_link('events'), $lang)); ?>"><?php echo esc_html($copy['browse_events']); ?></a><a href="<?php echo esc_url(cia_localized_url(home_url('/speakers/'), $lang)); ?>"><?php echo esc_html($copy['browse_speakers']); ?></a><a href="<?php echo esc_url(cia_localized_url(home_url('/partners/'), $lang)); ?>"><?php echo esc_html($copy['browse_partners']); ?></a></div></section>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php get_footer(); ?>
