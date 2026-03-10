<div class="container-fluid mt-5">
    <h3 class="card-title">{{ trans('Comment Section') }}</h2>
    <div id="comment-section" data-url="{{ $commentUrl }}">
       @foreach ($comments as $comment)
           @include('comment::inc.comment',['comment'=>$comment])
       @endforeach
    </div>
    <form id="comment-form" class="mt-3">
        <div class="mb-3">
            <textarea class="form-control" id="comment-input" rows="3" placeholder="Write a comment..." required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">{{ trans('Post Comment') }}</button>
    </form>
</div>
<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">{{ trans('Reply to Comment') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="reply-input" rows="3" placeholder="Write your reply..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('Close') }}</button>
                <button type="button" class="btn btn-primary" id="submit-reply">{{ trans('Submit Reply') }}</button>
            </div>
        </div>
    </div>
</div>
@push('js')
<script src="{{ asset('control') }}/js/custom/comments/scripts.js"></script>
@endpush
@push('css')
<link href="{{ asset('control') }}/js/custom/comments/style.css" rel="stylesheet" type="text/css" />

@endpush