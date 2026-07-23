<?php $__env->startSection('title', 'Login | SIBERKAS'); ?>

<?php $__env->startSection('content'); ?>
<script src="https://unpkg.com/lottie-web@5.12.2/build/player/lottie.min.js"></script>

<div class="min-h-screen lg:grid lg:grid-cols-2">
    <section class="siberkas-pattern-shell bg-animated relative hidden overflow-hidden text-white lg:flex lg:items-center lg:justify-center">
        <div class="siberkas-pattern" aria-hidden="true"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,rgba(201,120,89,.18),transparent_38%),radial-gradient(circle_at_bottom_left,rgba(74,146,130,.22),transparent_35%)]"></div>
        <div class="relative z-10 w-full max-w-xl px-10 py-12 text-center fade-slide">
            <p class="mb-3 inline-flex rounded-full border border-white/15 bg-white/5 px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.22em] text-emerald-50/90">Digital Document Hub</p>
            <h1 class="mb-3 text-4xl font-black tracking-[0.18em]">SIBERKAS</h1>
            <p class="mx-auto max-w-lg text-sm leading-6 text-emerald-50/85">Sistem Informasi Berkas Terintegrasi untuk mengelola data nasabah, dokumen, dan administrasi secara digital, cepat, rapi, aman, serta sesuai peran pengguna.</p>
            <div id="lottie-box" class="mx-auto my-4 h-72 w-72"></div>
            <div class="feature-list mx-auto max-w-md">
                <div class="feature-item"><span class="feature-icon">📁</span><div>Manajemen Data Nasabah</div></div>
                <div class="feature-item"><span class="feature-icon">🗂</span><div>Tracking Status Berkas</div></div>
                <div class="feature-item"><span class="feature-icon">💾</span><div>Arsip Digital Terintegrasi</div></div>
                <div class="feature-item"><span class="feature-icon">💰</span><div>Administrasi &amp; Transaksi</div></div>
            </div>
        </div>
    </section>

    <section class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_100%_0%,rgba(201,120,89,.10),transparent_30%),#fbfaf6] px-4 py-10 sm:px-6">
        <div class="glass-card max-w-md fade-slide">
            <div class="mb-7 text-center">
                <div class="mx-auto mb-4 grid h-14 w-14 place-items-center rounded-2xl bg-emerald-100 text-2xl text-emerald-700"><i class="bi bi-shield-lock-fill"></i></div>
                <h2 class="text-2xl font-black tracking-tight text-[#255d54]">Login SIBERKAS</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Akses hanya untuk pengguna resmi sesuai peran yang terdaftar.</p>
            </div>

            <?php if(session('error')): ?>
                <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="alert alert-danger"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
                <?php echo csrf_field(); ?>

                <div>
                    <div class="uiverse-form-control">
                        <input id="email" type="email" class="uiverse-input <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="email" placeholder=" ">
                        <label for="email" aria-hidden="true">
                            <span style="transition-delay: 0ms">E</span><span style="transition-delay: 50ms">m</span><span style="transition-delay: 100ms">a</span><span style="transition-delay: 150ms">i</span><span style="transition-delay: 200ms">l</span>
                        </label>
                    </div>
                </div>

                <div>
                    <div class="uiverse-form-control">
                        <input id="password" type="password" class="uiverse-input" name="password" required autocomplete="current-password" placeholder=" ">
                        <label for="password" aria-hidden="true">
                            <span style="transition-delay: 0ms">P</span><span style="transition-delay: 50ms">a</span><span style="transition-delay: 100ms">s</span><span style="transition-delay: 150ms">s</span><span style="transition-delay: 200ms">w</span><span style="transition-delay: 250ms">o</span><span style="transition-delay: 300ms">r</span><span style="transition-delay: 350ms">d</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-between gap-4">
                    <label class="uiverse-heart-check" for="remember">
                        <input id="remember" name="remember" type="checkbox">
                        <span class="uiverse-heart-check__box" aria-hidden="true"></span>
                        <svg class="uiverse-heart-check__icon" viewBox="0 0 68 87" aria-hidden="true" xmlns="http://www.w3.org/2000/svg">
                            <path d="M28.048 74.752c-.74 0-3.428.03-3.674-.175-3.975-3.298-10.07-11.632-12.946-15.92C7.694 53.09 5.626 48.133 3.38 42.035 1.937 38.12 1.116 35.298.93 31.012c-.132-3.034-.706-7.866 0-10.847C2.705 12.67 8.24 7.044 15.801 7.044c1.7 0 3.087-.295 4.55.875 4.579 3.663 5.515 8.992 7.172 14.171.142.443 3.268 6.531 2.1 7.698-.362.363-1.161-10.623-1.05-12.071.26-3.37 1.654-5.522 3.15-8.398 3.226-6.205 7.617-7.873 14.52-7.873 2.861 0 5.343-.274 8.049 1.224 16.654 9.22 14.572 23.568 5.773 37.966-1.793 2.934-3.269 6.477-5.598 9.097-1.73 1.947-4.085 3.36-5.774 5.424-2.096 2.562-3.286 5.29-5.598 7.698-4.797 4.997-9.56 10.065-14.522 14.872-1.64 1.588-10.194 6.916-10.672 7.873-.609 1.217 2.76-.195 4.024-.7"></path>
                        </svg>
                        <span>Ingat perangkat ini</span>
                    </label>
                    <span class="text-right text-xs font-medium text-slate-400">Lupa password? Hubungi Admin Sistem</span>
                </div>

                <button type="submit" class="uiverse-button uiverse-button--primary uiverse-button--full" data-uiverse-ready="true">
                    <span class="uiverse-button__splash" aria-hidden="true"></span>
                    <span class="uiverse-button__wave uiverse-button__wave--left" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487 487" preserveAspectRatio="none"><path fill-opacity=".45" fill-rule="nonzero" fill="#FFF" d="M0 .3c67 2.1 134.1 4.3 186.3 37 52.2 32.7 89.6 95.8 112.8 150.6 23.2 54.8 32.3 101.4 61.2 149.9 28.9 48.4 77.7 98.8 126.4 149.2H0V.3z"></path></svg>
                    </span>
                    <span class="uiverse-button__wave uiverse-button__wave--right" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 487 487" preserveAspectRatio="none"><path fill-opacity=".45" fill-rule="nonzero" fill="#FFF" d="M487 486.7c-66.1-3.6-132.3-7.3-186.3-37s-95.9-85.3-126.2-137.2c-30.4-51.8-49.3-99.9-76.5-151.4C70.9 109.6 35.6 54.8.3 0H487v486.7z"></path></svg>
                    </span>
                    <span class="uiverse-button__sheen" aria-hidden="true"></span>
                    <span class="uiverse-button__content"><i class="bi bi-box-arrow-in-right"></i><span>Masuk ke Sistem</span></span>
                </button>
            </form>
            <p class="mt-7 text-center text-xs text-slate-400">© <?php echo e(date('Y')); ?> SIBERKAS — Sistem Informasi Berkas</p>
        </div>
    </section>
</div>

<script>
    lottie.loadAnimation({
        container: document.getElementById('lottie-box'),
        renderer: 'svg',
        loop: true,
        autoplay: true,
        path: 'https://assets9.lottiefiles.com/packages/lf20_tno6cg2w.json'
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\punya_taupik\SEMESTER 6\BANK TA\bank_TA\resources\views/auth/login.blade.php ENDPATH**/ ?>