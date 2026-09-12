<?php
$lang = cia_home_language();
$copy = $lang === 'en' ? array(
    'kicker'=>'Cloud in Asia newsroom', 'title'=>'Technology moves fast. Context should move faster.',
    'deck'=>'Independent reporting on cloud, open source, infrastructure, digital policy, and the ideas shaping Southeast Asia.',
    'latest'=>'Latest briefing', 'more'=>'Read story', 'all'=>'Explore desk', 'empty'=>'No published stories in this desk yet.',
    'flow'=>'Five editorial desks, one connected signal.', 'desk_label'=>'Editorial desks'
) : array(
    'kicker'=>'Ruang berita Cloud in Asia', 'title'=>'Teknologi bergerak cepat. Konteks harus lebih cepat.',
    'deck'=>'Liputan independen tentang cloud, open source, infrastruktur, kebijakan digital, dan gagasan yang membentuk Asia Tenggara.',
    'latest'=>'Briefing terbaru', 'more'=>'Baca artikel', 'all'=>'Jelajahi desk', 'empty'=>'Belum ada artikel terbit di desk ini.',
    'flow'=>'Lima desk editorial, satu sinyal yang terhubung.', 'desk_label'=>'Desk editorial'
);
$desks = array(
    array('slug'=>'cloud', 'name'=>'Cloud', 'index'=>'01'),
    array('slug'=>'open-source', 'name'=>'Open Source', 'index'=>'02'),
    array('slug'=>'infrastruktur', 'name'=>$lang === 'en' ? 'Infrastructure' : 'Infrastruktur', 'index'=>'03'),
    array('slug'=>'kebijakan', 'name'=>$lang === 'en' ? 'Policy' : 'Kebijakan', 'index'=>'04'),
    array('slug'=>'opini', 'name'=>$lang === 'en' ? 'Opinion' : 'Opini', 'index'=>'05'),
);
$top_query = new WP_Query(cia_editorial_query_args($lang, array('posts_per_page'=>5, 'ignore_sticky_posts'=>true)));
$top_posts = $top_query->posts;
$top_ids = wp_list_pluck($top_posts, 'ID');
$lead_post = array_shift($top_posts);
get_header();
?>
<div class="editorial-surface blog-newsroom">
  <section class="blog-newsroom-hero">
    <div class="container blog-newsroom-hero-grid">
      <div class="blog-newsroom-intro">
        <span class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></span>
        <h1><?php echo esc_html($copy['title']); ?></h1>
        <p><?php echo esc_html($copy['deck']); ?></p>
      </div>
      <figure class="blog-motion-frame is-loading" data-blog-motion-frame role="img" aria-label="<?php echo esc_attr($copy['flow']); ?>">
        <img class="blog-motion-fallback" src="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-editorial-signal-static.png'); ?>" width="720" height="520" alt="">
        <div class="blog-lottie" data-blog-lottie data-animation-path="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-editorial-signal-lottie.json'); ?>" aria-hidden="true"></div>
        <div class="blog-motion-labels" aria-hidden="true">
          <?php foreach ($desks as $desk): ?><span style="--y:<?php echo esc_attr((string) (14 + 16 * ((int) $desk['index'] - 1))); ?>%"><?php echo esc_html($desk['name']); ?></span><?php endforeach; ?>
        </div>
        <figcaption><?php echo esc_html($copy['flow']); ?></figcaption>
      </figure>
    </div>
  </section>

  <nav class="blog-category-nav" aria-label="<?php echo esc_attr($copy['desk_label']); ?>">
    <div class="container"><span><?php echo esc_html($copy['desk_label']); ?></span><?php foreach ($desks as $desk): ?><a href="#desk-<?php echo esc_attr($desk['slug']); ?>"><b><?php echo esc_html($desk['index']); ?></b><?php echo esc_html($desk['name']); ?></a><?php endforeach; ?></div>
  </nav>

  <?php if ($lead_post): $post = $lead_post; setup_postdata($post); ?>
  <section class="blog-top-package" aria-labelledby="blog-latest-title">
    <div class="container">
      <header class="blog-package-heading"><span class="media-kicker section-brow"><?php echo esc_html($copy['latest']); ?></span><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time></header>
      <div class="blog-top-grid">
        <article class="blog-top-lead">
          <?php $lead_cats = get_the_category(); if ($lead_cats): ?><span class="blog-story-topic"><?php echo esc_html($lead_cats[0]->name); ?></span><?php endif; ?>
          <h2 id="blog-latest-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          <?php $lead_excerpt = trim((string) get_the_excerpt()); if ($lead_excerpt): ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($lead_excerpt), 34, '…')); ?></p><?php endif; ?>
          <a class="blog-read-link" href="<?php the_permalink(); ?>"><?php echo esc_html($copy['more']); ?> <span aria-hidden="true">→</span></a>
        </article>
        <ol class="blog-latest-rail" aria-label="<?php echo esc_attr($copy['latest']); ?>">
          <?php foreach ($top_posts as $i => $post): setup_postdata($post); ?><li><article><span class="blog-story-number"><?php echo esc_html(sprintf('%02d', $i + 2)); ?></span><div><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div><span aria-hidden="true">↗</span></article></li><?php endforeach; ?>
        </ol>
      </div>
    </div>
  </section>
  <?php wp_reset_postdata(); endif; ?>

  <?php $desk_used_ids = $top_ids; ?>
  <div class="blog-desk-stack">
  <?php foreach ($desks as $desk_index => $desk):
      $desk_count_query = new WP_Query(cia_editorial_query_args($lang, array(
          'category_name'=>$desk['slug'], 'posts_per_page'=>1, 'fields'=>'ids',
          'ignore_sticky_posts'=>true,
      )));
      $desk_count = (int) $desk_count_query->found_posts;
      $desk_query = new WP_Query(cia_editorial_query_args($lang, array(
          'category_name'=>$desk['slug'], 'posts_per_page'=>4, 'post__not_in'=>$desk_used_ids,
          'ignore_sticky_posts'=>true,
      )));
      $desk_posts = $desk_query->posts;
      $desk_used_ids = array_values(array_unique(array_merge($desk_used_ids, wp_list_pluck($desk_posts, 'ID'))));
      $desk_lead = array_shift($desk_posts);
      $term = get_category_by_slug($desk['slug']);
      $desk_url = $term ? get_category_link($term->term_id) : home_url('/category/' . $desk['slug'] . '/');
  ?>
    <section class="blog-desk blog-desk-<?php echo esc_attr($desk['slug']); ?>" id="desk-<?php echo esc_attr($desk['slug']); ?>" aria-labelledby="desk-title-<?php echo esc_attr($desk['slug']); ?>">
      <div class="container">
        <header class="blog-desk-heading"><div><span class="blog-desk-index"><?php echo esc_html($desk['index']); ?></span><h2 id="desk-title-<?php echo esc_attr($desk['slug']); ?>"><?php echo esc_html($desk['name']); ?></h2></div><div><span><?php echo esc_html((string) $desk_count); ?> <?php echo esc_html($lang === 'en' ? ($desk_count === 1 ? 'article' : 'articles') : 'artikel'); ?></span><a href="<?php echo esc_url(cia_localized_url($desk_url, $lang)); ?>"><?php echo esc_html($copy['all']); ?> <span aria-hidden="true">→</span></a></div></header>
        <?php if ($desk_lead): $post = $desk_lead; setup_postdata($post); ?>
        <div class="blog-desk-layout">
          <article class="blog-desk-lead">
            <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php $desk_excerpt = trim((string) get_the_excerpt()); if ($desk_excerpt): ?><p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($desk_excerpt), 30, '…')); ?></p><?php endif; ?>
            <a class="blog-read-link" href="<?php the_permalink(); ?>"><?php echo esc_html($copy['more']); ?> <span aria-hidden="true">→</span></a>
          </article>
          <ol class="blog-desk-rail" aria-label="<?php echo esc_attr($desk['name']); ?>">
            <?php foreach ($desk_posts as $story_index => $post): setup_postdata($post); ?><li><article><span class="blog-story-number"><?php echo esc_html(sprintf('%02d', $story_index + 2)); ?></span><div><time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(cia_editorial_date(get_the_ID(), $lang)); ?></time><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3></div><span aria-hidden="true">↗</span></article></li><?php endforeach; ?>
          </ol>
        </div>
        <?php wp_reset_postdata(); else: ?><p class="blog-desk-empty"><?php echo esc_html($copy['empty']); ?></p><?php endif; ?>
      </div>
    </section>
  <?php endforeach; ?>
  </div>
</div>
<?php get_footer(); ?>
