<?php declare(strict_types=1); ?>
    </div>
    <footer class="footer text-center text-sm-start d-print-none">
      <div class="container-xxl">
        <div class="row">
          <div class="col-12">
            <div class="card mb-0 border-0 shadow-sm">
              <div class="card-body py-2">
                <p class="text-muted mb-0 fs-13"><?= ded_h(yonetim_brand_footer_line()) ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</div>

<script src="<?= ded_h(yonetim_rizz_asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= ded_h(yonetim_rizz_asset('assets/libs/simplebar/simplebar.min.js')) ?>"></script>
<script src="<?= ded_h(yonetim_rizz_asset('assets/js/app.js')) ?>"></script>
<script src="<?= ded_h(yonetim_panel_asset('tema.js?v=2')) ?>"></script>
<?= $GLOBALS['yonetim_layout_scripts'] ?? '' ?>
</body>
</html>
