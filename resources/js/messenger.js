/**
 * ------------------------------------
 * Global Variables
 * -------------------------------------
 */

var tempMsgId = 0;
var activeUsersIds = [];

const messageForm = $(".message-form"),
   messageInput = $(".message-input"),
   messageBoxContainer = $(".wsus__chat_area_body"),
   csrf_token = $("meta[name=csrf_token]").attr("content"),
   auth_id = $("meta[name=auth_id]").attr("content"),
   url = $("meta[name=url]").attr("content"),
   messengerContactsBox = $(".messenger-contacts");

const getMessengerId = () => $("meta[name=id]").attr("content"); // Help us to get that id whenever we want  ------ We choose meta tag because we will be able to get it easily and give more flexibility   // messenger/layout/app.blade.php
const setMessengerId = (id) => $("meta[name=id]").attr("content", id); // Help us to set the value or id in meta tag


/**
 * ------------------------------------
 * Reusable Functions
 * -------------------------------------
 */

function enableChatBoxLoader() {
    $(".wsus__message_paceholder").removeClass('d-none');
}

function disableChatBoxLoader() {
    $(".wsus__chat_app").removeClass('show_info');
    $(".wsus__message_paceholder").addClass('d-none');
    $(".wsus__message_paceholder_black").addClass('d-none');
}

// function showChatBox() {
//     $(".wsus__message_paceholder.black").addClass('d-none');
// }

// function hideChatBox() {
//     $(".wsus__message_paceholder.black").removeClass('d-none');
// }

function imagePreview(input, selector) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $(selector).attr('src', e.target.result)
        }

        reader.readAsDataURL(input.files[0]);
    }
}

// default variable
let searchPage = 1;
let noMoreDataSearch = false;
let searchTempVal = "";
let setSearchLoading = false;
//

function searchUsers(query) {

    if(query != searchTempVal) {
        searchPage = 1;
        noMoreDataSearch = false;
    }
    searchTempVal = query;

    if(!setSearchLoading && !noMoreDataSearch) {
        $.ajax({
            method: 'GET',
            url: '/messenger/search',
            data: {query: query, page: searchPage}, //domain?page=1
            beforeSend: function() {
                setSearchLoading = true;
                let loader = `
                <div class="text-center search-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                `
                $('.user_search_list_result').append(loader);
            },
            success: function(data) {
                // console.log(data);
                setSearchLoading = false;
                $('.user_search_list_result').find('.search-loader').remove();
                if(searchPage < 2) {
                    $('.user_search_list_result').html(data.records);
                }else {
                    $('.user_search_list_result').append(data.records);
                }
                noMoreDataSearch = searchPage >= data?.last_page
                if(!noMoreDataSearch) searchPage += 1
            },
            error: function(xhr, status, error) {
                setSearchLoading = false;
                $('.user_search_list_result').find('.search-loader').remove();
            }
        });
    }
}

function actionOnScroll(selector, callback, topScroll = false) {
    $(selector).on('scroll', function() {
        let element = $(this).get(0);
        const condition = topScroll ? element.scrollTop == 0 :
        element.scrollTop + element.clientHeight >= element.scrollHeight;

        if(condition) {
            callback();
        }
    });
}

function debounce(callback, delay) {
    let timerId;
    return function(...args) {
        clearTimeout(timerId);
        timerId = setTimeout(() => {
            callback.apply(this, args);
        }, delay);
    }
}

/**
 * ------------------------------------
 * Fetch id data of user and update the view
 * -------------------------------------
 */

// This function only work if the user select a profile or new profile.

function IDinfo(id) {
    $.ajax({
        method: 'GET',
        url: '/messenger/id-info',
        data: {id: id},
        beforeSend: function() {
            NProgress.start();
            enableChatBoxLoader();
        },
        success: function(data) {
            // fetch messages
            fetchMessages(data.fetch.id, true);

            $(".wsus__chat_info_gallery").html("");
            // load gallery
            if(data?.shared_photos) {
                $(".nothing_share").addClass('d-none');

                $(".wsus__chat_info_gallery").html(data.shared_photos);
            }else {
                $(".nothing_share").removeClass('d-none');
            }

            initVenobox();

            data.favourite == 1
                ? $('.favourite').addClass('active')
                : $('.favourite').removeClass('active');

            // console.log(data);
            $(".messenger_header").find("img").attr("src", data.fetch.avatar);
            $(".messenger_header").find("h4").text(data.fetch.name);

            $(".messenger_info_view .user_photo").find("img").attr("src", data.fetch.avatar);
            $(".messenger_info_view").find(".user_name").text(data.fetch.name);
            $(".messenger_info_view").find(".user_unique_name").text(data.fetch.username);

            NProgress.done();
        },
        error: function(xhr, status, error) {
            disableChatBoxLoader();

        }
    });
}


