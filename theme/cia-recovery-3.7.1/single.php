<?php
$lang = function_exists('cia_translation_locale') ? cia_translation_locale(get_queried_object_id()) : cia_home_language();
$copy = $lang === 'en' ? array(
    'kicker'=>'Cloud in Asia / Editorial', 'context'=>'Article utilities', 'contributor'=>'Contributor',
    'share'=>'Share article', 'copy_link'=>'Copy link', 'copy_ready'=>'Link copied', 'copy_failed'=>'Automatic copy failed. Select the link below and copy it manually.',
    'related'=>'Continue reading', 'related_from'=>'More from %s', 'related_same'=>'Same categories', 'recent'=>'Latest from Cloud in Asia',
    'entities'=>'Inside this article', 'entities_note'=>'Names below are explicit article mentions. Institution mentions do not imply an active partnership.',
    'institutions'=>'Institutions mentioned', 'people'=>'People mentioned', 'logo_fallback'=>'Identity mark',
    'empty_people'=>'No audited people mentions are available for this article.'
) : array(
    'kicker'=>'Cloud in Asia / Editorial', 'context'=>'Utilitas artikel', 'contributor'=>'Kontributor',
    'share'=>'Bagikan artikel', 'copy_link'=>'Salin tautan', 'copy_ready'=>'Tautan berhasil disalin', 'copy_failed'=>'Penyalinan otomatis gagal. Pilih tautan berikut lalu salin secara manual.',
    'related'=>'Lanjut membaca', 'related_from'=>'Lanjut membaca · %s', 'related_same'=>'Kategori yang sama', 'recent'=>'Terbaru di Cloud in Asia',
    'entities'=>'Di dalam artikel ini', 'entities_note'=>'Nama berikut disebut secara eksplisit dalam artikel. Penyebutan institusi bukan status partner aktif.',
    'institutions'=>'Institusi disebut', 'people'=>'Figur disebut', 'logo_fallback'=>'Penanda identitas',
    'empty_people'=>'Belum ada data figur terverifikasi untuk artikel ini.'
);
get_header();
?>
<div class="editorial-surface editorial-article article-modern">
<?php while (have_posts()): the_post();
    $institutions = cia_archive_institutions(get_the_ID());
    $people = cia_article_people(get_the_ID());
    $current_article_id = get_the_ID();
    $share_url = get_permalink($current_article_id);
    $share_title = get_the_title($current_article_id);
    $article_category_ids = array_values(array_unique(array_map('intval', wp_get_post_categories($current_article_id, array('fields'=>'ids')))));
    $primary_category_id = (int) get_post_meta($current_article_id, '_yoast_wpseo_primary_category', true);
    $related_category_ids = $article_category_ids;
    $related_category_label = $copy['related_same'];
    if ($primary_category_id && in_array($primary_category_id, $article_category_ids, true)) {
        $related_category_ids = array($primary_category_id);
        $primary_category = get_term($primary_category_id, 'category');
        if ($primary_category && !is_wp_error($primary_category)) {
            $related_category_label = $primary_category->name;
        }
    }
    $related_same_category = array();
    if ($related_category_ids) {
        $related_query = new WP_Query(cia_editorial_query_args($lang, array(
            'category__in'=>$related_category_ids,
            'post__not_in'=>array($current_article_id),
            'posts_per_page'=>3,
            'ignore_sticky_posts'=>true,
        )));
        $related_same_category = $related_query->posts;
    }
    $related_excluded_ids = array_merge(array($current_article_id), wp_list_pluck($related_same_category, 'ID'));
    $related_fallback = array();
    $related_slots = 3 - count($related_same_category);
    if ($related_slots > 0) {
        $fallback_query = new WP_Query(cia_editorial_query_args($lang, array(
            'post__not_in'=>$related_excluded_ids,
            'posts_per_page'=>$related_slots,
            'ignore_sticky_posts'=>true,
        )));
        $related_fallback = $fallback_query->posts;
    }
