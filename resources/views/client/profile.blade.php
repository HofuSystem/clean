@extends('layouts.client')

@section('content')
    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">{{ trans('client.edit_account') }}</h1>
            <p class="page-subtitle">
            </p>
        </div>

        <!-- Profile Edit Form -->
        <div class="profile-form-section">
            <form id="profileForm" class="profile-form" action="{{ route('client.profile.update-profile.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <!-- Company Name -->
                <div class="form-group">
                    <label for="logo">{{ trans('client.logo') }}</label>

                    <div class="image-upload-container">
                        <img src="{{ Auth::user()->avatarUrl }}" alt="{{ trans('client.logo') }}" class="image-upload-preview">
                        <input name="logo" type="file" class="form-control" placeholder="{{ trans('client.logo') }}" value="" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <input name="fullname" type="text" class="form-control" placeholder="{{ trans('client.fullname') }}" value="{{ Auth::user()->fullname }}">
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <div class="phone-input-group">
                        <div class="country-code">
                            <span class="flag">
                                <img src="{{ asset('website/client/image/ksa.jpg') }}" width="22" alt="{{ trans('client.saudi_arabia') }}">
                            </span>
                            <span>+966</span>
                        </div>
                        <input name="phone" type="tel" class="phone-input" placeholder="{{ trans('client.phone') }}" required value="{{ Auth::user()->phone }}">
                    </div>
                </div>

               
                <!-- Province -->
                <div class="form-group">
                    <div class="dropdown-group">
                        <select name="city_id" class="form-control dropdown-select">
                            <option value="" disabled selected>{{ trans('client.select_city') }}</option>
                            @foreach ($cities as $city)
                                <option @selected(Auth::user()?->profile?->city_id == $city->id) value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                </div>

                <!-- Area -->
                <div class="form-group">
                    <div class="dropdown-group">
                        <select name="district_id" class="form-control dropdown-select">
                            <option value="" disabled selected>{{ trans('client.select_district') }}</option>
                            @foreach ($districts as $district)
                                <option @selected(Auth::user()?->profile?->district_id == $district->id) value="{{ $district->id }}" data-city-id="{{ $district->city_id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                </div>

                <!-- Detailed Address -->
                <div class="form-group">
                    <input name="address" type="text" class="form-control" placeholder="{{ trans('client.full_address') }}"
                        value="{{ Auth::user()->address }}">
                </div>

                <!-- Email -->
                <div class="form-group">
                    <input name="email" type="email" class="form-control" placeholder="{{ trans('client.email') }}" value="{{ Auth::user()->email }}">
                </div>

                <!-- Save Button -->
                <div class="form-actions">
                    <button type="submit" class="save-btn">{{ trans('client.save_changes') }}</button>
                </div>

            </form>
            <form id="passwordForm" class="profile-form">

              
                <!-- Password Section -->
                <div class="password-section">
                    <h3 class="password-section-title">{{ trans('client.password_data') }}</h3>

                    <div class="form-group ">
                        <input name="current_password" type="password" class="form-control" placeholder="{{ trans('client.current_password') }}">
                    </div>
                    <div class="form-group ">
                        <input name="password" type="password" class="form-control" placeholder="{{ trans('client.new_password') }}">
                    </div>
                    <div class="form-group ">
                        <input name="password_confirmation" type="password" class="form-control"
                            placeholder="{{ trans('client.confirm_new_password') }}">
                    </div>
                </div>


                <!-- Save Button -->
                <div class="form-actions">
                    <button type="submit" class="save-btn">{{ trans('client.save_changes') }}</button>
                </div>

            </form>
        </div>

    </main>
      
@endsection
