<?php
    $image = json_decode($photo->attachment);
?>
<li>
    <a class="venobox" data-gall="gallery01" href="<?php echo e(asset($image)); ?>">
        <img src="<?php echo e(asset($image)); ?>" alt="" class="img-fluid w-100" loading="lazy">
    </a>
</li><?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/components/gallery-item.blade.php ENDPATH**/ ?>