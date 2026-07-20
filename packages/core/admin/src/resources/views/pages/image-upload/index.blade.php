@extends('admin::layouts.dashboard')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">أدوات مساعدة /</span> رفع الصور</h4>

    <div class="row">
        <!-- قسم نموذج الرفع -->
        <div class="col-md-5 mb-4">
            <div class="card h-100">
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
                        <div class="mb-4">
                            <label class="form-label" for="image">اختر صورة من جهازك</label>
                            <input class="form-control" type="file" id="image" name="image" required accept="image/*">
                            @error('image')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 btn-lg">
                            <i class="fa fa-cloud-upload-alt me-2"></i>
                            <span id="btnText">رفع الصورة الآن</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm d-none mx-2" role="status" aria-hidden="true"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- قسم عرض النتيجة -->
        <div class="col-md-7 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">نتيجة الرفع</h5>
                </div>
                <div class="card-body d-flex flex-column justify-content-center align-items-center text-center">
                    @if(session('image_url'))
                        <div class="w-100">
                            <div class="mb-4 border p-2 rounded bg-light">
                                <img src="{{ session('image_url') }}" alt="Uploaded Image" class="img-fluid rounded shadow-sm" style="max-height: 350px; object-fit: contain;">
                            </div>
                            <h6 class="text-success fw-bold mb-3"><i class="fa fa-check-circle me-1"></i> تم رفع الصورة بنجاح!</h6>
                            
                            <label class="form-label text-start w-100">الرابط المباشر للصورة:</label>
                            <div class="input-group input-group-lg">
                                <input type="text" class="form-control text-start" dir="ltr" id="imageUrl" value="{{ session('image_url') }}" readonly>
                                <button class="btn btn-outline-primary px-4" type="button" onclick="copyToClipboard()">
                                    <i class="fa fa-copy me-1"></i> نسخ الرابط
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="text-muted p-5">
                            <i class="fa fa-image fa-4x mb-3 text-light"></i>
                            <h5>لم يتم رفع أي صورة بعد</h5>
                            <p>الرابط المباشر سيظهر هنا فور الانتهاء من الرفع.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- قسم معرض الصور المرفوعة -->
    @if(isset($images) && count($images) > 0)
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="fa fa-images me-2"></i> الصور المرفوعة سابقاً</h5>
            <div class="card">
                <div class="card-body">
                    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
                        @foreach($images as $img)
                            <div class="col">
                                <div class="card h-100 border shadow-sm">
                                    <div class="p-2 bg-light text-center" style="height: 150px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ $img['url'] }}" alt="Image" class="img-fluid rounded" style="max-height: 140px; object-fit: contain;">
                                    </div>
                                    <div class="card-body p-3 text-center">
                                        <div class="input-group input-group-sm">
                                            <input type="text" class="form-control text-start" dir="ltr" id="img_{{ $loop->index }}" value="{{ $img['url'] }}" readonly>
                                            <button class="btn btn-outline-primary" type="button" onclick="copySpecificUrl('img_{{ $loop->index }}')">
                                                <i class="fa fa-copy"></i> نسخ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
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
        if (copyText) {
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                toastr.success('تم نسخ الرابط بنجاح');
            }).catch(err => {
                toastr.error('فشل في نسخ الرابط');
            });
        }
    }

    function copySpecificUrl(elementId) {
        var copyText = document.getElementById(elementId);
        if (copyText) {
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(copyText.value).then(() => {
                toastr.success('تم نسخ الرابط بنجاح');
            }).catch(err => {
                toastr.error('فشل في نسخ الرابط');
            });
        }
    }
</script>
@endpush
@endsection
