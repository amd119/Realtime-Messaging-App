<?php $__env->startSection('contents'); ?>
<section class="wsus__chat_app show_info">

    <?php echo $__env->make('messenger.layouts.user-list-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <div class="wsus__chat_area">

        <div class="wsus__message_paceholder d-none"></div>
        <div class="wsus__message_paceholder_black d-flex justify-content-center align-items-center">
            <span class="select_a_user">Select a user to start conversation</span>
        </div>

        <div class="wsus__chat_area_header">
            <div class="header_left messenger_header">
                <span class="back_to_list">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <img src="" alt="User" class="img-fluid">
                <h4></h4>
            </div>
            <div class="header_right">
                <a href="" class="favourite"><i class="fas fa-star"></i></a>
                
                <a href="" class="info"><i class="fas fa-info-circle"></i></a>
            </div>
        </div>

        <div class="wsus__chat_area_body">
        
        </div>

        <div class="wsus__chat_area_footer">
            <div class="footer_message">
                <div class="img d-none attachment-block">
                    <img src="" alt="User" class="img-fluid attachment-preview">
                    <span class="cancel-attachment"><i class="far fa-times"></i></span>
                </div>
                <form action="#" class="message-form" enctype="multipart/form-data">
                    <div class="file">
                        <label for="file">
                            <i class="far fa-plus"></i>
                        </label>
                        <input id="file" type="file" name="attachment" hidden class="attachment-input" accept="image/*">
                    </div>
                    <textarea id="example1" rows="1" placeholder="Type a message.." name="message" class="message-input"></textarea>
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>

    <?php echo $__env->make('messenger.layouts.user-info-sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('messenger.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/index.blade.php ENDPATH**/ ?>