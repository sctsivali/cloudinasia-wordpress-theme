<?php
$lang = cia_home_language();
$title = is_category() ? single_cat_title('', false) : (is_tag() ? single_tag_title('', false) :
    (is_tax() ? single_term_title('', false) : wp_strip_all_tags(get_the_archive_title())));
$copy = $lang === 'en' ? array(
    'kicker'=>'Editorial archive', 'deck'=>'Reporting, analysis, contributors, and people shaping the technology conversation.',
    'latest'=>'Latest stories', 'all'=>'All', 'filter'=>'Filter topics', 'featured'=>'Featured story',
    'contributor'=>'Contributor', 'mentions'=>'People mentioned', 'institutions'=>'Institutions mentioned',
    'institution_note'=>'Article mention only; not active partnership status.', 'popular'=>'Popular in the archive',
    'basis'=>'Ranked by inbound internal article references, not page views.', 'more'=>'Load more', 'empty'=>'No published records found.'
) : array(
    'kicker'=>'Arsip editorial', 'deck'=>'Berita, analisis, kontributor, dan figur yang membentuk percakapan teknologi.',
    'latest'=>'Artikel terbaru', 'all'=>'Semua', 'filter'=>'Filter topik', 'featured'=>'Sorotan utama',
    'contributor'=>'Kontributor', 'mentions'=>'Figur disebut', 'institutions'=>'Institusi disebut',
    'institution_note'=>'Sebutan dalam artikel; bukan status partner aktif.', 'popular'=>'Populer di arsip',
    'basis'=>'Berdasarkan rujukan internal antarartikel, bukan angka page-view.', 'more'=>'Muat lebih banyak', 'empty'=>'Belum ada artikel yang diterbitkan.'
);
global $wp_query, $post;
$archive_records = array();
foreach ((array) $wp_query->posts as $archive_candidate) {
    $archive_record = get_post($archive_candidate);
    if ($archive_record instanceof WP_Post) { $archive_records[] = $archive_record; }
}
$featured = $archive_records ? array_shift($archive_records) : null;
$popular_category_id = is_category() ? (int) get_queried_object_id() : 0;
$popular = cia_archive_popular_posts($lang, 5, $popular_category_id);
$popular_heading = $popular_category_id > 0
    ? sprintf($lang === 'en' ? 'Popular in %s' : 'Populer di %s', $title)
    : $copy['popular'];
