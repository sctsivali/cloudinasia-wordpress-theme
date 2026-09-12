<?php
if (!defined('ABSPATH')) { exit; }
get_header();
$lang = cia_home_language();
$copy = $lang === 'en' ? array(
    'kicker'=>'Collaborators','title'=>'Collaboration is infrastructure.','intro'=>'Cloud in Asia treats collaboration as the connective tissue between journalism, technical communities, education, public institutions, and industry. This directory records organizations attached to our published event data—not a claim of endorsement, commercial status, or an active partnership beyond that evidence.','principle_title'=>'Why this page exists','principle_body'=>'Technology ecosystems become more resilient when knowledge can move between practitioners, institutions, communities, and decision-makers. We make those connections visible so readers can understand who helped shape a conversation and continue exploring the event record.','directory'=>'Collaboration directory','directory_intro'=>'Grouped by the role recorded in Cloud in Asia event taxonomy. A listing reflects a mention or event record, not a claim that a relationship remains active.','sponsors'=>'Sponsors','community'=>'Community supporters','education'=>'Education partners','events'=>'Related events','website'=>'Official website','event_singular'=>'event','event_plural'=>'events','open'=>'Open invitation','invite_title'=>'Bring a useful conversation into the open.','invite_body'=>'We welcome proposals for webinars, meetups, educational programs, technical discussions, and evidence-led editorial collaboration. Tell us what the audience should learn, who should be involved, and why it matters.','email'=>'Email the editorial team','disclosure'=>'Relationship disclosure','fallback'=>'No verified first-party logo asset was available; a neutral monogram is shown instead.'
) : array(
    'kicker'=>'Kolaborator','title'=>'Kolaborasi adalah infrastruktur.','intro'=>'Cloud in Asia memandang kolaborasi sebagai jaringan penghubung antara jurnalisme, komunitas teknis, pendidikan, institusi publik, dan industri. Direktori ini mencatat organisasi yang terikat pada data event terpublikasi—bukan klaim endorsement, status komersial, atau hubungan aktif di luar bukti tersebut.','principle_title'=>'Mengapa halaman ini ada','principle_body'=>'Ekosistem teknologi menjadi lebih tangguh ketika pengetahuan dapat bergerak di antara praktisi, institusi, komunitas, dan pengambil keputusan. Kami membuat hubungan itu terlihat agar pembaca memahami siapa yang membantu membentuk percakapan dan dapat menelusuri rekam event terkait.','directory'=>'Direktori kolaborasi','directory_intro'=>'Dikelompokkan berdasarkan peran yang tercatat dalam taxonomy event Cloud in Asia. Setiap listing mencerminkan catatan event atau penyebutan, bukan klaim bahwa suatu hubungan masih aktif.','sponsors'=>'Sponsor','community'=>'Pendukung komunitas','education'=>'Mitra pendidikan','events'=>'Event terkait','website'=>'Situs resmi','event_singular'=>'event','event_plural'=>'event','open'=>'Undangan terbuka','invite_title'=>'Bawa percakapan yang berguna ke ruang terbuka.','invite_body'=>'Kami terbuka untuk usulan webinar, meetup, program pendidikan, diskusi teknis, dan kolaborasi editorial berbasis bukti. Ceritakan apa yang perlu dipelajari audiens, siapa yang perlu terlibat, dan mengapa hal itu penting.','email'=>'Kirim email ke redaksi','disclosure'=>'Keterangan hubungan','fallback'=>'Aset logo first-party terverifikasi tidak tersedia; sebagai gantinya ditampilkan monogram netral.'
);
$groups = array(
    'sponsors'=>array('label'=>$copy['sponsors'],'class'=>'sponsors'),
    'community_support'=>array('label'=>$copy['community'],'class'=>'community'),
    'education-partner'=>array('label'=>$copy['education'],'class'=>'education'),
);
$catalog_path = get_template_directory() . '/assets/partner-logos/catalog.json';
$catalog_rows = is_file($catalog_path) ? json_decode((string) file_get_contents($catalog_path), true) : array();
$logo_files = array();
foreach (is_array($catalog_rows) ? $catalog_rows : array() as $row) {
    if (!empty($row['slug']) && !empty($row['file'])) { $logo_files[$row['slug']] = $row['file']; }
}
$logo_aliases = array('kementrian-komunikasi-dan-digital'=>'komdigi');
$dark_logos = array('amd','kementrian-komunikasi-dan-digital','sekolah-vokasi-universitas-gajah-mada','sivali-cloud-technology');
$display_names = array('kementrian-komunikasi-dan-digital'=>'Kementerian Komunikasi dan Digital');
$official_urls = array(
    'sivali-cloud-technology'=>'https://sivali.co/','amd'=>'https://www.amd.com/','kementrian-komunikasi-dan-digital'=>'https://portal.komdigi.go.id/','xecureit'=>'https://xecureit.id/','sahabat-ubuntu-indonesia'=>'https://www.linkedin.com/company/sahabat-ubuntu-indonesia/','ubuntu-indonesia'=>'https://ubuntu-id.org/','institut-teknologi-tangerang-selatan'=>'https://itts.ac.id/','onno-center-international'=>'http://onnocenter.or.id/','sekolah-vokasi-universitas-gajah-mada'=>'https://ugm.ac.id/id/fakultas/sekolah-vokasi/','universitas-kebangsaan-republik-indonesia'=>'https://ukri.ac.id/','universitas-jenderal-achmad-yani-yogyakarta'=>'https://unjaya.ac.id/','universitas-lia'=>'https://www.universitaslia.ac.id/','telkom-university'=>'https://telkomuniversity.ac.id/','ibi-kosgoro'=>'https://www.ibi-k57.ac.id/'
);
?>
<main class="editorial-surface partners-directory">
  <header class="partners-hero">
    <div class="partners-hero-inner"><p class="media-kicker section-brow"><?php echo esc_html($copy['kicker']); ?></p><h1><?php echo esc_html($copy['title']); ?></h1><p class="partners-hero-deck"><?php echo esc_html($copy['intro']); ?></p></div>
  </header>
  <section class="partners-principle" aria-labelledby="partners-principle-title">
    <p class="media-kicker section-brow">01 / <?php echo esc_html($copy['principle_title']); ?></p>
    <div><h2 id="partners-principle-title"><?php echo esc_html($copy['principle_title']); ?></h2><p><?php echo esc_html($copy['principle_body']); ?></p></div>
  </section>
  <section class="partner-directory" aria-labelledby="partner-directory-title">
    <header class="partner-directory-heading"><p class="media-kicker section-brow">02 / <?php echo esc_html($copy['directory']); ?></p><h2 id="partner-directory-title"><?php echo esc_html($copy['directory']); ?></h2><p><?php echo esc_html($copy['directory_intro']); ?></p></header>
    <div class="partner-directory-list">
    <?php foreach ($groups as $taxonomy=>$group) :
        $terms = get_terms(array('taxonomy'=>$taxonomy,'hide_empty'=>true,'orderby'=>'name','order'=>'ASC'));
        if (is_wp_error($terms) || !$terms) { continue; }
    ?>
      <section class="partner-group partner-group-<?php echo esc_attr($group['class']); ?>" aria-labelledby="partner-group-<?php echo esc_attr($group['class']); ?>">
        <header class="partner-group-heading"><h3 id="partner-group-<?php echo esc_attr($group['class']); ?>"><?php echo esc_html($group['label']); ?></h3><span><?php echo esc_html(count($terms)); ?></span></header>
        <div class="partner-group-rows">
        <?php foreach ($terms as $term) :
            $display_name = $display_names[$term->slug] ?? $term->name;
            $logo_key = $logo_aliases[$term->slug] ?? $term->slug;
            $logo_file = $logo_files[$logo_key] ?? '';
            $event_url = add_query_arg('event_collaborator', $taxonomy . ':' . $term->slug, cia_localized_url(get_post_type_archive_link('events'), $lang));
            $description = trim(wp_strip_all_tags((string) $term->description));
            $description = trim((string) preg_replace('~https?://\S+~', '', $description));
            if (preg_match('/^(?:Website|Informasi)\s+resmi\s*:?\s*$/iu', $description)) { $description = ''; }
            $initials = '';
            foreach (preg_split('/\s+/', $display_name) as $word) { if ($word !== '') { $initials .= function_exists('mb_substr') ? mb_substr($word, 0, 1) : substr($word, 0, 1); } if (strlen($initials) >= 3) { break; } }
        ?>
          <article id="partner-<?php echo esc_attr($taxonomy . '-' . $term->slug); ?>" class="partner-row">
            <div class="partner-logo-plate <?php echo in_array($term->slug, $dark_logos, true) ? 'is-dark' : ''; ?>">
              <?php if ($logo_file) : ?><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/partner-logos/' . $logo_file); ?>" alt="<?php echo esc_attr($display_name . ' logo'); ?>" loading="lazy" decoding="async">
              <?php else : ?><span class="partner-monogram" aria-hidden="true"><?php echo esc_html(strtoupper($initials)); ?></span><span class="screen-reader-text"><?php echo esc_html($copy['fallback']); ?></span><?php endif; ?>
            </div>
            <div class="partner-row-content"><p class="partner-role"><?php echo esc_html($group['label']); ?></p><h4><?php echo esc_html($display_name); ?></h4><?php if ($description) : ?><p class="partner-description"><?php echo esc_html($description); ?></p><?php endif; ?><p class="partner-record"><strong><?php echo esc_html($term->count); ?></strong> <?php echo esc_html((int) $term->count === 1 ? $copy['event_singular'] : $copy['event_plural']); ?></p></div>
            <nav class="partner-row-links" aria-label="<?php echo esc_attr($display_name); ?>"><a href="<?php echo esc_url($event_url); ?>"><?php echo esc_html($copy['events']); ?> <span aria-hidden="true">→</span></a><?php if (!empty($official_urls[$term->slug])) : ?><a href="<?php echo esc_url($official_urls[$term->slug]); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html($copy['website']); ?> <span aria-hidden="true">↗</span></a><?php endif; ?></nav>
          </article>
        <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
    </div>
    <aside class="partner-disclosure"><strong><?php echo esc_html($copy['disclosure']); ?></strong><p><?php echo esc_html($copy['directory_intro']); ?></p></aside>
  </section>
  <section class="partner-invitation" aria-labelledby="partner-invitation-title">
    <div><p class="media-kicker section-brow">03 / <?php echo esc_html($copy['open']); ?></p><h2 id="partner-invitation-title"><?php echo esc_html($copy['invite_title']); ?></h2></div>
    <div><p><?php echo esc_html($copy['invite_body']); ?></p><a class="partner-email-cta" href="mailto:redaksi@cloudinasia.com?subject=<?php echo rawurlencode($lang === 'en' ? 'Collaboration proposal for Cloud in Asia' : 'Usulan kolaborasi untuk Cloud in Asia'); ?>"><?php echo esc_html($copy['email']); ?> <span>redaksi@cloudinasia.com</span></a></div>
  </section>
</main>
<?php get_footer();
