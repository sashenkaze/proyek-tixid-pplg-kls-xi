@extends('templates.app')

@section('content')
    <form class="w-75 d-block mx-auto my-5" method="POST" action="{{ route('signup') }}">
        {{-- column grid layout with text inputs for the first and last names --}}
        {{-- csrf = token sebagai kunci untuk data form bisa diakses sevrer/controller --}}
        @csrf
        <div class="row mb-4">
            <div class="col">
                <div data-mdb-input-init class="form-outline">
                    <input type="text" id="form3Example1" class="form-control form-control-lg @error('first_name') is-invalid @enderror" name="first_name" value="{{old('first_name')}}"/>
                    <label class="form-label" for="form3Example1">First name</label>
                </div>
                @error('first_name')
                    <div class="text-danger">{{ $message }}</div>

                @enderror
            </div>
            <div class="col">
                <div data-mdb-input-init class="form-outline">
                    <input type="text" id="form3Example2" class="form-control form-control-lg @error('last_name') is-invalid @enderror" name="last_name" value="{{old('last_name')}}"/>
                    <label class="form-label" for="form3Example2">Last name</label>
                </div>
                @error('last_name')
                    <div class="text-danger">{{ $message }}</div>

                @enderror
            </div>
        </div>

        <!-- Email input -->
        @error('email')
            <div class="text-danger">{{ $message }}</div>

        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="email" id="form3Example3" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email" value="{{old('email')}}"/>
            <label class="form-label" for="form3Example3">Email</label>
        </div>

        <!-- Password input -->
        @error('password')
            <div class="text-danger">{{ $message }}</div>

        @enderror
        <div data-mdb-input-init class="form-outline mb-4">
            <input type="password" id="form3Example4" class="form-control form-control-lg @error('password') is-invalid @enderror" name="password" value="{{old('password')}}"/>
            <label class="form-label" for="form3Example4">Password</label>
        </div>

        <!-- Submit button -->
        <button data-mdb-ripple-init type="submit" class="btn btn-primary btn-block mb-4 btn-lg">Sign up</button>

        <!-- Register buttons -->
        <div class="text-center">
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
