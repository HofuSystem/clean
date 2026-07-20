@extends('admin::layouts.dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">أدوات مساعدة /</span> رفع الصور</h4>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">رفع صورة جديدة</h5>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="uploadForm" action="{{ route('dashboard.image-uploader.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label" for="image">اختر صورة</label>
                            <input class="form-control" type="file" id="image" name="image" required accept="image/*">
                            @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" id="submitBtn" class="btn btn-primary w-100">
                            <span id="btnText">رفع الصورة</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>

            @if(session('image_url'))
            <div class="card mt-4">
                <div class="card-body text-center">
                    <h5 class="mb-3">تم رفع الصورة بنجاح!</h5>
                    <img src="{{ session('image_url') }}" alt="Uploaded Image" class="img-fluid rounded mb-3" style="max-height: 250px;">
                    
                    <div class="input-group">
                        <input type="text" class="form-control" id="imageUrl" value="{{ session('image_url') }}" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard()">نسخ الرابط</button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('js')
<script>
    document.getElementById('uploadForm').addEventListener('submit', function() {
        document.getElementById('btnText').innerText = 'جاري الرفع...';
        document.getElementById('btnLoader').classList.remove('d-none');
        document.getElementById('submitBtn').disabled = true;
    });

    function copyToClipboard() {
        var copyText = document.getElementById("imageUrl");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(() => {
            toastr.success('تم نسخ الرابط بنجاح');
        }).catch(err => {
            toastr.error('فشل في نسخ الرابط');
        });
    }
</script>
@endpush
@endsection
