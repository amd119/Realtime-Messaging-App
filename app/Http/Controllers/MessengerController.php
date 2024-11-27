<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Events\Message as MessageEvent;
use App\Models\Favourite;
use App\Models\Message;
use App\Models\User;

class MessengerController extends Controller
{
    use FileUploadTrait;

    function index() : View {
        $favouriteList = Favourite::with('user:id,name,avatar')->where('username', Auth::id())->get();
        return view('messenger.index', compact('favouriteList'));
    }

    // Search User Profiles
    function search(Request $request) {
        // dd($request->all());
        $getRecords = null;
        $input = $request['query'];
        $records = User::where('id', '!=', Auth::user()->id)
            ->where('name', 'LIKE', "%{$input}%")
            ->orWhere('username', 'LIKE', "%{$input}%")
            // ->get();
            ->paginate(10);

        if($records->total() < 1) {
            $getRecords .= "<p class='text-center'>Not Found</p>";
        }

        // return $records;
        foreach ($records as $record) {
            # code...
            $getRecords .= view('messenger.components.search-item', compact('record'))->render();
        }

        // return $getRecords;
        return response()->json([
            'records' => $getRecords,
            'last_page' => $records->lastPage()
        ]);
    }

    // fetch user by id
    function fetchIdInfo(Request $request) {
        // dd($request->all());
        $fetch = User::where('id', $request['id'])->first();
        $favourite = Favourite::where(['username' => Auth::id(), 'favourite_id' => $fetch->id])->exists();
        $sharedPhotos = Message::where('from_id', Auth::user()->id)->where('to_id', $request->id)->whereNotNull('attachment')
            ->orWhere('from_id', $request->id)->where('to_id', Auth::user()->id)->whereNotNull('attachment')
            ->latest()->get();

            $content = "";
            foreach ($sharedPhotos as $photo) {
                $content .= view('messenger.components.gallery-item', compact('photo'))->render();
            }

        return response()->json([
            'fetch' => $fetch,
            'favourite' => $favourite,
            'shared_photos' => $content
        ]);
    }

    function sendMessage(Request $request) {
        // dd($request->all());
        $request->validate([
            // 'message' => ['required'],
            'id' => ['required', 'integer'],
            'tempMsgId' => ['required'],
            'attachment' => ['nullable', 'max:1024', 'image']
        ]);

        // store the message to db
        $attachmentPath = $this->uploadFile($request, 'attachment');
        $message = new Message();
        $message->from_id = Auth::user()->id;
        $message->to_id = $request->id;
        $message->body = $request->message;
        if($attachmentPath) $message->attachment = json_encode($attachmentPath);
        $message->save();

        // broadcast event
        MessageEvent::dispatch($message);

        return response()->json([
            'message' => $message->attachment ? $this->messageCard($message, true) : $this->messageCard($message),
            'tempID' => $request->tempMsgId
        ]);
    }

    function messageCard($message, $attachment = false) {
        return view('messenger.components.message-card', compact('message', 'attachment'))->render(); // we will be passing this message variable in our view using compact and rendered the view & give us raw content inside this template
    }

    // fetch messages from db
    function fetchMessages(Request $request) {
        // dd($request->all());
        $messages = Message::where('from_id', Auth::user()->id)->where('to_id', $request->id) // first condition, for sender
            ->orWhere('from_id', $request->id)->where('to_id', Auth::user()->id) // second condition, reverse, for receiver
            ->latest()->paginate(15);

        // return $messages;
        $response = [
            'last_page' => $messages->lastPage(), // This is inbuilt function
            'last_message' => $messages->last(), // This is inbuilt function
            'messages' => ''
        ];

        if(count($messages) < 1) {
            $response['messages'] = "<div class='d-flex justify-content-center no_messages align-items-center h-100'><p>Say 'Hi' and start conversation</p></div>";
            return response()->json($response);

        }

        $allMessages = '';
        foreach($messages->reverse() as $message) {
            $allMessages .= $this->messageCard($message, $message->attachment ? true : false); // if message has attachment it will be true otherwise it will be false
        }

        $response['messages'] = $allMessages;

        return response()->json($response);

    }

