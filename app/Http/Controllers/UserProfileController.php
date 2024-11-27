<?php

namespace App\Http\Controllers;

use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    use FileUploadTrait;

    function update(Request $request) {
        // dd($request->all()); //use for check all the data is working
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:1024'],
            'name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username,'.auth()->user()->id],
            'email' => ['required', 'email', 'max:100']
        ]);

        $avatarPath = $this->uploadFile($request, 'avatar');

        // dd($avatarPath);
        $user = Auth::user();
        if($avatarPath) $user->avatar = $avatarPath;
        $user->name =  $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if($request->filled('current_password')) {
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'string', 'min:8', 'confirmed']
            ]);
            $user->password = bcrypt($request->password);
        }
        $user->save();

        notyf()->success('Update Successfully.');
        return response(['message' => 'Update Successfully'], 200);
    }
}











/*  1. function update(Request $request):
     Ini adalah sebuah fungsi di dalam controller yang akan menangani permintaan (request) dari pengguna, biasanya saat ingin memperbarui data di server.
     Parameter Request $request adalah sebuah objek request yang berisi semua data yang dikirim oleh pengguna, misalnya data dari form HTML.
    2. $request->all():
    Bagian ini akan mengambil semua data yang dikirim dalam request, baik itu data dari form input (POST atau GET), file yang diunggah, atau parameter lain.
    Contoh: Jika pengguna mengirim form dengan data name: John dan email: john@example.com, maka $request->all() akan mengembalikan array seperti:
    ['name' => 'John', 'email' => 'john@example.com']
    3. dd($request->all()):
    dd() adalah singkatan dari dump and die, yaitu fitur Laravel yang digunakan untuk menampilkan isi dari suatu variabel dan langsung menghentikan eksekusi kode.
    Ini sering digunakan oleh developer untuk debugging atau melihat isi variabel ketika mengembangkan aplikasi.
    Contoh: Jika fungsi update ini dijalankan dan pengguna mengirim data, maka halaman akan langsung menampilkan semua data tersebut dan kode di bawahnya tidak akan dijalankan.

    KESIMPULAN:
    Fungsi ini digunakan untuk menangkap data dari pengguna dan menampilkan (dump) semua data tersebut untuk tujuan debugging. Misalnya, saat memperbarui profil, kamu bisa melihat apakah data yang dikirim sudah benar sebelum menyimpannya ke database.
*/
