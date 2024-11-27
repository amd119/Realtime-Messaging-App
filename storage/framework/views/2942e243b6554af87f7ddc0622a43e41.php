<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi" />
    <meta name="id" content="">
    <meta name="csrf_token" content="<?php echo e(csrf_token()); ?>">
    <meta name="auth_id" content="<?php echo e(auth()->user()->id); ?>">
    <meta name="url" content="<?php echo e(public_path()); ?>">
    <title>Chatting Application</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('assets/images/chat_list_icon.png')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/all.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/slick.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/venobox.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/emojionearea.min.css')); ?>">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/spacing.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/style.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/responsive.css')); ?>">

    <!-- Script -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js', 'resources/js/messenger.js']); ?>
</head>

<body>

    <!--==================================
        Chatting Application Start
    ===================================-->
    <?php echo $__env->yieldContent('contents'); ?>
    <!--==================================
        Chatting Application End
    ===================================-->

    <!--jquery library js-->
    <script src="<?php echo e(asset('assets/js/jquery-3.7.1.min.js')); ?>"></script>

    <!--bootstrap js-->
    <script src="<?php echo e(asset('assets/js/bootstrap.bundle.min.js')); ?>"></script>

    <!--font-awesome js-->
    <script src="<?php echo e(asset('assets/js/Font-Awesome.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/slick.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/venobox.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/emojionearea.min.js')); ?>"></script>

    <!--main/custom js-->
    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        var notyf = new Notyf({
            duration: 800,
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/layouts/app.blade.php ENDPATH**/ ?>