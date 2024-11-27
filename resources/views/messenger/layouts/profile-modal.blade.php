<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <form action="#" class="profile-form" enctype="multipart/form-data">
                    @csrf {{-- We will use POST to update profile --}}
                    <div class="file profile-file">
                        <img src="{{ asset(auth()->user()->avatar) }}" alt="Upload" class="img-fluid profile-image-preview">
                        <label for="select_file"><i class="fal fa-camera-alt"></i></label>
                        <input id="select_file" type="file" hidden name="avatar">
                    </div>
                    <p>Edit information</p>
                    <input type="text" placeholder="Name" value="{{ auth()->user()->name }}" name="name">
                    <input type="text" placeholder="User ID" value="{{ auth()->user()->username }}" name="username">
                    <input type="email" placeholder="Email" value="{{ auth()->user()->email }}" name="email">
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
                        <button {{-- type="button" ==> to --}} type="submit" class="btn btn-primary save profile-save">Save changes</button>
                    </div>
                </form>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary cancel"
                    data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save">Save changes</button>
                /*! ==> 2 buttons move to inside the form */
            </div> --}}
        </div>
    </div>
</div>

@push('scripts')
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
                url: '{{ route("profile.update") }}',
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
@endpush



{{-- Baik, mari kita bahas bagian ini satu per satu dengan penjelasan singkat yang bermanfaat untuk pemula.

### Penjelasan Tentang Script dan Fungsi di Bawahnya

#### 1. `@push('scripts')`
   - **Apa itu**: Ini adalah **Blade directive** di Laravel. Blade adalah template engine di Laravel yang membantu kita menyusun tampilan (view).
   - **Fungsi**: `@push('scripts')` digunakan untuk menambahkan **blok script** JavaScript yang akan dimasukkan ke bagian yang sudah didefinisikan sebelumnya dengan `@stack('scripts')` di file layout.
   - **Kapan digunakan**: Berguna saat kamu ingin menambahkan kode JavaScript di halaman tertentu tanpa harus meletakkannya langsung di file layout utama.

#### 2. `$(document).ready(function() {...});`
   - **Apa itu**: Ini adalah bagian dari jQuery, sebuah pustaka JavaScript yang memudahkan manipulasi elemen HTML dan interaksi di halaman.
   - **Fungsi**: Ini memastikan bahwa semua elemen di halaman sudah dimuat sepenuhnya sebelum kode di dalamnya dijalankan. Jadi, setelah dokumen selesai dimuat, fungsi ini dijalankan.
   - **Contoh sederhana**: Saat halaman selesai dimuat, kamu bisa menginisialisasi fitur tertentu, seperti modal, animasi, atau menangani event seperti klik.

#### 3. `$('.profile-form').on('submit', function(e) {...});`
   - **Apa itu**: Ini adalah **event listener** untuk form dengan class `.profile-form`. Kode ini akan menangani **event submit** dari form tersebut.
   - **Fungsi**: Saat pengguna menekan tombol "Submit" di form, fungsi ini dijalankan.
   - **Contoh**: Form yang digunakan untuk memperbarui profil.

#### 4. `e.preventDefault();`
   - **Apa itu**: Fungsi JavaScript untuk **mencegah tindakan default** dari suatu event.
   - **Fungsi**: Pada form HTML, tindakan default ketika menekan submit adalah me-refresh halaman. Kode ini mencegah hal itu agar tidak terjadi, sehingga proses submit dilakukan secara AJAX tanpa reload.
   - **Mengapa penting**: Ini memberikan pengalaman pengguna yang lebih baik karena data dikirim tanpa harus me-refresh halaman.

#### 5. `let formData = $(this).serialize();`
   - **Apa itu**: Menggunakan jQuery untuk mengambil **data dari form** dan mengubahnya menjadi string yang bisa dikirimkan ke server.
   - **Fungsi**: `serialize()` akan mengumpulkan semua input di dalam form dan membuatnya siap untuk dikirim melalui AJAX.
   - **Contoh**: Jika form memiliki data seperti `name`, `email`, `password`, maka `formData` akan berisi sesuatu seperti: `name=John&email=john@example.com&password=123456`.

#### 6. `$.ajax({...})`
   - **Apa itu**: Metode jQuery untuk membuat permintaan **AJAX** ke server tanpa harus me-refresh halaman.
   - **Fungsi**: Digunakan untuk mengirim data form ke server (Laravel) menggunakan metode POST.
   - **Detail**:
     - **`method: 'POST'`**: Menggunakan metode POST untuk mengirim data.
     - **`url: '{{ route("profile.update") }}'`**: URL ke mana data akan dikirim. Di sini, rutenya adalah `profile.update`.
     - **` formData`**: Data form yang sudah di-serialize dikirim melalui AJAX.
     - **`success: function(data) {...}`**: Fungsi ini dijalankan jika permintaan AJAX berhasil.
     - **`error: function(xhr, status, error) {...}`**: Fungsi ini dijalankan jika terjadi kesalahan saat mengirim permintaan AJAX.

### Contoh Alur Kerja
1. Pengguna mengisi form profil dan menekan tombol "Save changes".
2. Form tidak di-submit secara langsung (karena ada `e.preventDefault()`), tetapi data dikumpulkan dengan `serialize()`.
3. Data form dikirim ke server menggunakan AJAX (ke rute `profile.update`).
4. Jika berhasil, kita bisa melakukan sesuatu di bagian `success`, misalnya menampilkan notifikasi.
5. Jika gagal, kita bisa menampilkan pesan kesalahan di bagian `error`.

### Kesimpulan
Kode ini memadukan Blade, jQuery, dan AJAX untuk memungkinkan pengiriman form tanpa reload halaman. Ini sangat berguna untuk membuat aplikasi yang lebih interaktif dan cepat, seperti fitur edit profil ini. Dengan AJAX, proses pengiriman data menjadi lebih mulus bagi pengguna. --}}
