<?php
$lang = cia_home_language();
$copy = $lang === 'en' ? array(
  'kicker'=>'People directory','title'=>'People shaping the technology conversation','intro'=>'Event speakers and statement sources explicitly recorded in Cloud in Asia publications.','profiles'=>'verified profiles','metrics'=>'separate metrics','statement'=>'Statement source','speaker'=>'Speaker','portrait'=>'Portrait','portrait_missing'=>'Portrait is not yet available for','mentions'=>'publications mention this name','appearances'=>'speaker appearances','events'=>'Explore events'
) : array(
  'kicker'=>'Direktori orang','title'=>'Orang-orang yang membentuk percakapan teknologi','intro'=>'Pembicara event dan sumber pernyataan editorial yang tercatat secara eksplisit dalam publikasi Cloud in Asia.','profiles'=>'profil terverifikasi','metrics'=>'metrik yang dipisahkan','statement'=>'Sumber pernyataan','speaker'=>'Pembicara','portrait'=>'Potret','portrait_missing'=>'Foto belum tersedia untuk','mentions'=>'publikasi menyebut nama','appearances'=>'penampilan pembicara','events'=>'Jelajahi event'
);
$data = cia_people_directory_data();
get_header();
?>
<div class="editorial-surface editorial-page people-directory">
  <header class="editorial-page-hero people-directory-hero"><div class="container"><span class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></span><h1><?php echo esc_html($copy['title']); ?></h1><p class="editorial-deck"><?php echo esc_html($copy['intro']); ?></p><div class="people-directory-summary"><strong><?php echo esc_html($data['records_total'] ?? count($data['records'])); ?></strong><span><?php echo esc_html($copy['profiles']); ?></span><strong>2</strong><span><?php echo esc_html($copy['metrics']); ?></span></div><a class="home-news-all" href="<?php echo esc_url(cia_localized_url(cia_archive_link('events','/events/'), $lang)); ?>"><?php echo esc_html($copy['events']); ?> <span aria-hidden="true">→</span></a></div></header>
  <section class="people-directory-main"><div class="container"><div class="people-directory-grid">
  <?php foreach ($data['records'] as $index=>$person):
      $person_id=(int)$person['person_id'];
      $term_link=$person_id > 0 ? get_term_link($person_id,'speakers') : '';
      $article_ids=array_map('intval',$person['article_mention_record_ids'] ?? array());
      $href=(!is_wp_error($term_link) && $term_link) ? $term_link : ($article_ids ? get_permalink($article_ids[0]) : '#person-' . abs($person_id));
      $parts=preg_split('/\s+/u',trim($person['name'])); $initials=''; foreach(array_slice($parts,0,2) as $part){ $initials.=mb_strtoupper(mb_substr($part,0,1)); }
  ?>
    <article class="people-card" id="person-<?php echo esc_attr(abs($person_id)); ?>">
      <a class="people-portrait" href="<?php echo esc_url($href); ?>"><?php if ($person['portrait']): ?><img src="<?php echo esc_url(get_template_directory_uri() . '/' . ltrim($person['portrait']['theme_path'],'/')); ?>" alt="<?php echo esc_attr($copy['portrait'] . ' ' . $person['name']); ?>" width="400" height="400" loading="lazy"><?php else: ?><span aria-label="<?php echo esc_attr($copy['portrait_missing'] . ' ' . $person['name']); ?>"><?php echo esc_html($initials ?: 'CIA'); ?></span><?php endif; ?></a>
      <div class="people-copy"><p class="profile-type"><?php echo esc_html($person['profile_type']==='statement_source' ? $copy['statement'] : $copy['speaker']); ?></p><h2><a href="<?php echo esc_url($href); ?>"><?php echo esc_html($person['name']); ?></a></h2><?php foreach(array($person['alias_note'] ?? '',$person['affiliation_note'] ?? '') as $note): if($note): ?><p class="speaker-note"><?php echo esc_html($note); ?></p><?php endif; endforeach; ?><p class="people-metrics"><span><strong><?php echo esc_html($person['article_mention_records']); ?></strong> <?php echo esc_html($copy['mentions']); ?></span><span><strong><?php echo esc_html($person['speaker_appearances']); ?></strong> <?php echo esc_html($copy['appearances']); ?></span></p></div>
    </article>
  <?php endforeach; ?>
  </div></div></section>
</div>
<?php get_footer(); ?>
