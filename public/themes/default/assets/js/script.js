document.addEventListener('DOMContentLoaded', function() {

}, false);

$(document).ready(function(){
    active_pages = $('.active_pages');
    if(window.location.search){
        page = window.location.search.substring(6);

        if(page == 1){
            $('.page-prev').addClass('disabled')
        }
        if(page == active_pages.length){
            $('.page-next').addClass('disabled')
        }
        
        $('.page-'+page).addClass('active')
        page_prev = parseInt(page) - 1;
        page_next = parseInt(page) + 1;
        $('.page-prev .page-link').prop('search', 'page='+page_prev)
        $('.page-next .page-link').prop('search', 'page='+page_next)
        
    }
    else{
        $('.page-next .page-link').prop('search', 'page=2')
        $('.page-prev').addClass('disabled')
        $('.page-1').addClass('active')
    }
});

function setAssignFile(file, orderID, editor_id){
    window.localStorage.removeItem('orderID');
    window.localStorage.removeItem('assignFile');
    window.localStorage.removeItem('editor_id');
    window.localStorage.setItem('orderID', orderID);
    window.localStorage.setItem('assignFile', file);
    window.localStorage.setItem('editor_id', editor_id);
}

//translator assign document to himself;
function translatorAssign(id){
    $.ajax({
        url: "/api/translator-assign-to-me/"+id,
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:"json",
        data: {
            orderID: window.localStorage.getItem('orderID'),
            file: window.localStorage.getItem('assignFile'),
            translator_id: id
        },
        success: function(data){
            location.reload();
        }
    });
}


//send to editor
function sendToEditor(){
    var id = window.localStorage.getItem('orderID');
    $.ajax({
        url: "/api/send-to-editor/"+id,
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:"json",
        data: {
            orderID: window.localStorage.getItem('orderID'),
            file: window.localStorage.getItem('assignFile'),
        },
        success: function(data){
            location.reload();
        }
    });
}

//editor assign document to himself;
function editorAssign(){
    var id = window.localStorage.getItem('editor_id');
    $.ajax({
        url: "/api/editor-assign-to-me/"+id,
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:"json",
        data: {
            orderID: window.localStorage.getItem('orderID'),
            file: window.localStorage.getItem('assignFile'),
            editor_id: id
        },
        success: function(data){
            location.reload();
        }
    });
}


function sendToClient(){
    var id = window.localStorage.getItem('orderID');
    $.ajax({
        url: "/api/send-to-client/"+id,
        type: "PUT",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType:"json",
        data: {
            orderID: window.localStorage.getItem('orderID'),
            file: window.localStorage.getItem('assignFile'),
        },
        success: function(data){
            location.reload();
        }
    });
}




