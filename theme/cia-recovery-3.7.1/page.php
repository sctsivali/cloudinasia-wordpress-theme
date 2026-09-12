<?php
$page_lang = function_exists('cia_home_language') ? cia_home_language() : 'id';
get_header();
?>
<div class="editorial-surface editorial-page">
<?php while (have_posts()): the_post(); ?>
  <?php
  $page_title = get_the_title();
  $page_content = get_the_content();
  if ($page_lang === 'en') {
      $english_title = trim((string) get_post_meta(get_the_ID(), '_cia_title_en', true));
      $english_content = (string) get_post_meta(get_the_ID(), '_cia_content_en', true);
      if ($english_title !== '') { $page_title = $english_title; }
      if (trim($english_content) !== '') { $page_content = $english_content; }
  }
  $policy_versions = array(
      'editorial-policy'   => array('effective' => '2026-09-08', 'updated' => '2026-09-08', 'version' => '1.0'),
      'corrections-policy' => array('effective' => '2026-09-08', 'updated' => '2026-09-08', 'version' => '1.0'),
      'disclosure'         => array('effective' => '2026-09-08', 'updated' => '2026-09-08', 'version' => '1.0'),
      'privacy-policy'     => array('effective' => '2026-09-08', 'updated' => '2026-09-08', 'version' => '1.0'),
      'terms-of-use'       => array('effective' => '2026-09-08', 'updated' => '2026-09-08', 'version' => '1.0'),
  );
  $policy_versions = apply_filters('cia_policy_versions', $policy_versions);
  $policy_slug = get_post_field('post_name', get_the_ID());
  $policy_version = $policy_versions[$policy_slug] ?? null;
  if ($policy_version) {
      $page_content = (string) preg_replace('/^<p><em>(?:Diperbarui|Updated): 8 September 2026<\/em><\/p>/', '', $page_content, 1);
  }
  ?>
  <header class="editorial-page-hero"><div class="container"><span class="media-kicker section-brow">Cloud in Asia</span><h1><?php echo esc_html($page_title); ?></h1></div></header>
  <section class="editorial-page-main"><div class="container editorial-page-layout"><article class="entry editorial-page-body"><div class="entry-content">
    <?php if ($policy_version):
      $version_labels = $page_lang === 'en'
          ? array('label' => 'Policy version information', 'effective' => 'Effective from', 'updated' => 'Last updated', 'version' => 'Version', 'effective_date' => '8 September 2026', 'updated_date' => '8 September 2026')
          : array('label' => 'Informasi versi kebijakan', 'effective' => 'Berlaku sejak', 'updated' => 'Terakhir diperbarui', 'version' => 'Versi', 'effective_date' => '8 September 2026', 'updated_date' => '8 September 2026');
      $version_labels = apply_filters('cia_policy_version_labels', $version_labels, $policy_slug, $page_lang);
    ?>
      <div class="cia-policy-version" aria-label="<?php echo esc_attr($version_labels['label']); ?>">
        <dl>
          <div><dt><?php echo esc_html($version_labels['effective']); ?></dt><dd><time datetime="<?php echo esc_attr($policy_version['effective']); ?>"><?php echo esc_html($version_labels['effective_date']); ?></time></dd></div>
          <div><dt><?php echo esc_html($version_labels['updated']); ?></dt><dd><time datetime="<?php echo esc_attr($policy_version['updated']); ?>"><?php echo esc_html($version_labels['updated_date']); ?></time></dd></div>
          <div><dt><?php echo esc_html($version_labels['version']); ?></dt><dd><?php echo esc_html($policy_version['version']); ?></dd></div>
        </dl>
      </div>
    <?php endif; ?>
    <?php echo apply_filters('the_content', $page_content); ?>
  </div></article></div></section>
<?php endwhile; ?>
</div>
<?php get_footer(); ?>
