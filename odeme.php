<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/bootstrap.php';

header('Content-Type: text/html; charset=utf-8');
$api = 'api.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ödeme | Laykids</title>
  <style>
    :root { color-scheme: dark; --bg:#141416; --card:#1c1c20; --bd:#2e2e34; --t:#e8e8ea; --m:#9a9aa0; --a:#c9a962; }
    * { box-sizing: border-box; }
    body { margin:0; font-family: system-ui, sans-serif; background:var(--bg); color:var(--t); line-height:1.5; }
    .wrap { max-width: 720px; margin: 0 auto; padding: 1.5rem; }
    h1 { font-size: 1.35rem; margin: 0 0 0.5rem; }
    .lead { color: var(--m); margin: 0 0 1.25rem; font-size: 0.95rem; }
    .card { background: var(--card); border: 1px solid var(--bd); border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem; }
    label { display:block; font-size:0.82rem; color:var(--m); margin-bottom:0.25rem; }
    input, textarea, select { width:100%; padding:0.55rem 0.65rem; border-radius:8px; border:1px solid var(--bd); background:#0f0f12; color:var(--t); font-size:0.95rem; }
    .row { display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; }
    @media (max-width:560px){ .row { grid-template-columns:1fr; } }
    .hp { position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden; }
    .btn { display:inline-flex; align-items:center; justify-content:center; padding:0.65rem 1.2rem; border-radius:10px; border:none; background:#6b1c24; color:#fff; font-weight:600; cursor:pointer; text-decoration:none; font-size:0.95rem; }
    .btn:disabled { opacity:0.5; cursor:default; }
    .btn2 { background:transparent; border:1px solid var(--bd); color:var(--t); margin-left:0.5rem; }
    .err { color:#ffb4b4; font-size:0.88rem; margin-top:0.35rem; display:none; }
    .ok { color:#8fdc9a; font-size:0.88rem; }
    .sum { display:flex; justify-content:space-between; margin:0.35rem 0; font-size:0.92rem; }
    .sum strong { color: var(--a); }
    pre.note { white-space:pre-wrap; font-family:inherit; font-size:0.88rem; color:var(--m); margin:0; }
    .cart-mini { font-size:0.88rem; color:var(--m); }
    .gw-opt { display:flex; align-items:flex-start; gap:0.5rem; margin:0.4rem 0; cursor:pointer; }
    .gw-opt input { width:auto; margin-top:0.2rem; accent-color: var(--a); }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Ödeme ve teslimat</h1>
    <p class="lead">Bilgilerinizi girin, siparişi onaylayın. Sepet içeriğiniz tarayıcınızdaki sepetten alınır.</p>

    <div id="state-err" class="card" style="display:none; border-color:#5a2a2a">
      <strong>Ödeme şu an kullanılamıyor.</strong>
      <p class="cart-mini" id="state-err-msg">Mağaza veritabanında sipariş modülü kapalı olabilir.</p>
      <a class="btn" href="<?= ded_h(ded_vitrin_url('cart')) ?>">Sepete dön</a>
    </div>

    <form id="co-form" class="card" style="display:none">
      <input type="text" name="website" class="hp" tabindex="-1" autocomplete="off">

      <p id="cfg-intro" class="lead" style="display:none; margin-top:0"></p>

      <div id="demo-box" class="card" style="margin-top:0; background:#0f1a12; border-color:#2a4a32; display:none">
        <strong style="color:#8fdc9a">Demo ödeme modu</strong>
        <pre class="note" id="demo-text"></pre>
      </div>

      <div class="row">
        <div><label for="c_name">Ad soyad *</label><input id="c_name" name="name" required autocomplete="name"></div>
        <div><label for="c_email">E-posta *</label><input id="c_email" name="email" type="email" required autocomplete="email"></div>
      </div>
      <div style="margin-top:0.75rem">
        <label for="c_phone">Telefon (SMS bilgilendirme için)</label>
        <input id="c_phone" name="phone" autocomplete="tel">
      </div>
      <h2 style="font-size:1rem; margin:1.25rem 0 0.5rem">Teslimat</h2>
      <div style="margin-top:0.5rem">
        <label for="c_addr">Adres</label>
        <textarea id="c_addr" name="address_line" rows="3" autocomplete="street-address"></textarea>
      </div>
      <div class="row" style="margin-top:0.75rem">
        <div><label for="c_city">İl / ilçe</label><input id="c_city" name="city" autocomplete="address-level2"></div>
        <div><label for="c_country">Ülke</label><input id="c_country" name="country" value="TR" autocomplete="country-name"></div>
      </div>

      <h2 style="font-size:1rem; margin:1.25rem 0 0.5rem">Kupon</h2>
      <div class="row">
        <div>
          <label for="c_coupon">Kupon kodu</label>
          <input id="c_coupon" name="coupon" placeholder="Örn. YILBASI20">
        </div>
        <div style="display:flex; align-items:flex-end; gap:0.5rem">
          <button type="button" class="btn btn2" id="c_apply" style="margin:0">Uygula</button>
        </div>
      </div>
      <div id="coup-msg" class="ok" style="margin-top:0.35rem; min-height:1.25rem"></div>

      <h2 style="font-size:1rem; margin:1.25rem 0 0.5rem">Ödeme yöntemi</h2>
      <div id="gw-wrap" class="card" style="margin-top:0; display:none; background:#101015; border-color:#2a2a32">
        <div id="gw-list"></div>
      </div>

      <div id="paytr-info-box" class="card" style="margin-top:1rem; background:#0f1418; border-color:#243444; display:none">
        <strong style="color:var(--a)">Kart ile ödeme</strong>
        <pre class="note" id="paytr-info-text"></pre>
      </div>

      <div id="cod-box" class="card" style="margin-top:1rem; background:#141016; border-color:#34243a; display:none">
        <strong style="color:var(--a)">Kapıda ödeme</strong>
        <pre class="note" id="cod-text"></pre>
      </div>

      <div id="bank-box" class="card" style="margin-top:1rem; background:#16140f; border-color:#3a3424; display:none">
        <strong style="color:var(--a)">Ödeme bilgisi</strong>
        <pre class="note" id="bank-text"></pre>
      </div>

      <div style="margin-top:1rem">
        <div class="sum"><span>Ara toplam</span><span id="s-sub">—</span></div>
        <div class="sum"><span>İndirim</span><span id="s-disc">—</span></div>
        <div class="sum"><span>Kargo</span><span id="s-ship">—</span></div>
        <p id="ship-note" class="cart-mini" style="display:none; margin:0.25rem 0 0"></p>
        <div class="sum" style="font-size:1.05rem"><span>Genel toplam</span><strong id="s-tot">—</strong></div>
      </div>

      <div id="f-err" class="err"></div>
      <div style="margin-top:1rem; display:flex; flex-wrap:wrap; gap:0.5rem; align-items:center">
        <button type="submit" class="btn" id="c_submit">Siparişi tamamla</button>
        <a class="btn btn2" href="<?= ded_h(ded_vitrin_url('cart')) ?>">Sepete dön</a>
      </div>
      <p class="cart-mini" id="cart-empty" style="display:none">Sepet boş. <a href="<?= ded_h(ded_vitrin_url('collections')) ?>" style="color:var(--a)">Alışverişe devam</a></p>
    </form>

    <div id="paytr-step" class="wrap" style="display:none; padding-top:0">
      <div class="card">
        <h2 style="font-size:1.1rem; margin:0 0 0.5rem">Güvenli ödeme</h2>
        <p class="cart-mini" id="paytr-order-ref" style="margin-top:0"></p>
        <p class="cart-mini" id="paytr-err" style="display:none; color:#ffb4b4"></p>
        <div id="paytr-frame-host"></div>
      </div>
    </div>
  </div>
  <?php
  $_dedCkJs = __DIR__ . '/js/sepetcekirdek.js';
  $_dedCkVer = is_readable($_dedCkJs) ? ('?v=' . (string) filemtime($_dedCkJs)) : '';
  ?>
  <?= ded_vitrin_js_config_script() ?>
  <script src="js/vitrin-urls.js"></script>
  <script src="js/sepetcekirdek.js<?= htmlspecialchars($_dedCkVer, ENT_QUOTES, 'UTF-8') ?>"></script>
  <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
  <script>
(function(){
  'use strict';
  var api = <?php echo json_encode($api, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR); ?>;
  function u(path){ return api + '?path=' + encodeURIComponent(path); }
  function money(n, cur){
    cur = cur || 'TRY';
    if (cur === 'TRY') return (Number(n)||0).toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' ₺';
    return (Number(n)||0).toFixed(2) + ' ' + cur;
  }
  var GW_LABELS = {
    manual_transfer: 'Havale / EFT',
    cod: 'Kapıda ödeme',
    paytr: 'Kredi / banka kartı (PayTR)',
    shopify_redirect: 'Shopify / harici ödeme sayfasına git',
    demo_completed: 'Demo — sipariş anında “ödendi” işlenir'
  };
  var cfg = null;
  var lines = [];
  var discount = 0;
  var subtotal = 0;
  var shipFee = 0;
  var currency = 'TRY';

  function recalc(){
    subtotal = lines.reduce(function(s,l){
      var q = parseInt(l.qty,10)||1; var p = Number(l.price)||0; return s + p*q;
    },0);
    shipFee = cfg ? Number(cfg.default_shipping_fee)||0 : 0;
    currency = (cfg && cfg.currency) || 'TRY';
    var d = Math.min(discount, subtotal);
    var tot = Math.max(0, subtotal - d + shipFee);
    document.getElementById('s-sub').textContent = money(subtotal, currency);
    document.getElementById('s-disc').textContent = money(d, currency);
    document.getElementById('s-ship').textContent = money(shipFee, currency);
    document.getElementById('s-tot').textContent = money(tot, currency);
    return { subtotal: subtotal, discount: d, total: tot, ship: shipFee };
  }

  function selectedGateway(){
    var gw = (cfg && cfg.checkout_gateways) ? cfg.checkout_gateways : [];
    if (cfg && cfg.checkout_multi && gw.length > 1) {
      var sel = document.querySelector('#gw-list input[name="gateway_pick"]:checked');
      return sel ? sel.value : gw[0];
    }
    return gw[0] || (cfg && cfg.checkout_mode) || 'manual_transfer';
  }

  function updateGatewayBoxes(){
    var g = selectedGateway();
    function show(id, on, fn){
      var el = document.getElementById(id);
      if (!el) return;
      el.style.display = on ? 'block' : 'none';
      if (on && fn) fn();
    }
    show('demo-box', g === 'demo_completed', function(){
      document.getElementById('demo-text').textContent = cfg.payment_demo_notice || '';
    });
    show('bank-box', g === 'manual_transfer', function(){
      document.getElementById('bank-text').textContent = cfg.bank_instructions || '';
    });
    show('cod-box', g === 'cod', function(){
      document.getElementById('cod-text').textContent = cfg.cod_instructions || '';
    });
    var pb = document.getElementById('paytr-info-box');
    if (pb) {
      pb.style.display = g === 'paytr' ? 'block' : 'none';
      var tx = document.getElementById('paytr-info-text');
      if (tx) tx.textContent = cfg.paytr_checkout_note || '';
    }
  }

  function setupGateways(){
    var gw = (cfg.checkout_gateways) ? cfg.checkout_gateways : [];
    var wrap = document.getElementById('gw-wrap');
    var host = document.getElementById('gw-list');
    if (!wrap || !host) return;
    if (cfg.checkout_multi && gw.length > 1) {
      wrap.style.display = 'block';
      host.innerHTML = '';
      gw.forEach(function(g, i){
        var id = 'gw_' + g.replace(/[^a-z0-9_]/gi, '_');
        var lab = document.createElement('label');
        lab.className = 'gw-opt';
        lab.setAttribute('for', id);
        var inp = document.createElement('input');
        inp.type = 'radio';
        inp.name = 'gateway_pick';
        inp.value = g;
        inp.id = id;
        if (i === 0) inp.checked = true;
        var sp = document.createElement('span');
        sp.textContent = GW_LABELS[g] || g;
        lab.appendChild(inp);
        lab.appendChild(sp);
        host.appendChild(lab);
        inp.addEventListener('change', updateGatewayBoxes);
      });
    } else {
      wrap.style.display = 'none';
    }
    updateGatewayBoxes();
  }

  fetch(u('public-shop-config'), { headers: { 'Accept':'application/json' } })
    .then(function(r){ return r.json(); })
    .then(function(j){
      if (!j.ok || !j.config) throw new Error(j.error || 'config');
      cfg = j.config;
      var gw = cfg.checkout_gateways || [];
      if (!gw.length || cfg.checkout_mode === 'disabled') throw new Error('disabled');
      document.getElementById('co-form').style.display = 'block';
      var introEl = document.getElementById('cfg-intro');
      if (cfg.checkout_intro) {
        introEl.style.display = 'block';
        introEl.textContent = cfg.checkout_intro;
      }
      setupGateways();
      if (cfg.shipping_note) {
        var sn = document.getElementById('ship-note');
        sn.style.display = 'block';
        sn.textContent = cfg.shipping_note;
      }
      lines = (window.DEDCart && DEDCart.get) ? DEDCart.get() : [];
      if (!lines.length) {
        document.getElementById('cart-empty').style.display = 'block';
        document.getElementById('c_submit').disabled = true;
      }
      recalc();
    })
    .catch(function(){
      document.getElementById('state-err').style.display = 'block';
    });

  document.getElementById('c_apply').addEventListener('click', function(){
    var code = (document.getElementById('c_coupon').value || '').trim();
    var msg = document.getElementById('coup-msg');
    msg.textContent = '';
    discount = 0;
    if (!code) { recalc(); return; }
    fetch(u('public-validate-coupon'), {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json' },
      body: JSON.stringify({ code: code, subtotal: subtotal })
    }).then(function(r){ return r.json().then(function(j){ return { ok: r.ok, j: j }; }); })
      .then(function(x){
        if (!x.j.ok) { msg.style.color = '#ffb4b4'; msg.textContent = x.j.message || 'Geçersiz kupon'; return; }
        discount = Number(x.j.discount)||0;
        msg.style.color = '#8fdc9a';
        msg.textContent = x.j.message || 'İndirim uygulandı';
        recalc();
      }).catch(function(){ msg.style.color = '#ffb4b4'; msg.textContent = 'Bağlantı hatası'; });
  });

  function showPaytrStep(token, orderNum, paytrErr){
    document.getElementById('co-form').style.display = 'none';
    document.getElementById('state-err').style.display = 'none';
    var step = document.getElementById('paytr-step');
    step.style.display = 'block';
    document.getElementById('paytr-order-ref').textContent = 'Sipariş no: ' + orderNum;
    var pe = document.getElementById('paytr-err');
    if (paytrErr) {
      pe.style.display = 'block';
      pe.textContent = paytrErr;
    } else {
      pe.style.display = 'none';
    }
    var host = document.getElementById('paytr-frame-host');
    host.innerHTML = '';
    if (!token) return;
    var ifr = document.createElement('iframe');
    ifr.src = 'https://www.paytr.com/odeme/guvenli/' + token;
    ifr.id = 'paytriframe';
    ifr.setAttribute('frameborder', '0');
    ifr.setAttribute('scrolling', 'no');
    ifr.style.width = '100%';
    ifr.style.minHeight = '560px';
    host.appendChild(ifr);
    if (window.iFrameResize) {
      try { window.iFrameResize({}, '#paytriframe'); } catch (e) {}
    }
  }

  document.getElementById('co-form').addEventListener('submit', function(ev){
    ev.preventDefault();
    var err = document.getElementById('f-err');
    err.style.display = 'none';
    lines = (window.DEDCart && DEDCart.get) ? DEDCart.get() : [];
    if (!lines.length) { err.textContent = 'Sepet boş.'; err.style.display='block'; return; }
    var fd = new FormData(ev.target);
    var coupon = (document.getElementById('c_coupon').value || '').trim();
    var payload = {
      website: (fd.get('website')||'')+'',
      gateway: selectedGateway(),
      customer: {
        name: (fd.get('name')||'')+'',
        email: (fd.get('email')||'')+'',
        phone: (fd.get('phone')||'')+''
      },
      shipping: {
        address_line: (fd.get('address_line')||'')+'',
        city: (fd.get('city')||'')+'',
        country: (fd.get('country')||'')+''
      },
      lines: lines.map(function(l){
        return { slug:l.slug, title:l.title, price:l.price, qty:l.qty, variant:l.variant||'', image:l.image||'' };
      }),
      coupon_code: coupon || null
    };
    var btn = document.getElementById('c_submit');
    btn.disabled = true;
    fetch(u('public-create-order'), {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'Accept':'application/json' },
      body: JSON.stringify(payload)
    }).then(function(r){ return r.json().then(function(j){ return { ok: r.ok, j:j, st:r.status }; }); })
      .then(function(x){
        if (!x.ok || !x.j.ok) {
          err.textContent = (x.j && x.j.error) ? ('Hata: ' + x.j.error) : 'Sipariş oluşturulamadı';
          err.style.display = 'block';
          btn.disabled = false;
          return;
        }
        var num = x.j.order && x.j.order.order_number ? x.j.order.order_number : '';
        try { if (window.DEDCart && DEDCart.clear) DEDCart.clear(); } catch(e){}
        if (x.j.paytr && x.j.paytr.iframe_token) {
          showPaytrStep(x.j.paytr.iframe_token, num, x.j.paytr_error || '');
          return;
        }
        if (x.j.paytr_error) {
          showPaytrStep('', num, x.j.paytr_error);
          return;
        }
        if (x.j.redirect_url) {
          window.location.href = x.j.redirect_url;
          return;
        }
        window.location.href = window.dedVitrinUrl ? window.dedVitrinUrl('orderSuccess', num) : 'siparis-tamam?num=' + encodeURIComponent(num);
      }).catch(function(){
        err.textContent = 'Bağlantı hatası';
        err.style.display = 'block';
        btn.disabled = false;
      });
  });
})();
  </script>
</body>
</html>