?>
  <header class="article-modern-hero">
    <div class="container article-modern-heading">
      <span class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></span>
      <div class="article-modern-meta"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time><span><?php the_category(' · '); ?></span></div>
      <h1><?php the_title(); ?></h1>
      <?php if (has_excerpt()): ?><p class="article-modern-deck"><?php echo esc_html(get_the_excerpt()); ?></p><?php endif; ?>
      <div class="article-modern-byline"><span><?php echo esc_html($copy['contributor']); ?></span><strong><?php echo esc_html(cia_archive_contributor_name()); ?></strong></div>
    </div>
  </header>

  <div class="container article-modern-layout">
    <article class="entry editorial-article-body article-reading-column">
      <figure class="editorial-article-media article-modern-media"><?php cia_card_media(get_the_ID(), 'full'); ?></figure>
      <div class="entry-content"><?php the_content(); ?></div>
    </article>
    <aside class="article-browse-rail" aria-labelledby="article-utilities-title">
      <h2 class="screen-reader-text" id="article-utilities-title"><?php echo esc_html($copy['context']); ?></h2>
      <section class="article-share-tools" aria-labelledby="article-share-title">
        <h3 id="article-share-title"><?php echo esc_html($copy['share']); ?></h3>
        <div class="article-share-actions">
          <a href="<?php echo esc_url('https://wa.me/?text=' . rawurlencode($share_title . ' ' . $share_url)); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($lang === 'en' ? 'Share this article to WhatsApp' : 'Bagikan artikel ini ke WhatsApp'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M20 11.7a8 8 0 0 1-11.8 7l-4.2 1.1 1.1-4.1A8 8 0 1 1 20 11.7Z"/><path d="M9.2 8.1c.2-.4.4-.4.7-.4h.5l.7 1.8c.1.3.1.5-.1.7l-.5.6c.8 1.5 1.8 2.4 3.4 3l.5-.6c.2-.2.4-.3.7-.2l1.8.8c.3.1.4.3.4.6 0 .8-.5 1.5-1.2 1.8-.6.3-1.5.4-2.7 0-1.5-.5-3-1.4-4.2-2.7-1.1-1.2-1.9-2.5-2.2-3.7-.2-.8 0-1.3.2-1.7Z"/></svg></a>
          <a href="<?php echo esc_url('https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($share_url)); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($lang === 'en' ? 'Share this article to LinkedIn' : 'Bagikan artikel ini ke LinkedIn'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.5 9.5V18M6.5 6.5v.1M10.5 18v-8.5M10.5 13.2c0-2 3-2.8 4.4-1.4.5.5.6 1.3.6 2.2v4"/></svg></a>
          <a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($share_url)); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($lang === 'en' ? 'Share this article to Facebook' : 'Bagikan artikel ini ke Facebook'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M14 20v-7h2.5l.4-3H14V8.2c0-.9.3-1.5 1.6-1.5H17V4.1c-.5-.1-1.3-.1-2.2-.1-2.3 0-3.8 1.4-3.8 3.9V10H8.5v3H11v7"/></svg></a>
          <a href="<?php echo esc_url('https://x.com/intent/post?text=' . rawurlencode($share_title) . '&url=' . rawurlencode($share_url)); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($lang === 'en' ? 'Share this article to X' : 'Bagikan artikel ini ke X'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m5 4 14 16M19 4 5 20"/></svg></a>
        </div>
        <button class="article-copy-link" type="button" data-share-url="<?php echo esc_url($share_url); ?>" aria-describedby="article-share-status"><span><?php echo esc_html($copy['copy_link']); ?></span><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><rect x="8" y="8" width="10" height="11"/><path d="M15 8V5H5v11h3"/></svg></button>
        <p class="article-share-status" id="article-share-status" role="status" aria-live="polite" data-copy-success="<?php echo esc_attr($copy['copy_ready']); ?>" data-copy-failed="<?php echo esc_attr($copy['copy_failed']); ?>"></p>
        <input class="article-copy-manual" id="article-copy-manual" type="text" value="<?php echo esc_url($share_url); ?>" aria-label="<?php echo esc_attr($copy['copy_link']); ?>" readonly hidden>
      </section>

      <?php if ($related_same_category || $related_fallback):
        $related_title = $related_same_category ? sprintf($copy['related_from'], $related_category_label) : $copy['related'];
      ?>
      <nav class="article-related-reading" aria-labelledby="article-related-title">
        <h3 id="article-related-title"><?php echo esc_html($related_title); ?></h3>
        <?php if ($related_same_category): ?><ul class="article-related-list">
          <?php foreach ($related_same_category as $related_post): $post = $related_post; setup_postdata($post); ?><li><a href="<?php the_permalink(); ?>"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time><strong><?php the_title(); ?></strong></a></li><?php endforeach; wp_reset_postdata(); ?>
        </ul><?php endif; ?>
        <?php if ($related_fallback): ?><p class="article-related-fallback-label"><?php echo esc_html($copy['recent']); ?></p><ul class="article-related-list is-fallback">
          <?php foreach ($related_fallback as $fallback_post): $post = $fallback_post; setup_postdata($post); ?><li><a href="<?php the_permalink(); ?>"><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time><strong><?php the_title(); ?></strong></a></li><?php endforeach; wp_reset_postdata(); ?>
        </ul><?php endif; ?>
      </nav>
      <?php endif; ?>
    </aside>
  </div>

  <?php if ($institutions || $people): ?>
  <section class="article-entity-section" id="article-entities" aria-labelledby="article-entities-title">
    <div class="container">
      <div class="article-entity-intro"><span class="media-kicker section-brow"><?php echo esc_html($copy['entities']); ?></span><h2 id="article-entities-title"><?php echo esc_html($copy['entities']); ?></h2><p><?php echo esc_html($copy['entities_note']); ?></p></div>
      <div class="article-entity-columns">
        <section class="article-entity-panel" aria-labelledby="article-institutions-title">
          <header><span aria-hidden="true">01</span><h3 id="article-institutions-title"><?php echo esc_html($copy['institutions']); ?></h3><strong><?php echo esc_html((string) count($institutions)); ?></strong></header>
          <ul class="article-institution-list">
          <?php foreach ($institutions as $name): $mark = cia_institution_mark($name); ?>
            <li><div class="article-entity-logo<?php echo $mark['type'] === 'initials' ? ' is-fallback' : (!empty($mark['variant']) && $mark['variant'] === 'dark' ? ' is-dark' : ''); ?>"<?php if ($mark['type'] === 'initials'): ?> aria-label="<?php echo esc_attr($copy['logo_fallback']); ?>"<?php endif; ?>><?php if ($mark['type'] === 'image'): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/' . $mark['path']); ?>" alt="<?php echo esc_attr($mark['alt']); ?>" loading="lazy"><?php else: ?><span aria-hidden="true"><?php echo esc_html($mark['text']); ?></span><?php endif; ?></div><div><strong><?php echo esc_html($name); ?></strong><small><?php echo esc_html($lang === 'en' ? 'Mentioned institution' : 'Institusi yang disebut'); ?></small></div></li>
          <?php endforeach; ?>
          </ul>
        </section>

        <section class="article-entity-panel" aria-labelledby="article-people-title">
          <header><span aria-hidden="true">02</span><h3 id="article-people-title"><?php echo esc_html($copy['people']); ?></h3><strong><?php echo esc_html((string) count($people)); ?></strong></header>
          <?php if ($people): ?><ul class="article-people-list">
          <?php foreach ($people as $person): ?>
            <li><?php if ($person['portrait']): ?><img class="article-person-portrait" src="<?php echo esc_url(get_template_directory_uri() . '/' . $person['portrait']); ?>" alt="<?php echo esc_attr($person['name']); ?>" loading="lazy" width="112" height="112"><?php else: ?><div class="article-person-portrait is-fallback" aria-label="<?php echo esc_attr($copy['logo_fallback']); ?>"><span aria-hidden="true"><?php echo esc_html(cia_entity_initials($person['name'])); ?></span></div><?php endif; ?><div><strong><?php echo esc_html($person['name']); ?></strong><?php if ($person['affiliation']): ?><small><?php echo esc_html($person['affiliation']); ?></small><?php endif; ?></div></li>
          <?php endforeach; ?>
          </ul><?php else: ?><p class="article-entity-empty"><?php echo esc_html($copy['empty_people']); ?></p><?php endif; ?>
        </section>
      </div>
    </div>
  </section>
  <?php endif; ?>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
