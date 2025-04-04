@extends('layouts.login')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">

                <div class="card bg-pattern" style="opacity: 0.95">

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <a href="/">
                                <span><img src="{{ env('APP_PATH') }}/images/app_logo.png" width="80%"></span>
                            </a>
                            <p class="text-muted mb-4 mt-3">Enter username and password to login</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group">
                                <label for="email">Username</label>
                                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            </div>

                            @if ($errors->has('email'))
                                <span class="help-block text-danger">
                                    {{ $errors->first('email') }}
                                </span>
                            @endif

                            <div class="form-group mb-3 mt-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            </div>

                            <div class="form-group mt-4 text-center">
                                <button class="btn btn-blue btn-block" type="submit"> Log In </button>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-center">
                        <a href="{{ route('reset.password') }}" type="button" class="btn btn-warning waves-effect waves-light" style="opacity: .8">Forgot your password?</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
