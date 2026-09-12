<?php
$lang = (is_singular('post') && function_exists('cia_translation_locale')) ? cia_translation_locale(get_queried_object_id()) : cia_home_language();
$nav = $lang === 'en' ? array(
    'latest'=>'Latest','guide'=>'Guide','events'=>'Events','speakers'=>'Speakers','partners'=>'Collaborators','about'=>'About','editorial'=>'Editorial',
    'menu'=>'Menu','close'=>'Close','skip'=>'Skip to content','topics'=>'Topics','cloud'=>'Cloud','open'=>'Open Source',
    'infra'=>'Infrastructure','policy'=>'Policy','opinion'=>'Opinion','appearance'=>'Appearance','follow'=>'Follow Cloud in Asia',
    'search'=>'Search Cloud in Asia','search_short'=>'Search','language'=>'Language','system'=>'System','light'=>'Light','dark'=>'Dark'
) : array(
    'latest'=>'Terkini','guide'=>'Guide','events'=>'Events','speakers'=>'Speakers','partners'=>'Kolaborator','about'=>'Tentang','editorial'=>'Editorial',
    'menu'=>'Menu','close'=>'Tutup','skip'=>'Lompat ke konten','topics'=>'Topik','cloud'=>'Cloud','open'=>'Open Source',
    'infra'=>'Infrastruktur','policy'=>'Kebijakan','opinion'=>'Opini','appearance'=>'Tampilan','follow'=>'Media sosial Cloud in Asia',
    'search'=>'Cari di Cloud in Asia','search_short'=>'Cari','language'=>'Bahasa','system'=>'Sistem','light'=>'Terang','dark'=>'Gelap'
);
$primary_items = array(
    array('label'=>$nav['latest'],'url'=>cia_home_language_url($lang) . '#latest','external'=>false),
    array('label'=>$nav['guide'],'url'=>'https://guide.cloudin.asia/','external'=>true),
    array('label'=>$nav['events'],'url'=>cia_localized_url(cia_archive_link('events','/events/'), $lang),'external'=>false),
    array('label'=>$nav['speakers'],'url'=>cia_localized_url(home_url('/speakers/'), $lang),'external'=>false),
    array('label'=>$nav['partners'],'url'=>cia_localized_url(home_url('/partners/'), $lang),'external'=>false),
    array('label'=>$nav['about'],'url'=>cia_localized_url(home_url('/about/'), $lang),'external'=>false),
    array('label'=>$nav['editorial'],'url'=>cia_localized_url(home_url('/editorial-policy/'), $lang),'external'=>false),
);
$topic_items = array(
    array('label'=>$nav['cloud'],'url'=>cia_localized_url(home_url('/category/cloud-technology/'), $lang)),
    array('label'=>$nav['open'],'url'=>cia_localized_url(home_url('/category/open-source/'), $lang)),
    array('label'=>$nav['infra'],'url'=>cia_localized_url(home_url('/category/infrastruktur/'), $lang)),
    array('label'=>$nav['policy'],'url'=>cia_localized_url(home_url('/category/kebijakan/'), $lang)),
    array('label'=>$nav['opinion'],'url'=>cia_localized_url(home_url('/category/opini/'), $lang)),
);
$social_items = array(
    array('label'=>'Instagram','url'=>'https://www.instagram.com/cloudinasia/','path'=>'M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41-.56-.22-.96-.48-1.38-.9-.42-.42-.68-.82-.9-1.38-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16M12 0C8.74 0 8.33.01 7.05.07 5.78.13 4.9.33 4.14.63c-.79.31-1.46.72-2.12 1.39C1.35 2.68.94 3.35.63 4.14.33 4.9.13 5.78.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.06 1.27.26 2.15.56 2.91.31.79.72 1.46 1.39 2.12.66.67 1.33 1.08 2.12 1.39.76.3 1.64.5 2.91.56C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c1.27-.06 2.15-.26 2.91-.56.79-.31 1.46-.72 2.12-1.39.67-.66 1.08-1.33 1.39-2.12.3-.76.5-1.64.56-2.91.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.06-1.27-.26-2.15-.56-2.91-.31-.79-.72-1.46-1.39-2.12C21.32 1.35 20.65.94 19.86.63 19.1.33 18.22.13 16.95.07 15.67.01 15.26 0 12 0m0 5.84A6.16 6.16 0 1 0 18.16 12 6.16 6.16 0 0 0 12 5.84m0 10.16A4 4 0 1 1 16 12a4 4 0 0 1-4 4m7.85-10.4a1.44 1.44 0 1 1-1.44-1.44 1.44 1.44 0 0 1 1.44 1.44'),
    array('label'=>'Facebook','url'=>'https://www.facebook.com/cloudinasiacom','path'=>'M13.5 21.5v-8.2h2.76l.41-3.2H13.5V8.04c0-.93.26-1.56 1.59-1.56h1.7V3.61c-.3-.04-1.31-.13-2.5-.13-2.47 0-4.16 1.51-4.16 4.28v2.39H7.36v3.2h2.77v8.2z'),
    array('label'=>'LinkedIn','url'=>'https://www.linkedin.com/company/cloud-in-asia/','path'=>'M6.94 4.96a1.94 1.94 0 1 1-3.88 0 1.94 1.94 0 0 1 3.88 0M3.3 20.9h3.4V8.8H3.3zm6.13 0h3.4v-6.39c0-1.69.32-3.33 2.42-3.33 2.07 0 2.1 1.94 2.1 3.44v6.28h3.4v-6.96c0-2.95-.64-5.21-4.08-5.21-1.66 0-2.77.91-3.22 1.77h-.05V8.8H9.43z'),
    array('label'=>'WhatsApp','url'=>'https://www.whatsapp.com/channel/0029VbBv5Tq0LKZ7SVCy9F1K','path'=>'M17.47 14.38c-.3-.15-1.76-.87-2.03-.97-.27-.1-.47-.15-.67.15-.2.3-.77.97-.94 1.17-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.4-1.48-.89-.79-1.49-1.77-1.66-2.07-.17-.3-.02-.46.13-.61.14-.13.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.38-.02-.53-.08-.15-.68-1.61-.93-2.2-.24-.58-.48-.5-.66-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.02-1.04 2.48s1.06 2.88 1.21 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.62.71.23 1.36.19 1.87.12.57-.09 1.76-.72 2.01-1.41.25-.7.25-1.29.17-1.42-.07-.13-.27-.2-.57-.35M12.05 21.28h-.01a9.44 9.44 0 0 1-4.8-1.32l-.35-.2-3.57.93.96-3.48-.23-.36a9.4 9.4 0 0 1-1.44-5.03c0-5.2 4.24-9.44 9.45-9.44 2.52 0 4.89.99 6.67 2.77a9.38 9.38 0 0 1 2.76 6.68c0 5.2-4.24 9.45-9.44 9.45m8.04-17.49A11.36 11.36 0 0 0 12.05.28C5.8.28.72 5.36.72 11.61c0 2 .52 3.95 1.52 5.67L.62 23.28l6.14-1.61a11.33 11.33 0 0 0 5.29 1.35h.01c6.25 0 11.33-5.08 11.33-11.33 0-3.03-1.18-5.87-3.3-8.01'),
);
?>
<!doctype html>
<html lang="<?php echo esc_attr($lang); ?>">
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php echo esc_html($nav['skip']); ?></a>
<header class="site-header cia-chrome-header cia-chrome">
  <div class="cia-chrome-row cia-chrome-mobile-bar">
    <a href="<?php echo esc_url(cia_home_language_url($lang)); ?>" aria-label="Cloud in Asia home"><img class="cia-chrome-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-logo.png'); ?>" alt="Cloud in Asia" width="675" height="152"></a>
    <button class="cia-chrome-menu-open" id="cia-menu-open" type="button" aria-expanded="false" aria-controls="cia-mobile-drawer"><span><?php echo esc_html($nav['menu']); ?></span><svg viewBox="0 0 20 14" aria-hidden="true"><path d="M0 1h20M0 13h20"/></svg></button>
  </div>
  <div class="cia-chrome-row cia-chrome-row-brand">
    <a href="<?php echo esc_url(cia_home_language_url($lang)); ?>" aria-label="Cloud in Asia home"><img class="cia-chrome-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-logo.png'); ?>" alt="Cloud in Asia" width="675" height="152"></a>
    <div class="cia-chrome-utility">
      <nav class="cia-chrome-language" aria-label="<?php echo esc_attr($nav['language']); ?>"><a href="<?php echo esc_url(cia_switch_language_url('id')); ?>" lang="id"<?php if ($lang === 'id') echo ' aria-current="page"'; ?>>ID</a><span aria-hidden="true">/</span><a href="<?php echo esc_url(cia_switch_language_url('en')); ?>" lang="en"<?php if ($lang === 'en') echo ' aria-current="page"'; ?>>EN</a></nav>
      <details class="header-theme-menu cia-chrome-theme"><summary><?php echo esc_html($nav['appearance']); ?></summary><div class="header-theme-options" role="group" aria-label="<?php echo esc_attr($nav['appearance']); ?>"><button type="button" data-theme-mode="system" aria-pressed="true"><?php echo esc_html($nav['system']); ?></button><button type="button" data-theme-mode="light" aria-pressed="false"><?php echo esc_html($nav['light']); ?></button><button type="button" data-theme-mode="dark" aria-pressed="false"><?php echo esc_html($nav['dark']); ?></button></div></details>
    </div>
  </div>
  <div class="cia-chrome-row cia-chrome-row-nav">
    <nav aria-label="Primary navigation"><ul class="cia-chrome-primary-list"><?php foreach ($primary_items as $item): ?><li><a href="<?php echo esc_url($item['url']); ?>"<?php if ($item['external']) echo ' rel="external"'; ?>><?php echo esc_html($item['label']); ?></a></li><?php endforeach; ?></ul></nav>
    <form class="cia-chrome-search" role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get"><label class="screen-reader-text" for="cia-search-desktop"><?php echo esc_html($nav['search']); ?></label><svg viewBox="0 0 16 16" aria-hidden="true"><circle cx="7" cy="7" r="5.1"/><path d="M10.8 10.8L15 15"/></svg><input id="cia-search-desktop" type="search" name="s" placeholder="<?php echo esc_attr($nav['search_short']); ?>"><?php if ($lang === 'en'): ?><input type="hidden" name="lang" value="en"><?php endif; ?></form>
  </div>
  <div class="cia-chrome-row cia-chrome-row-topics">
    <nav aria-label="<?php echo esc_attr($nav['topics']); ?>"><ul class="cia-chrome-topic-list"><?php foreach ($topic_items as $item): ?><li><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a></li><?php endforeach; ?></ul></nav>
    <nav class="cia-chrome-socials" aria-label="<?php echo esc_attr($nav['follow']); ?>"><?php foreach ($social_items as $social): ?><a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?> Cloud in Asia"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?php echo esc_attr($social['path']); ?>"/></svg></a><?php endforeach; ?></nav>
  </div>