/**
 * ------------------------------------
 * Send Message
 * -------------------------------------
 */

function sendMessage() {
    tempMsgId += 1;
    let tempID = `temp_${tempMsgId}`; // temp_1 temp_2 ...
    let hasAttachment = !!$(".attachment-input").val(); // in this variable there will be a boolean, if attachment-input have value it will be true, otherwise is not. !! => This sign will convert that in a boolean format.
    const inputValue = messageInput.val();
    // alert(inputValue);

    if(inputValue.length > 0 || hasAttachment) {
        const formData = new FormData($(".message-form")[0]);
        formData.append("id", getMessengerId());
        formData.append("tempMsgId", tempID);
        formData.append("_token", csrf_token);
        // console.log(formData);

        // console.log("FormData object:", formData);
        // console.log("Input value:", inputValue);
        // console.log("Messenger ID:", getMessengerId());
        // console.log("CSRF Token:", csrf_token);
        $.ajax({
            method: 'POST',
            url: '/messenger/send-message',
            data: formData,
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: function() {
                // console.log(hasAttachment);

                // add temp message on dom
                if(hasAttachment) {
                    messageBoxContainer.append(sendTempMessageCard(inputValue, tempID, true));
                }else{
                    messageBoxContainer.append(sendTempMessageCard(inputValue, tempID));
                }

                $('.no_messages').addClass('d-none');
                // messageForm.trigger("reset");
                // $(".emojionearea-editor").text("");
                scrollToBottom(messageBoxContainer);
                messageFormReset();
            },
            success: function(data) {
                makeSeen(true);
                updateContactItem(getMessengerId()); // update contact item

                const tempMessageCardElement = messageBoxContainer.find(`.message-card[data-id=${data.tempID}]`);
                // console.log(tempMessageCardElement);
                tempMessageCardElement.before(data.message);
                tempMessageCardElement.remove();
                initVenobox();

                // initEmojiPicker();
            },
            error: function(xhr, status, error) {

            }
        });
    }
}

function sendTempMessageCard(message, tempID, attachment = false) {

    if(attachment) {
        // console.log(message.length);
        return `
        <div class="wsus__single_chat_area message-card" data-id="${tempID}">
            <div class="wsus__single_chat chat_right">
                <div class="pre_loader">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                ${message.length > 0 ? `<p class="messages">${message}</p>` : ''}
                <span class="clock"><i class="fas fa-clock"></i> now</span>

            </div>
        </div>
        `
    } else {
        return `
        <div class="wsus__single_chat_area message-card" data-id="${tempID}">
            <div class="wsus__single_chat chat_right">
                <p class="messages">${message}</p>
                <span class="clock"><i class="fas fa-clock"></i> now</span>

            </div>
        </div>
        `
    }

}

function receiveMessageCard(e) {
    if(e.attachment) {
        return `
        <div class="wsus__single_chat_area message-card" data-id="${e.id}">
            <div class="wsus__single_chat">
                <a class="venobox" data-gall="gallery${e.id}" href="${e.attachment}">
                    <img src="${e.attachment}" alt="" class="img-fluid w-100">
                </a>
                ${e.body != null && e.body.length > 0 ? `<p class="messages">${e.body}</p>` : ''}
            </div>
        </div>
        `
    }else {
        return `
        <div class="wsus__single_chat_area message-card" data-id="${e.id}">
            <div class="wsus__single_chat">
                <p class="messages">${e.body}</p>
            </div>
        </div>
        `
    }
}