$filter_terms = array();
foreach ($archive_records as $archive_record) {
    foreach (get_the_category($archive_record->ID) as $term) { $filter_terms[$term->slug] = $term->name; }
}
$filter_terms = array_slice($filter_terms, 0, 6, true);
get_header();
?>
<div class="archive-portal" data-surface="explore" data-archive-portal data-count-label="<?php echo esc_attr(strtolower($copy['latest'])); ?>">
  <header class="archive-portal-header">
    <div class="container">
      <span class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?> / <?php echo esc_html($title); ?></span>
      <h1><?php echo esc_html($title); ?></h1>
      <p><?php echo esc_html($copy['deck']); ?></p>
      <?php the_archive_description('<div class="archive-portal-description">','</div>'); ?>
    </div>
  </header>

  <?php if ($featured instanceof WP_Post): $post = $featured; setup_postdata($post); ?>
  <section class="archive-portal-featured" aria-label="<?php echo esc_attr($copy['featured']); ?>" data-reveal>
    <div class="container archive-portal-hero">
      <a class="archive-portal-featured-media" href="<?php the_permalink(); ?>"><?php cia_card_media(get_the_ID(), 'full'); ?></a>
      <div class="archive-portal-featured-copy">
        <span class="media-kicker section-brow"><?php echo esc_html($copy['featured']); ?></span>
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <p><?php echo esc_html(get_the_excerpt()); ?></p>
        <div class="archive-portal-byline"><span><?php echo cia_archive_icon('person'); ?><?php echo esc_html($copy['contributor']); ?></span><strong><?php echo esc_html(cia_archive_contributor_name()); ?></strong><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time></div>
        <?php $mentions = cia_archive_mentions(); if ($mentions): ?><div class="archive-portal-mentions"><span><?php echo esc_html($copy['mentions']); ?></span><?php foreach (array_slice($mentions,0,3) as $name): ?><b><?php echo esc_html($name); ?></b><?php endforeach; ?><?php if (count($mentions)>3): ?><b>+<?php echo esc_html((string)(count($mentions)-3)); ?></b><?php endif; ?></div><?php endif; ?>
        <?php $institutions = cia_archive_institutions(); if ($institutions): ?><div class="archive-portal-institutions" aria-label="<?php echo esc_attr($copy['institutions'] . '. ' . $copy['institution_note']); ?>" data-relationship-scope="mention-only"><span><?php echo esc_html($copy['institutions']); ?></span><?php foreach (array_slice($institutions, 0, 3) as $name): ?><b><?php echo esc_html($name); ?></b><?php endforeach; ?><?php if (count($institutions)>3): ?><a href="<?php the_permalink(); ?>#article-entities" aria-label="<?php echo esc_attr(sprintf($lang === 'en' ? 'View %d more institutions' : 'Lihat %d institusi lainnya', count($institutions) - 3)); ?>">+<?php echo esc_html((string)(count($institutions) - 3)); ?></a><?php endif; ?></div><?php endif; ?>
      </div>
    </div>
  </section>
  <?php wp_reset_postdata(); endif; ?>

  <nav class="archive-portal-filters" aria-label="<?php echo esc_attr($copy['filter']); ?>">
    <div class="container">
      <button type="button" class="is-active" data-archive-filter="all" aria-pressed="true"><?php echo cia_archive_icon('filter'); ?><?php echo esc_html($copy['all']); ?></button>
      <?php foreach ($filter_terms as $slug=>$name): ?><button type="button" data-archive-filter="<?php echo esc_attr($slug); ?>" aria-pressed="false"><?php echo esc_html($name); ?></button><?php endforeach; ?>
      <span data-archive-result-count aria-live="polite"><?php echo esc_html((string) count($archive_records)); ?> <?php echo esc_html(strtolower($copy['latest'])); ?></span>
    </div>
  </nav>

  <section class="archive-portal-body"><div class="container archive-portal-layout">
    <div class="archive-portal-stream">
      <div class="archive-portal-section-head"><h2><?php echo esc_html($copy['latest']); ?></h2><span><?php echo esc_html((string) count($archive_records)); ?></span></div>
      <?php if ($archive_records): foreach ($archive_records as $index=>$archive_record): $post=$archive_record; setup_postdata($post); $terms=wp_get_post_categories($post->ID,array('fields'=>'slugs')); ?>
        <article class="archive-portal-card<?php echo $index >= 6 ? ' is-pending' : ''; ?>" data-archive-card data-topics="<?php echo esc_attr(implode(' ',array_map('sanitize_html_class',$terms))); ?>" data-reveal>
          <a class="archive-portal-card-media" href="<?php the_permalink(); ?>"><?php cia_card_media(get_the_ID(), 'full'); ?></a>
          <div class="archive-portal-card-copy">
            <div class="archive-portal-card-meta"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(),$lang)); ?></time></div>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <p><?php echo esc_html(get_the_excerpt()); ?></p>
            <div class="archive-portal-byline"><span><?php echo esc_html($copy['contributor']); ?></span><strong><?php echo esc_html(cia_archive_contributor_name()); ?></strong></div>
            <?php $mentions=cia_archive_mentions(); ?><div class="archive-portal-mentions"><span><?php echo esc_html($copy['mentions']); ?></span><?php if ($mentions): foreach(array_slice($mentions,0,3) as $name): ?><b><?php echo esc_html($name); ?></b><?php endforeach; if(count($mentions)>3): ?><b>+<?php echo esc_html((string)(count($mentions)-3)); ?></b><?php endif; else: ?><i>—</i><?php endif; ?></div>
            <?php $institutions=cia_archive_institutions(); if ($institutions): ?><div class="archive-portal-institutions" aria-label="<?php echo esc_attr($copy['institutions'] . '. ' . $copy['institution_note']); ?>" data-relationship-scope="mention-only"><span><?php echo esc_html($copy['institutions']); ?></span><?php foreach (array_slice($institutions, 0, 3) as $name): ?><b><?php echo esc_html($name); ?></b><?php endforeach; ?><?php if (count($institutions)>3): ?><a href="<?php the_permalink(); ?>#article-entities" aria-label="<?php echo esc_attr(sprintf($lang === 'en' ? 'View %d more institutions' : 'Lihat %d institusi lainnya', count($institutions) - 3)); ?>">+<?php echo esc_html((string)(count($institutions) - 3)); ?></a><?php endif; ?></div><?php endif; ?>
          </div>
        </article>
      <?php endforeach; wp_reset_postdata(); else: ?><div class="editorial-empty"><?php echo esc_html($copy['empty']); ?></div><?php endif; ?>
      <?php if (count($archive_records)>6): ?><button type="button" class="archive-portal-load-more" data-archive-load-more><?php echo esc_html($copy['more']); ?><?php echo cia_archive_icon('arrow'); ?></button><?php endif; ?>
      <div class="editorial-pagination archive-portal-fallback-pagination"><?php the_posts_pagination(); ?></div>
    </div>

    <aside class="archive-portal-sidebar" aria-label="<?php echo esc_attr($popular_heading); ?>" data-reveal>
      <h2><?php echo esc_html($popular_heading); ?></h2>
      <p><?php echo esc_html($copy['basis']); ?></p>
      <?php if ($popular): ?><ol><?php foreach ($popular as $item): ?><li><a href="<?php echo esc_url(get_permalink($item['post'])); ?>"><?php echo esc_html(get_the_title($item['post'])); ?></a><small><?php echo esc_html((string)$item['internal_reference_count']); ?> <?php echo esc_html($lang === 'en' ? 'internal references' : 'rujukan internal'); ?></small></li><?php endforeach; ?></ol><?php else: ?><p><?php echo esc_html($copy['empty']); ?></p><?php endif; ?>
    </aside>
  </div></section>
</div>
<?php get_footer(); ?>
