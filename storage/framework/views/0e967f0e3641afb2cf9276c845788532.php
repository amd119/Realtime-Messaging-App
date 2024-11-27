<div class="wsus__user_list">
    <div class="wsus__user_list_header">
        <h3>
            <span><img src="<?php echo e(asset('assets/images/chat_list_icon.png')); ?>" alt="Chat" class="img-fluid"></span>
            MESSENGER
        </h3>
        <div class="d-flex">
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <a href="route('logout')" onclick="event.preventDefault();
                this.closest('form').submit();" style="padding-right: 4px">
                    <span class="setting">
                        <i class="fas fa-sign-out-alt" style="color: red"></i>
                    </span>
                </a>
            </form>
            <span class="setting" data-bs-toggle="modal" data-bs-target="#exampleModal">
                <i class="fas fa-user-cog"></i>
            </span>
        </div>

    <?php echo $__env->make('messenger.layouts.profile-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    
    <?php echo $__env->make('messenger.layouts.search-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="wsus__favourite_user">
        <div class="top">favourites</div>
        <div class="row favourite_user_slider">
            <?php $__currentLoopData = $favouriteList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-xl-3 messenger-list-item" role="button" data-id="<?php echo e($item->user?->id); ?>">
                    <a href="#" class="wsus__favourite_item">
                        <div class="img">
                            <img src="<?php echo e(asset($item->user?->avatar)); ?>" alt="User" class="img-fluid">
                            <span class="inactive"></span>
                        </div>
                        <p><?php echo e($item->user->name); ?></p>
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="wsus__save_message">
        <div class="top">your space</div>
        <div class="wsus__save_message_center messenger-list-item" data-id="<?php echo e(auth()->user()->id); ?>">
            <div class="icon">
                <i class="far fa-bookmark"></i>
            </div>
            <div class="text">
                <h3>Saved Messages</h3>
                <p>Save messages secretly</p>
            </div>
            <span>you</span>
        </div>
    </div>

    <div class="wsus__user_list_area">
        <div class="top">All Messages</div>
        <div class="wsus__user_list_area_height messenger-contacts">

        </div>
        

        

    </div>
</div>
<?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/layouts/user-list-sidebar.blade.php ENDPATH**/ ?>