function messageFormReset() {
    $('.attachment-block').addClass('d-none');
    // $('.emojionearea-editor').text("");
    // $("input[type='file']").val(null);  // If anything goes wrong, this line will be needed and remove the next one
    messageForm.trigger("reset");
    var emojiElt = $('#example1').emojioneArea();
    emojiElt.data("emojioneArea").setText('');
}

// cancel/reset selected attachment
function cancelAttachment() {
    $('.attachment-block').addClass('d-none');
}


/**
 * ------------------------------------
 * Fetch message from database
 * -------------------------------------
 */

let messagesPage = 1;
let noMoreMessages = false;
let messagesLoading = false; // if there's a previous request, a new request will not send until the previous request is completed

function fetchMessages(id, newFetch = false) {
    if(newFetch) {
        messagesPage = 1;
        noMoreMessages = false;
    }

    if(!noMoreMessages && !messagesLoading) {
        $.ajax({
            method: 'GET',
            url: '/messenger/fetch-messages',
            data: {
                _token: csrf_token,
                id: id,
                page: messagesPage
            },
            beforeSend: function() {
                messagesLoading = true;
                let loader = `
                <div class="text-center messages-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                `;
                messageBoxContainer.prepend(loader);
            },
            success: function(data) {
                messagesLoading = false;
                //remove loader
                messageBoxContainer.find(".messages-loader").remove();

                // make messages seen
                makeSeen(true);

                if(messagesPage == 1) {
                    messageBoxContainer.html(data.messages);
                    scrollToBottom(messageBoxContainer);
                }else {
                    const lastMsg = $(messageBoxContainer).find(".message-card").first();
                    const curOffset = lastMsg.offset().top - messageBoxContainer.scrollTop();

                    messageBoxContainer.prepend(data.messages); // we use append for add the data at bottom portion, and we use prepend to add the data at the top portion
                    messageBoxContainer.scrollTop(lastMsg.offset().top - curOffset);
                }

                // pagination lock and page increment
                noMoreMessages = messagesPage >= data?.last_page;
                if(!noMoreMessages) messagesPage += 1;

                initVenobox();

                disableChatBoxLoader();
            },
            error: function(xhr, statux, error) {
                console.log(error);
            }
        });
    }
}

/**
 * ------------------------------------
 * Fetch contact list from database
 * -------------------------------------
 */

 let contactsPage = 1;
 let noMoreContacts = false;
 let contactLoading = false;

 function getContacts() {
    if(!contactLoading && !noMoreContacts) {
        $.ajax({
            method: 'GET',
            url: '/messenger/fetch-contacts',
            data: {page: contactsPage},
            beforeSend: function() {
                contactLoading = true;
                let loader = `
                <div class="text-center contact-loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                `;
                messengerContactsBox.append(loader)
            },
            success: function(data) {
                // console.log(data);
                contactLoading = false;
                messengerContactsBox.find(".contact-loader").remove();
                if (contactsPage < 2) {
                    messengerContactsBox.html(data.contacts);
                }else {
                    messengerContactsBox.append(data.contacts);
                }

                noMoreContacts = contactsPage >= data?.last_page;
                if(!noMoreContacts) contactsPage += 1;

                userUpdateActiveList();
            },
            error: function(xhr, status, error) {
                contactLoading = false;
                messengerContactsBox.find(".contact-loader").remove();
            }
        });
    }
 }

 /**
 * ------------------------------------
 * Update Contact Item
 * -------------------------------------
 */

 function updateContactItem(username) {
    if(username != auth_id) {
        $.ajax({
            method: 'GET',
            url: '/messenger/update-contact-item',
            data: {username: username},
            success: function(data) {
                messengerContactsBox.find('.no_contact').remove();
                messengerContactsBox.find(`.messenger-list-item[data-id="${username}"]`).remove();
                messengerContactsBox.prepend(data.contact_item);

                if(activeUsersIds.includes(+username)) {
                    // console.log(username);
                    userActive(username);
                }

                if(username == getMessengerId()) updateSelectedItem(username);
            },
            error: function(xhr, status, error) {

            }
        });
    }
 }

 function updateSelectedItem(username) {
    $('.messenger-list-item').removeClass('active');
    $('body').find(`.messenger-list-item[data-id="${username}"]`).addClass('active');
 }


 /**
 * ------------------------------------
 * Make message seen
 * ------------------------------------
 */

 function makeSeen(status) {
    $(`.messenger-list-item[data-id="${getMessengerId()}"]`).find('.unseen-count').remove();
    $.ajax({
        method: 'POST',
        url: '/messenger/make-seen',
        data: {
            _token: csrf_token,
            id: getMessengerId()
        },
        success: function(data) {},
        error: function(xhr, status, error) {}
    });
 }


 /**
 * ------------------------------------
 * Favourites
 * ------------------------------------
 */

  function fav(username) {
    $(".favourite").toggleClass('active');

    $.ajax({
        method: 'POST',
        url: '/messenger/favourite',
        data: {
            _token: csrf_token,
            id: username
        },
        success: function(data) {
            if(data.status == 'added') {
                notyf.success('Added to favourite list.');
            }else {
                notyf.success('Removed to favourite list.');
            }
        },
        error: function(xhr, status, error) {

        }
    });
 }


 /**
 * ------------------------------------
 * Delete Message
 * ------------------------------------
 */

 function deleteMessage(message_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            method: 'DELETE',
            url: '/messenger/delete-message',
            data: {
                _token: csrf_token,
                message_id: message_id
            },
            beforeSend: function() {
                $(`.message-card[data-id="${message_id}"]`).remove();
                // console.log(message_id);
            },
            success: function(data) {
                updateContactItem(getMessengerId());
            },
            error: function(xhr, status, error) {}
          });
        }
      });
 }



 /**
 * ------------------------------------
 * Get Favourite Users
 * ------------------------------------
 */

