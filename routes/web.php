<?php

use App\Http\Controllers\MessengerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::group(['middleware' => 'auth'], function() {
    Route::get('messenger', [MessengerController::class, 'index'])->name('home');
    Route::post('profile', [UserProfileController::class, 'update'])->name('profile.update');

    // search router
    Route::get('messenger/search', [MessengerController::class, 'search'])->name('messenger.search');

    // fetch user by id
    Route::get('messenger/id-info', [MessengerController::class, 'fetchIdInfo'])->name('messenger.id-info');

    // send message
    Route::post('messenger/send-message', [MessengerController::class, 'sendMessage'])->name('messanger.send-message');

    // fetch message
    Route::get('messenger/fetch-messages', [MessengerController::class, 'fetchMessages'])->name('messanger.fetch-messages');

    // fetch contacts
    Route::get('messenger/fetch-contacts', [MessengerController::class, 'fetchContacts'])->name('messanger.fetch-contacts');

    // update contacts content
    Route::get('messenger/update-contact-item', [MessengerController::class, 'updateContactItem'])->name('messanger.update-contact-item');

    // make seen contacts
    Route::post('messenger/make-seen', [MessengerController::class, 'makeSeen'])->name('messanger.make-seen');

    // favourites contacts
    Route::post('messenger/favourite', [MessengerController::class, 'favourite'])->name('messanger.favourite');

    // fetch favourite
    Route::get('messenger/fetch-favourite', [MessengerController::class, 'fetchfavouriteList'])->name('messanger.fetch-favourite');

    // delete message
    Route::delete('messenger/delete-message', [MessengerController::class, 'deleteMessage'])->name('messanger.delete-message');
});
