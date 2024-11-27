<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form action="#" class="profile-form" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?> 
                    <div class="file profile-file">
                        <img src="<?php echo e(asset(auth()->user()->avatar)); ?>" alt="Upload" class="img-fluid profile-image-preview">
                        <label for="select_file"><i class="fal fa-camera-alt"></i></label>
                        <input id="select_file" type="file" hidden name="avatar">
                    </div>
                    <p>Edit information</p>
                    <input type="text" placeholder="Name" value="<?php echo e(auth()->user()->name); ?>" name="name">
                    <input type="text" placeholder="User ID" value="<?php echo e(auth()->user()->username); ?>" name="username">
                    <input type="email" placeholder="Email" value="<?php echo e(auth()->user()->email); ?>" name="email">
                    <p>Change password</p>
                    <div class="row">
                        <div class="col-xl-6">
                            <input type="password" placeholder="Current Password" name="current_password">
                        </div>
                        <div class="col-xl-6">
                            <input type="password" placeholder="New Password" name="password">
                        </div>
                        <div class="col-xl-12">
                            <input type="password" placeholder="Confirm Password" name="password_confirmation">
                        </div>
                    </div>
                    <div class="modal-footer p-0 mt-4">
                        <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">Close</button>
                        <button  type="submit" class="btn btn-primary save profile-save">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.profile-form').on('submit', function(e) {
            e.preventDefault(); // This will prevent reload page by default when we submit a form
            // alert('working');
            // let formData = $(this).serialize();// this func isn't working
            let saveBtn = $('.profile-save');
            let formData = new FormData(this);

            $.ajax({
                method: 'POST',
                url: '<?php echo e(route("profile.update")); ?>',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    saveBtn.text('saving...');
                    saveBtn.prop("disabled", true);
                },
                success: function(data) {
                    // Jika berhasil, kita bisa melakukan sesuatu di bagian success, misalnya menampilkan notifikasi.
                    window.location.reload(); // refresh automatically when we success updating our profile data.
                },
                error: function(xhr, status, error) {
                    // Jika gagal, kita bisa menampilkan pesan kesalahan di bagian error.
                    console.log(xhr)
                    let errors = xhr.responseJSON.errors;

                    // console.log(errors)
                    $.each(errors, function(index, value) {
                        // console.log(value[0]);
                        notyf.error(value[0]);  
                    });

                    saveBtn.text('Save changes');
                    saveBtn.prop("disabled", false);
                },
            })
        });
    });
</script>
<?php $__env->stopPush(); ?>




<?php /**PATH /home/mos/Desktop/laravel/dmes/resources/views/messenger/layouts/profile-modal.blade.php ENDPATH**/ ?>