// function fetchfavouriteList() {
//     $.ajax({
//         method: 'GET',
//         url: '/messenger/fetch-favourite',
//         data: {
//         },
//         success: function(data) {
//             $(".favourite_user_slider").html(data.favourite_list);
//         },
//         error: function(xhr, status, error) {
//         }
//     });
// }


/**
 * ------------------------------------
 * Slide to bottom on action
 * -------------------------------------
 */

function scrollToBottom(container) {
    $(container).stop().animate({
        scrollTop: $(container)[0].scrollHeight
    });
}

/**
 * ------------------------------------
 * Initialize venobox.js
 * -------------------------------------
 */

function initVenobox() {
    $('.venobox').venobox();
}

/**
 * ------------------------------------
 * Play message sound
 * -------------------------------------
 */

// function playNotificationSound() {
//     const sound = new Audio(`/default/message-sound.mp3`);
//     sound.play();
// }


// function initEmojiPicker() {
//     $("#example1").emojioneArea({
//         pickerPosition: "top",
//         tonesStyle: "radio",
//         events: {
//             keyup: function(editor, event) {
//                 $("#example1").val(this.getText()).trigger("keyup");
//             }
//         }
//     });
// }

/**
 * If we want to select a 'class' or 'id' in the JQUery we have to use this format => $(...) => $(selector)
 * Remember this: id=># class=>.
*/

// Listen to message channel
window.Echo.private('message.' + auth_id)
    .listen("Message",
    (e) => {
        // console.log(e);
        if(getMessengerId() != e.from_id) {
            updateContactItem(e.from_id);
            // playNotificationSound();
        }

        let message = receiveMessageCard(e);
        if(getMessengerId() == e.from_id) {
            messageBoxContainer.append(message);
            scrollToBottom(messageBoxContainer);
        }
    }
);

// Listen to online channel
window.Echo.join('online')
    .here((users) => {
        // console.log(users);

        // set active users
        setActiveUserId(users);
        // console.log(activeUsersIds);

        $.each(users, function(index, user) {
            // console.log(user.id);
            userActive(user.id);
        })
    })
    .joining((user) => {
        // console.log('Joining: ');
        // console.log(user);

        // add user to array
        addNewUserId(user.id);
        // console.log(activeUsersIds);

        userActive(user.id);
    })
    .leaving((user) => {
        // console.log('Leaving: ');
        // console.log(user);

        // remove user from array
        removeUserId(user.id);
        // console.log(activeUsersIds);
        userInactive(user.id);
    })

