<div class="card-body row">

    <div class="col-12 mt-5">

        <div class="tab-content mt-3" id="languageTabsContent">
            <div class="tab-pane fade show active" id="title-en" role=tabpanel" aria-labelledby="en-tab">
                <div class="form-group mb-3 col-md-6">
                    <label for="title">{{ trans('title') }}</label>
                    <input type="text" name="translations[en][title]" class="form-control "
                        placeholder="{{ trans('Enter title') }} "
                        value="@isset($item) {{ $item->translate('en')->title }} @endisset" />
                </div>
            </div>
            <div class="tab-pane fade " id="title-ar" role="tabpanel" aria-labelledby="ar-tab">
                <div class="form-group mb-3 col-md-6">
                    <label for="title">{{ trans('title') }}</label>
                    <input type="text" name="translations[ar][title]" class="form-control"
                        placeholder="{{ trans('Enter title') }} "
                        value="@isset($item) {{ $item->translate('ar')->title }} @endisset" />
                </div>
            </div>
        </div>
    </div>
</div>
