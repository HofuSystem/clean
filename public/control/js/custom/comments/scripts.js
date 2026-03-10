$(document).ready(function() {
    let commentId = 0;
    let currentParentId = null;

    // Function to add a new comment or reply
    function addComment(commentId,commentText, commenterName, commenterEmail, commenterAvatar, parentId = null) {
        const avatarUrl = commenterAvatar; // Default avatar if none provided
        const commentHtml = `
            <div class="card mb-3" id="comment-${commentId}" data-parent="${parentId}">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <img src="${avatarUrl}" alt="${commenterName}" class="avatar me-2">
                        <div>
                            <strong>${commenterName}</strong>
                            <small class="text-muted">${commenterEmail}</small>
                        </div>
                    </div>
                    <p>${commentText}</p>
                    <button class="btn btn-sm btn-outline-secondary reply-btn" data-comment-id="${commentId}" data-bs-toggle="modal" data-bs-target="#replyModal">
                        <i class="fas fa-reply"></i> Reply
                    </button>
                    <div class="replies mt-3"></div>
                </div>
            </div>
        `;

        if (parentId) {
            $(`#comment-${parentId} > .card-body  >  .replies`).append(commentHtml);
        } else {
            $('#comment-section').append(commentHtml);
        }
    }

    // Handle form submission for new comments
    $('#comment-form').on('submit', function(e) {
        e.preventDefault();
        const commentText   = $('#comment-input').val().trim();
        const commentingUrl = $('#comment-section').data('url');
        $.ajax({
            type: "POST",
            url: commentingUrl,
            data: {content:commentText},
            dataType: "json",
            success: function (response) {
                if(response.status){
                    toastr.success(response.message)
                    $('#comment-input').val(''); // Clear form
                    addComment(response.comment.id,response.comment.content, response.comment.user.name, response.comment.user.email, response.comment.user.avatar) 
                }else{
                    toastr.error(response.message)
                    $.each(response.errors, function (key, messages) {
                        $.each(messages, function (index , message) {
                            toastr.error(message,key);
                        });
                    });
                }
            }
        });
    });

    // Handle reply button click to set the current parent ID
    $('#comment-section').on('click', '.reply-btn', function() {
        currentParentId = $(this).data('comment-id');
        
    });

    // Handle reply submission in the modal
    $('#submit-reply').on('click', function() {
        const commentText   = $('#reply-input').val().trim();
        const commentingUrl = $('#comment-section').data('url');
        $.ajax({
            type: "POST",
            url: commentingUrl,
            data: {content:commentText,parent_id:currentParentId},
            dataType: "json",
            success: function (response) {
                if(response.status){
                    toastr.success(response.message)
                    $('#reply-input').val(''); // Clear form                        
                    $('#replyModal').modal('hide'); // Clear form                        
                    addComment(response.comment.id,response.comment.content, response.comment.user.name, response.comment.user.email, response.comment.user.avatar,response.comment.parent_id) 

                }else{
                    toastr.error(response.message)
                    $.each(response.errors, function (key, messages) {
                        $.each(messages, function (index , message) {
                            toastr.error(message,key);
                        });
                    });
                }
            }
        });
     
    });
});