</header>
<div class="cia-chrome-mobile-drawer cia-chrome" id="cia-mobile-drawer" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr($nav['menu']); ?> Cloud in Asia" data-open="false" hidden>
  <div class="cia-chrome-drawer-top"><a href="<?php echo esc_url(cia_home_language_url($lang)); ?>"><img class="cia-chrome-logo" src="<?php echo esc_url(get_template_directory_uri() . '/assets/cia-logo.png'); ?>" alt="Cloud in Asia" width="675" height="152"></a><button id="cia-menu-close" type="button"><?php echo esc_html($nav['close']); ?> <span aria-hidden="true">×</span></button></div>
  <div class="cia-chrome-drawer-body">
    <form class="cia-chrome-search cia-chrome-drawer-search" role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get"><label class="screen-reader-text" for="cia-search-mobile"><?php echo esc_html($nav['search']); ?></label><svg viewBox="0 0 16 16" aria-hidden="true"><circle cx="7" cy="7" r="5.1"/><path d="M10.8 10.8L15 15"/></svg><input id="cia-search-mobile" type="search" name="s" placeholder="<?php echo esc_attr($nav['search']); ?>"><?php if ($lang === 'en'): ?><input type="hidden" name="lang" value="en"><?php endif; ?></form>
    <nav class="cia-chrome-drawer-primary" aria-label="Mobile navigation"><?php foreach ($primary_items as $item): ?><a href="<?php echo esc_url($item['url']); ?>"<?php if ($item['external']) echo ' rel="external"'; ?>><span><?php echo esc_html($item['label']); ?></span><span aria-hidden="true">↗</span></a><?php endforeach; ?></nav>
    <section class="cia-chrome-drawer-section"><h2><?php echo esc_html($nav['topics']); ?></h2><nav class="cia-chrome-drawer-topics" aria-label="<?php echo esc_attr($nav['topics']); ?>"><?php foreach ($topic_items as $item): ?><a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a><?php endforeach; ?></nav></section>
    <section class="cia-chrome-drawer-section mobile-theme-menu"><h2><?php echo esc_html($nav['appearance']); ?></h2><div class="header-theme-options" role="group" aria-label="<?php echo esc_attr($nav['appearance']); ?>"><button type="button" data-theme-mode="system" aria-pressed="true"><?php echo esc_html($nav['system']); ?></button><button type="button" data-theme-mode="light" aria-pressed="false"><?php echo esc_html($nav['light']); ?></button><button type="button" data-theme-mode="dark" aria-pressed="false"><?php echo esc_html($nav['dark']); ?></button></div></section>
    <section class="cia-chrome-drawer-section"><h2><?php echo esc_html($nav['language']); ?></h2><nav class="cia-chrome-drawer-language" aria-label="<?php echo esc_attr($nav['language']); ?>"><a href="<?php echo esc_url(cia_switch_language_url('id')); ?>" lang="id"<?php if ($lang === 'id') echo ' aria-current="page"'; ?>>Bahasa Indonesia</a><a href="<?php echo esc_url(cia_switch_language_url('en')); ?>" lang="en"<?php if ($lang === 'en') echo ' aria-current="page"'; ?>>English</a></nav></section>
    <nav class="cia-chrome-drawer-socials cia-chrome-socials" aria-label="<?php echo esc_attr($nav['follow']); ?>"><?php foreach ($social_items as $social): ?><a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?> Cloud in Asia"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?php echo esc_attr($social['path']); ?>"/></svg></a><?php endforeach; ?></nav>
  </div>
</div>
<main id="main-content">