    // Fetch contacts from database
    // function fetchContacts(Request $request) {
    //     // joining our messages and users table depending on the from id and to id
    //     $users = Message::join('users', function($join) {
    //         $join->on('messages.from_id', '=', 'users.id') // if messages from id and users id is match, it will create join
    //         ->orOn('messages.to_id', '=', 'users.id'); // or if messages to id and users id is match, it will create join
    //     })
    //     // simple where conditions, where we are selecting the messages depending on our current login users id
    //     ->where(function($q) {
    //         $q->where('messages.from_id', Auth::user()->id)
    //         ->orWhere('messages.to_id', Auth::user()->id);
    //     })
    //     ->where('users.id', '!=', Auth::user()->id) // we are currently trying to fetch the profiles, so we need to ignoring our profiles to fetch and it don't show in the front end
    //     ->select('users.*', DB::raw('MAX(messages.created_at) max_created_at')) // raw queries, we're selecting all information about the users, and we're checking the created at value of the messages table, we're taking the max value of it, and we're creating an alias of it so later on we can order by that created at value.
    //     ->orderBy('max_created_at', 'desc') // we ordering by our data in descending, depending on the created at
    //     ->groupBy('users.id') // then we're grouping everything by the ID
    //     ->paginate(5); // last paginating it

    //     return $users;
    // }

    // Fetch contacts from database v Claude
    function fetchContacts(Request $request)
    {
        $currentUserId = Auth::id();

        $users = Message::select('users.id', 'users.avatar', 'users.name', 'users.username', 'users.email', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.created_at', 'users.updated_at')
            ->selectRaw('MAX(messages.created_at) as last_message_at')
            ->join('users', function ($join) use ($currentUserId) {
                $join->on('messages.from_id', '=', 'users.id')
                    ->where('messages.to_id', '=', $currentUserId)
                    ->orOn('messages.to_id', '=', 'users.id')
                    ->where('messages.from_id', '=', $currentUserId);
            })
            ->where('users.id', '!=', $currentUserId)
            ->groupBy('users.id', 'users.avatar', 'users.name', 'users.username', 'users.email', 'users.email_verified_at', 'users.password', 'users.remember_token', 'users.created_at', 'users.updated_at')
            ->orderByDesc('last_message_at')
            ->paginate(10);

        if(count($users) > 0) {
            $contacts = '';
            foreach($users as $user) {
                $contacts .= $this->getContactItem($user);
            }
        }else {
            $contacts = "<p class='text-center no_contact'>Your contact list is empty</p>";
        }

        return response()->json([
            'contacts' => $contacts,
            'last_page' => $users->lastPage()
        ]);
    }

    function getContactItem($user) {
        $lastMessage = Message::where('from_id', Auth::id())->where('to_id', $user->id) // first condition, for sender
        ->orWhere('from_id', $user->id)->where('to_id', Auth::id()) // second condition, reverse, for receiver
        ->latest()->first();

        $unseenCounter = Message::where('from_id', $user->id)->where('to_id', Auth::id())->where('seen', 0)
        ->count(); // from id is end user id, to id is our id

        return view('messenger.components.contact-list-item', compact('lastMessage', 'unseenCounter', 'user'))->render();
    }

    // update contatc item
    function updateContactItem(Request $request) {
        // dd($request->all());
        // get user data
        $user = User::where('id', $request->username)->first();

        if(!$user) {
            return response()->json([
                'message' => 'user not found'
            ], 401);
        }

        $contactItem = $this->getContactItem($user);
        return response()->json([
            'contact_item' => $contactItem
        ], 200);
    }

    function makeSeen(Request $request) {
        // dd($request->all());
        Message::where('from_id', $request->id)
            ->where('to_id', Auth::id())
            ->where('seen', 0)->update(['seen' => 1]);

        return true;
    }

    // add/remove favourite list
    function favourite(Request $request) {
        // dd($request->all());
        $query = Favourite::where(['username' => Auth::id(), 'favourite_id' => $request->id]);
        $favouriteStatus = $query->exists();

        if(!$favouriteStatus) {
            $fav = new Favourite();
            $fav->username = Auth::id();
            $fav->favourite_id = $request->id;
            $fav->save();

            // return $fav ? true : false;
            return response(['status' => 'added']);
        }else {
            $query->delete();
            return response(['status' => 'removed']);
        }
    }

    // fetch favourite list item
    // function fetchfavouriteList() {
    //     // return 'working';
    //     $list = Favourite::with('user:id,name,avatar')->where('username', Auth::id())->get();
    //     $favourites = "";
    //     foreach($list as $item) {
    //         $favourites .= view('messenger.components.favourite-item', compact('item'));
    //     }

    //     return response()->json([
    //         'favourite_list' => $favourites
    //     ]);
    // }

    // delete message
    function deleteMessage(Request $request) {
        // dd($request->all());
        $message = Message::findOrFail($request->message_id);
        if($message->from_id == Auth::id()) {
            $message->delete();
            return response()->json([
                'id' => $request->message_id
            ], 200);
        }
        return; // if anything goes wrong we can return nothing
    }
}
