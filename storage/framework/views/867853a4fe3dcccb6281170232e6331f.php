<?php if (isset($component)) { $__componentOriginalcf324bfcd6feee0107a23f728e62ecac = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcf324bfcd6feee0107a23f728e62ecac = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.account-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('account-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="max-w-2xl">
        <h1 class="text-3xl font-black text-gray-900 mb-8">Profil Ayarları</h1>
        
        <form wire:submit="updateProfile" class="mb-16">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Kişisel Bilgiler</h2>
            <div class="space-y-6 bg-gray-50/50 p-8 rounded-3xl border border-gray-100">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Ad Soyad</label>
                    <input wire:model="name" type="text" id="name" class="w-full bg-white border <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs font-bold mt-2 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">E-posta Adresi</label>
                    <input wire:model="email" type="email" id="email" class="w-full bg-white border <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs font-bold mt-2 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="pt-2">
                    <button type="submit" class="bg-black text-white font-bold text-sm px-8 py-4 rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updateProfile">Değişiklikleri Kaydet</span>
                        <span wire:loading wire:target="updateProfile">Kaydediliyor...</span>
                    </button>
                </div>
            </div>
        </form>

        <form wire:submit="updatePassword">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Şifre Değiştir</h2>
            <div class="space-y-6 bg-gray-50/50 p-8 rounded-3xl border border-gray-100">
                <div>
                    <label for="current_password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Mevcut Şifre</label>
                    <input wire:model="current_password" type="password" id="current_password" class="w-full bg-white border <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs font-bold mt-2 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="new_password" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Yeni Şifre</label>
                    <input wire:model="new_password" type="password" id="new_password" class="w-full bg-white border <?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-300 <?php else: ?> border-gray-200 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['new_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs font-bold mt-2 block"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Yeni Şifre (Tekrar)</label>
                    <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation" class="w-full bg-white border border-gray-200 rounded-xl px-5 py-4 text-gray-900 focus:outline-none focus:ring-2 focus:ring-black focus:border-transparent transition-shadow shadow-sm">
                </div>
                <div class="pt-2">
                    <button type="submit" class="bg-black text-white font-bold text-sm px-8 py-4 rounded-xl hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center justify-center min-w-[160px]">
                        <span wire:loading.remove wire:target="updatePassword">Şifreyi Güncelle</span>
                        <span wire:loading wire:target="updatePassword">Güncelleniyor...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcf324bfcd6feee0107a23f728e62ecac)): ?>
<?php $attributes = $__attributesOriginalcf324bfcd6feee0107a23f728e62ecac; ?>
<?php unset($__attributesOriginalcf324bfcd6feee0107a23f728e62ecac); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcf324bfcd6feee0107a23f728e62ecac)): ?>
<?php $component = $__componentOriginalcf324bfcd6feee0107a23f728e62ecac; ?>
<?php unset($__componentOriginalcf324bfcd6feee0107a23f728e62ecac); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Lenovo\Desktop\Projelerim\patenliayakkabilar.com\resources\views\livewire\account\profile.blade.php ENDPATH**/ ?>