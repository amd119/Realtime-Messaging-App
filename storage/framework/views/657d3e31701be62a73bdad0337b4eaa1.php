<?php if($attachment): ?>
    <?php
        $imagePath = json_decode($message->attachment);
    ?>
    <div class="wsus__single_chat_area message-card" data-id="<?php echo e($message->id); ?>">
        <div class="wsus__single_chat <?php echo e($message->from_id === auth()->user()->id ? 'chat_right' : ''); ?>">
            <a class="venobox" data-gall="gallery<?php echo e($message->id); ?>" href="<?php echo e(asset($imagePath)); ?>">
                <img src="<?php echo e(asset($imagePath)); ?>" alt="" class="img-fluid w-100">
            </a>
            <?php if( $message->body ): ?>
                <p class="messages"><?php echo e($message->body); ?></p>
            <?php endif; ?>
            <span class="time"> <?php echo e(timeAgo($message->created_at)); ?></span>
            <?php if($message->from_id === auth()->user()->id): ?>
                <a class="action dlt-message" data-id="<?php echo e($message->id); ?>" href=""><i class="fas fa-trash"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="wsus__single_chat_area message-card" data-id="<?php echo e($message->id); ?>">
        <div class="wsus__single_chat <?php echo e($message->from_id === auth()->user()->id ? 'chat_right' : ''); ?>">
            <p class="messages"><?php echo e($message->body); ?></p>
            <span class="time"> <?php echo e(timeAgo($message->created_at)); ?></span>
            <?php if($message->from_id === auth()->user()->id): ?>
                <a class="action dlt-message" data-id="<?php echo e($message->id); ?>" href=""><i class="fas fa-trash"></i></a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?><?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/components/message-card.blade.php ENDPATH**/ ?>