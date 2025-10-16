@extends('templates.app')

@section('content')
    <form class="w-75 d-block mx-auto my-5" method="POST" action="{{ route('login.auth') }}">
        @csrf
        {{-- nama session seperti nama with() --}}
        @if (Session::get('ok'))
            <div class="alert alert-success">
                {{ Session::get('ok') }}
            </div>
        @endif
        @if (Session::get('error'))
            <div class="alert alert-danger">
                {{ Session::get('error') }}
            </div>
        @endif
        <!-- Email input -->
        @error('email')
            <small class="text-danger">{{$message}}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="form2Example1" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"/>
            <label class="form-label" for="form2Example1">Email address</label>
        </div>

        <!-- Password input -->
        @error('password')
            <small class="text-danger">{{$message}}</small>
        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="password" id="form2Example2" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"/>
            <label class="form-label" for="form2Example2">Password</label>
        </div>

        <!-- 2 column grid layout for inline styling -->
        <div class="row mb-4">
            <div class="d-flex flex-column align-items-center mb-4">
            </div>
                <!-- Simple link -->
                <a href="#!" class="mt-2 text-center d-block">Forgot password?</a>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4 btn-lg">Sign in</button>

        <!-- Register buttons -->
        <div class="text-center">
            <p>Not a member? <a href="{{ route('signup') }}">Register</a></p>
            <p>or sign up with:</p>
            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1 btn-lg">
                <i class="fab fa-facebook-f"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1 btn-lg">
                <i class="fab fa-google"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1 btn-lg">
                <i class="fab fa-twitter"></i>
            </button>

            <button data-mdb-ripple-init type="button" class="btn btn-secondary btn-floating mx-1 btn-lg">
                <i class="fab fa-github"></i>
            </button>
        </div>
    </form>
@endsection
