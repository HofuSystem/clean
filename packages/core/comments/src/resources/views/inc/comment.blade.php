<div class="card mb-3" id="comment-{{ $comment->id }}" data-parent="{{ $comment->parent_id }}">
    <div class="card-body">
        <div class="d-flex align-items-center mb-2">
            <img src="{{ $comment->user?->avatarUrl }}" alt="{{ $comment->user?->name }}" class="avatar me-2">
            <div>
                <strong>{{ $comment->user?->fullname }} : </strong>
                <small class="text-muted">{{ $comment->user?->email }}</small>
            </div>
        </div>
        <p>{{ $comment->content }}</p>
        <button class="btn btn-sm btn-outline-secondary reply-btn" data-comment-id="{{ $comment->id }}" data-bs-toggle="modal" data-bs-target="#replyModal">
            <i class="fas fa-reply"></i> {{ trans('Reply') }}
        </button>
        <div class="replies mt-3">
            @foreach ($comment->subComments as $subComment)
                @include('comment::inc.comment',['comment'=>$subComment])
            @endforeach
        </div>
    </div>
</div>

