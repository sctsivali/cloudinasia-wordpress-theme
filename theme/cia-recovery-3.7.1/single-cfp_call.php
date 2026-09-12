<?php
if (!defined('ABSPATH')) { exit; }
$lang=function_exists('cia_home_language') ? cia_home_language() : ((isset($_GET['lang']) && sanitize_key(wp_unslash($_GET['lang']))==='en') ? 'en' : 'id');
$copy=$lang==='en' ? array(
    'kicker'=>'Conference opportunity','back'=>'Back to event','related'=>'Related conference','opens'=>'Opens','closes'=>'Closes',
    'planned'=>'Details being prepared','scheduled'=>'Scheduled','open'=>'Open','closed'=>'Closed',
    'proposal'=>'Call for Proposal','sponsor'=>'Call for Sponsor','volunteer'=>'Call for Volunteer',
    'proposal_cta'=>'Submit proposal','sponsor_cta'=>'Discuss sponsorship','volunteer_cta'=>'Apply as volunteer',
    'no_cta'=>'The verified submission or inquiry destination has not been published yet.','about'=>'About this call',
) : array(
    'kicker'=>'Kesempatan conference','back'=>'Kembali ke event','related'=>'Conference terkait','opens'=>'Dibuka','closes'=>'Ditutup',
    'planned'=>'Detail sedang disiapkan','scheduled'=>'Terjadwal','open'=>'Dibuka','closed'=>'Ditutup',
    'proposal'=>'Call for Proposal','sponsor'=>'Call for Sponsor','volunteer'=>'Call for Volunteer',
    'proposal_cta'=>'Kirim proposal','sponsor_cta'=>'Diskusikan sponsorship','volunteer_cta'=>'Daftar sebagai volunteer',
    'no_cta'=>'Kanal submission atau inquiry yang terverifikasi belum dipublikasikan.','about'=>'Tentang call ini',
);
get_header();
while(have_posts()):the_post();
$id=get_the_ID();$parent=(int)get_post_meta($id,'_cia_parent_event_id',true);
$type=(string)get_post_meta($id,'_cia_call_type',true);if(!in_array($type,array('proposal','sponsor','volunteer'),true)){$type='proposal';}
$state=function_exists('cia_event_call_state')?cia_event_call_state($id):'planned';
$title=function_exists('cia_event_call_localized')?cia_event_call_localized($id,'title',$lang):get_the_title();
$summary=function_exists('cia_event_call_localized')?cia_event_call_localized($id,'summary',$lang):get_the_excerpt();
$content=function_exists('cia_event_call_localized')?cia_event_call_localized($id,'content',$lang):get_the_content();
$open=(string)get_post_meta($id,'_cia_call_open',true);$close=(string)get_post_meta($id,'_cia_call_close',true);
$cta=esc_url((string)get_post_meta($id,'_cia_call_cta_url',true));
$format_date=static function($value){$dt=DateTimeImmutable::createFromFormat('!Y-m-d H:i:s',(string)$value,new DateTimeZone('Asia/Jakarta'));return $dt?$dt->format('d M Y · H:i').' WIB':'';};
?>
<article class="event-call-single">
  <header class="event-call-hero"><div class="container">
    <?php if($parent && get_post_type($parent)==='events'): ?><a class="event-back-link" href="<?php echo esc_url(function_exists('cia_localized_url')?cia_localized_url(get_permalink($parent),$lang):get_permalink($parent)); ?>">← <?php echo esc_html($copy['back']); ?></a><?php endif; ?>
    <div class="event-call-heading"><div><span class="media-kicker section-brow"><?php echo esc_html($copy['kicker'].' · '.$copy[$type]); ?></span><h1><?php echo esc_html($title); ?></h1><?php if($summary): ?><p><?php echo esc_html($summary); ?></p><?php endif; ?></div><span class="event-state event-state-<?php echo esc_attr($state); ?>"><?php echo esc_html($copy[$state]); ?></span></div>
  </div></header>
  <section class="event-call-main"><div class="container event-call-layout">
    <div class="event-call-body"><span class="media-kicker section-brow"><?php echo esc_html($copy['about']); ?></span><div class="entry-content"><?php echo wp_kses_post($content); ?></div></div>
    <aside class="event-call-panel"><dl>
      <div><dt><?php echo esc_html($copy['related']); ?></dt><dd><?php if($parent && get_post_type($parent)==='events'): ?><a href="<?php echo esc_url(function_exists('cia_localized_url')?cia_localized_url(get_permalink($parent),$lang):get_permalink($parent)); ?>"><?php echo esc_html(get_the_title($parent)); ?></a><?php else: ?>—<?php endif; ?></dd></div>
      <?php if($open): ?><div><dt><?php echo esc_html($copy['opens']); ?></dt><dd><?php echo esc_html($format_date($open)); ?></dd></div><?php endif; ?>
      <?php if($close): ?><div><dt><?php echo esc_html($copy['closes']); ?></dt><dd><?php echo esc_html($format_date($close)); ?></dd></div><?php endif; ?>
    </dl><?php if($cta): ?><a class="event-register-button" href="<?php echo esc_url($cta); ?>" rel="external noopener"><?php echo esc_html($copy[$type.'_cta']); ?> ↗</a><?php else: ?><p class="event-call-no-cta"><?php echo esc_html($copy['no_cta']); ?></p><?php endif; ?></aside>
  </div></section>
</article>
<?php endwhile;get_footer();
