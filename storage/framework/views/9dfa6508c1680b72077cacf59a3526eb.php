<?php
    $gtmId = config('services.google.gtm_container_id', env('GTM_CONTAINER_ID'));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($gtmId): ?>

<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo e($gtmId); ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\components\analytics-noscript.blade.php ENDPATH**/ ?>