<?php

declare(strict_types=1);

require_once __DIR__ . '/magazadepo.php';

function ded_vitrin_head_injections(): string
{
    $pdo = ded_pdo();
    if (!$pdo || !ded_shop_ready($pdo)) {
        return '';
    }
    $s = ded_shop_settings_get($pdo);
    $out = '';
    $verify = trim((string) ($s['google_site_verification'] ?? ''));
    if ($verify !== '') {
        $out .= '<meta name="google-site-verification" content="' . ded_attr($verify) . '">' . "\n";
    }
    $ga = trim((string) ($s['ga4_measurement_id'] ?? ''));
    if ($ga !== '' && preg_match('/^G-[A-Z0-9]+$/i', $ga)) {
        $out .= '<script async src="https://www.googletagmanager.com/gtag/js?id=' . ded_attr($ga) . '"></script>';
        $out .= '<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag("js",new Date());gtag("config","' . ded_attr($ga) . '");</script>' . "\n";
    }
    $pixel = trim((string) ($s['meta_pixel_id'] ?? ''));
    if ($pixel !== '' && preg_match('/^\d+$/', $pixel)) {
        $out .= '<script>!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};';
        $out .= 'if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";n.queue=[];t=b.createElement(e);t.async=!0;';
        $out .= 't.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,"script","https://connect.facebook.net/en_US/fbevents.js");';
        $out .= 'fbq("init","' . ded_attr($pixel) . '");fbq("track","PageView");</script>' . "\n";
    }

    return $out;
}

function ded_vitrin_newsletter_api_url(): string
{
    $base = document_base_href_for_vitrin();

    return $base . 'bulten.php';
}

function document_base_href_for_vitrin(): string
{
    require_once __DIR__ . '/vitrinrotalar.php';
    $b = ded_vitrin_base_href();
    if ($b !== '' && $b !== '/') {
        return $b;
    }

    return rtrim(ded_storefront_public_url(), '/') . '/';
}

function ded_vitrin_footer_scripts(): string
{
    $api = ded_vitrin_newsletter_api_url();
    $js = <<<'JS'
<script>
(function(){
  var api = %API%;
  function apiUrl() {
    try {
      var b = document.querySelector('base');
      if (b && b.href) return new URL('bulten.php', b.href).href;
    } catch (e) {}
    return api;
  }
  function hookForm(form){
    if (!form || form.dataset.dedNlHook) return;
    form.dataset.dedNlHook = '1';
    form.addEventListener('submit', function(ev){
      ev.preventDefault();
      var emailInp = form.querySelector('input[type=email]')
        || form.querySelector('input[name="contact[email]"]')
        || form.querySelector('input[name*="email"]');
      var email = emailInp ? String(emailInp.value || '').trim() : '';
      if (!email) return;
      var btn = form.querySelector('button[type=submit],button');
      if (btn) btn.disabled = true;
      fetch(apiUrl(), {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
        body: JSON.stringify({email: email})
      }).then(function(r){
        return r.text().then(function(t){
          try { return JSON.parse(t); } catch (e) { throw new Error('invalid'); }
        });
      }).then(function(j){
        alert(j.message || (j.ok ? 'Kayıt alındı.' : 'Hata'));
        if (j.ok && emailInp) emailInp.value = '';
      }).catch(function(){
        alert('Kayıt gönderilemedi.');
      }).finally(function(){ if (btn) btn.disabled = false; });
    });
  }
  document.querySelectorAll('#footer-newsletter, form.footer__newsletter-form').forEach(hookForm);
})();
</script>
JS;

    return str_replace('%API%', json_encode($api, JSON_UNESCAPED_UNICODE), $js);
}

function ded_vitrin_apply_extras(string $html): string
{
    $head = ded_vitrin_head_injections();
    if ($head !== '') {
        $html = preg_replace('/<\/head>/i', $head . '</head>', $html, 1) ?? $html;
    }
    $html = preg_replace('/<\/body>/i', ded_vitrin_footer_scripts() . '</body>', $html, 1) ?? $html;

    return $html;
}
