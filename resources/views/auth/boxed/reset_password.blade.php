@extends('auth.layouts.authentication')

@section('content')
    <!-- aiz-main-wrapper -->
    <div class="aiz-main-wrapper d-flex flex-column justify-content-md-center my-login">
        <section class="overflow-hidden">
            <div class="row">
                <div class="col-xxl-6 col-xl-9 col-lg-10 col-md-7 mx-auto py-lg-4">
                    <div class="card shadow-none rounded-2 border-1">
                        <div class="row no-gutters">
                            <!-- Left Side Image-->
                            <div class="col-lg-6">
                                <img loading="lazy" src="{{ uploaded_asset(get_setting('password_reset_page_image')) }}"
                                    alt="{{ translate('Password Reset Page Image') }}" class="img-fit h-100 rounded-2">
                            </div>

                            <div class="col-lg-6 p-4 p-lg-3 d-flex flex-column justify-content-center rounded-2 border right-content"
                                style="height: auto;">
                                <!-- Site Icon -->
                                <a href="{{ url()->previous() }}" class="size-70px mb-1 mx-auto">
                                    <img loading="lazy" src="{{ uploaded_asset(get_setting('site_icon')) }}"
                                        alt="{{ translate('Site Icon') }}" class="img-fluid h-100">
                                </a>

                                <!-- Titles -->
                                <div class="text-center">
                                    <h1 class="fs-20 fs-md-20 fw-700 text-primary" style="text-transform: uppercase;">
                                        {{ translate('Reset Password') }}</h1>
                                    <h5 class="fs-14 fw-400 text-dark">
                                        {{ translate('Enter your email address and new password and confirm password.') }}
                                    </h5>
                                </div>

                                <!-- Reset password form -->
                                <div class="pt-3">
                                    <div class="">
                                        <form class="was-validated form-default" role="form"
                                            action="{{ route('password.update') }}" method="POST">
                                            @csrf

                                            <!-- Email -->
                                            <div class="form-group">
                                                <input id="email" type="email"
                                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                    name="email" value="{{ $email ?? old('email') }}"
                                                    placeholder="{{ translate('Email') }}" required autofocus>

                                                @if ($errors->has('email'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Code -->
                                            <div class="form-group">
                                                <input id="code" type="text"
                                                    class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}"
                                                    name="code" value="{{ $email ?? old('code') }}"
                                                    placeholder="{{ translate('Code') }}" required autofocus>

                                                @if ($errors->has('code'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('code') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Password -->
                                            <div class="form-group">
                                                <input id="password" type="password"
                                                    class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                    name="password" placeholder="{{ translate('New Password') }}" required>

                                                @if ($errors->has('password'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Password Confirmation-->

                                            <div class="form-group">
                                                <input id="password-confirm" type="password" class="form-control"
                                                    name="password_confirmation"
                                                    placeholder="{{ translate('Reset Password') }}" required>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mb-4 mt-4">
                                                <button type="submit"
                                                    class="btn btn-primary btn-block fw-700 fs-14 rounded-0">{{ translate('Reset Password') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
