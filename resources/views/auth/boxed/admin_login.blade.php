<!-- aiz-main-wrapper -->
<div class="aiz-main-wrapper d-flex flex-column justify-content-md-center my-login">
    <section class=" overflow-hidden">
        <div class="row">
            <div class="col-xxl-6 col-xl-9 col-lg-10 col-md-7 mx-auto py-lg-4">
                <div class="card shadow-none rounded-2 border-1">
                    <div class="row no-gutters">
                        <!-- Left Side Image-->
                        <div class="col-lg-6">
                            <img loading="lazy" src="{{ uploaded_asset(get_setting('admin_login_page_image')) }}"
                                alt="{{ translate('Admin Login Page Image') }}" class="img-fit h-100 rounded-2">
                        </div>

                        <div class="col-lg-6 p-4 p-lg-3 d-flex flex-column justify-content-center rounded-2 border right-content"
                            style="height: auto;">
                            <!-- Site Icon -->
                            <a href="{{ url()->previous() }}" class="size-70px mb-3 mx-auto">
                                <img loading="lazy" src="{{ uploaded_asset(get_setting('site_icon')) }}"
                                    alt="{{ translate('Site Icon') }}" class="img-fluid h-100">
                            </a>

                            <!-- Titles -->
                            <div class="text-center">
                                <h1 class="fs-20 fs-md-20 fw-700 text-primary" style="text-transform: uppercase;">
                                    {{ translate('Welcome to') }} {{ env('APP_NAME') }}</h1>
                                <h5 class="fs-14 fw-400 text-dark">{{ translate('Login to your account') }}</h5>
                            </div>

                            <!-- Login form -->
                            <div class="pt-3">
                                <div class="">
                                    <form class="was-validated form-default" role="form"
                                        action="{{ route('login') }}" method="POST">
                                        @csrf

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="email"
                                                class="fs-12 fw-700 text-soft-dark">{{ translate('Email') }}</label>
                                            <input type="email"
                                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }} rounded-0"
                                                value="{{ old('email') }}"
                                                placeholder="{{ translate('johndoe@example.com') }}" name="email"
                                                id="email" autocomplete="off" required>
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <!-- password -->
                                        <div class="form-group">
                                            <label for="password"
                                                class="fs-12 fw-700 text-soft-dark">{{ translate('Password') }}</label>
                                            <div class="position-relative">
                                                <input type="password"
                                                    class="form-control rounded-0 {{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                    placeholder="{{ translate('Password') }}" name="password"
                                                    id="password" required>
                                                <i class="password-toggle las la-2x la-eye"></i>
                                            </div>
                                        </div>

                                        <div class="row mb-2">
                                            <!-- Remember Me -->
                                            <div class="col-6">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" name="remember"
                                                        {{ old('remember') ? 'checked' : '' }}>
                                                    <span
                                                        class="has-transition fs-12 fw-400 text-gray-dark hov-text-primary">{{ translate('Remember Me') }}</span>
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>

                                            <!-- Forgot password -->
                                            <div class="col-6 text-right">
                                                <a href="{{ route('password.request') }}"
                                                    class="text-reset fs-12 fw-400 text-gray-dark hov-text-primary"><u>{{ translate('Forgot password?') }}</u></a>
                                            </div>
                                        </div>

                                        <!-- Submit Button -->
                                        <div class="mb-4 mt-4">
                                            <button type="submit"
                                                class="btn btn-primary btn-block fw-700 fs-14 rounded-0">{{ translate('Login') }}</button>
                                        </div>
                                    </form>

                                    <!-- DEMO MODE -->
                                    @if (env('DEMO_MODE') == 'On')
                                        <div class="mt-4">
                                            <table class="table table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>admin@example.com</td>
                                                        <td>123456</td>
                                                        <td class="text-center">
                                                            <button class="btn btn-info btn-xs"
                                                                onclick="autoFillAdmin()">{{ translate('Copy') }}</button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