function userUpdateActiveList() {
    $('.messenger-list-item').each(function(index, value) {
        // console.log($(this).data('id'));
        let id = $(this).data('id');
        if(activeUsersIds.includes(id)) userActive(id);
    });
}

function userActive(id) {
    let contactItem = $(`.messenger-list-item[data-id="${id}"]`).find('.img').find('span');
    contactItem.removeClass('inactive');
    contactItem.addClass('active');
}

function userInactive(id) {
    let contactItem = $(`.messenger-list-item[data-id="${id}"]`).find('.img').find('span');
    contactItem.removeClass('active');
    contactItem.addClass('inactive');
}

// set active users id to array
function setActiveUserId(users) {
    $.each(users, function(index, user) {
        activeUsersIds.push(user.id);
    });
}

// add new user to array
function addNewUserId(id) {
    activeUsersIds.push(id);
}

// remove user from array
function removeUserId(id) {
    let index = activeUsersIds.indexOf(id);

    if(index !== -1) {
        activeUsersIds.splice(index, 1);
    }
}

/**
 * ------------------------------------
 * On Dom Load
 * ------------------------------------
 */

$(document).ready(function() {
    getContacts();
    // fetchfavouriteList();
    // initEmojiPicker();

    if(window.innerWidth < 768) {
        $("body").on('click', '.messenger-list-item', function() {
            $(".wsus__user_list").addClass('d-none');
        });

        $("body").on('click', '.back_to_list', function() {
            $(".wsus__user_list").removeClass('d-none');
        });
    }

   $('#select_file').change(function() {
        imagePreview(this, '.profile-image-preview');
   });

   // Search action on keyup with delay 500 milisecond
   const debouncedSearch = debounce(function() {
        const value = $('.user_search').val();
        searchUsers(value);
   }, 500);

   // This func is to listen the word that user type in the search bar in every click
   $('.user_search').on('keyup', function() {
        let query = $(this).val();
        // console.log(query);
        // searchUsers(query);
        if(query.length > 0) {
            debouncedSearch();
        }
   });

   // search pagination
   actionOnScroll(".user_search_list_result", function() {
        let value = $('.user_search').val();
        searchUsers(value);
        // alert('working');
   });

   // click action for messenger list item
   $("body").on("click", ".messenger-list-item", function() {
        // alert('selected');
        const dataId = $(this).attr("data-id");
        updateSelectedItem(dataId);
        // alert(dataId);
        setMessengerId(dataId);
        IDinfo(dataId);

        messageFormReset();
   });

    // send message
    messageForm.on("submit", function(e) {
        e.preventDefault(); // because the default behavior of a form is to reload the page, so we are just preventing it.
        // alert('working');
        sendMessage();
    });

    // send attachment
    $('.attachment-input').change(function() {
        imagePreview(this, '.attachment-preview');
        $('.attachment-block').removeClass('d-none');
    });

    $('.cancel-attachment').on('click', function() {
        cancelAttachment();
    });

    // message pagination
    actionOnScroll(".wsus__chat_area_body", function() {
        fetchMessages(getMessengerId()); // when we reach and hit the top, it will call the fetch method and it will send the ajax request, it will fetch the data.
   }, true);

   // contacts pagination
   actionOnScroll(".messenger-contacts", function() {
    // alert('working');
    getContacts();
   });

   // add/remove to favourites
   $(".favourite").on('click', function(e) {
        // alert('working');
        e.preventDefault();
        fav(getMessengerId());
   });

   // delete message
   $("body").on('click', '.dlt-message', function(e) {
        e.preventDefault();
        let id = $(this).data('id'); // grep the messenger id from frontend
        deleteMessage(id);
   });

   function adjustHeight() {
    var windowHeight = $(window).height();
    $('.wsus__chat_area_body').css('height', (windowHeight - 120) + 'px');
    $('.messenger-contacts').css('max-height', (windowHeight - 393) + 'px');
    $('.wsus__chat_info_gallery').css('height', (windowHeight - 400) + 'px');
    $('.user_search_list_result').css({
        'height': (windowHeight - 130) + 'px',
    });
   }

   // Call the function initially
   adjustHeight();

   // Call the function whenever the window is resized
   $(window).resize(function () {
    adjustHeight();
   });

});
