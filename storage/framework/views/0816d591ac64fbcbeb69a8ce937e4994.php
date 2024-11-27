<div class="wsus__user_list_item messenger-list-item" data-id="<?php echo e($user->id); ?>">
    <div class="img">
        <img src="<?php echo e(asset($user->avatar)); ?>" alt="User" class="img-fluid">
        <span class="inactive"></span>
    </div>
    <div class="text">
        <h5><?php echo e($user->name); ?></h5>
        <?php if($lastMessage->from_id == auth()->user()->id): ?>
            <p><span>You</span> <?php echo e(truncate($lastMessage->body)); ?></p>
        <?php else: ?>
            <p><?php echo e(truncate($lastMessage->body)); ?></p>
        <?php endif; ?>
    </div>
    <?php if($unseenCounter !== 0): ?> 
        <span class="badge bg-primary text-light unseen-count time"><?php echo e($unseenCounter); ?></span>
    <?php endif; ?>
</div><?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/components/contact-list-item.blade.php ENDPATH**/ ?>