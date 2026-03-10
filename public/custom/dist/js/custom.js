$('.btn-destroy').click(function(e) {
    e.preventDefault();
    $('#delete-recored').modal('toggle')
    $('#delete-recored form').attr('action', $(this).attr('href'))
});

$('.toggle-status').change(function(e) {
    e.preventDefault();
    let status = $(this).is(':checked') ? 1 : 0;
    let url = $(this).data('url');

    $.ajax({
        type: "POST",
        url: url,
        data: {
            'status': status
        },
        dataType: "json",
        success: function(response) {}
    });
});

$('.btn-editTrans').click(function(e) {
    e.preventDefault();
    let row = $(this).parents('tr');
    let tds = row.find('td');

    tds.each(function() {
        let value = $(this).text();
        $(`#editTrans #${$(this).data('name')}`).val(value);
    });
});
$('.btn-destroy-trans').click(function(e) {
    e.preventDefault();
    let key = $(this).data('key');
    $('#delete-trans-form [name=key]').val(key)
    $('#delete-trans-form').submit();
});

$('[name=avatar]').change(function(e) {
    e.preventDefault();
    if(this.files[0]){
    $(this).parent().find('img').attr('src',window.URL.createObjectURL(this.files[0]));
    }else{
    $(this).parent().find('img').attr('src','');

    }
});

const origXHR = window.XMLHttpRequest;
window.XMLHttpRequest = function() {
    const xhr = new origXHR();
    var oldOpen = xhr.open;
    xhr.open = function(method, url, async, user, pass) {
        oldOpen.call(this, method, url, async, user, pass);
        this.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
        this.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    }
    return xhr;
};

window.ajaxRequest = function({method,url,parameters}) {
    let xhr = new XMLHttpRequest;

    method = method.toUpperCase();
    parameters = "_token={{ csrf_token() }}&" + parameters;

    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            response = this.response;
            try{

                response = JSON.parse(response);
                console.log(response)
                for(let i = 0;i < response.length;) {
                    res = response[i].response;
                    let msg = ++i+") "+res.message;
                    if(res.status) {
                        toastr.success(msg)
                    } else {
                        toastr.error(msg)
                    }
                }
                return response;
            } catch(e) {
                toastr.error("{{ ucfirst(trans('Try again later')) }}");
            }
        }
    }
    if(method == "POST") {
        xhr.open(method, url, true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send(parameters);
    } else {
        xhr.open(method, url+"?"+parameters, true);
        xhr.send();
    }
} // end of ajaxRequest
document.addEventListener("DOMContentLoaded", function() {
    var loaderWrapper = document.querySelector('.loader-rm-wrapper');
    // Simulate a delay to hide loader after some time (you can replace this with your actual loading logic)
    loaderWrapper.classList.add('hide-loader');
});