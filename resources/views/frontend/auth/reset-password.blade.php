@extends('layouts.frontend')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Reset Password</h4>
                    </div>
                    <div class="card-body">

                        <!-- Validation Errors -->
                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <!-- Password Reset Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email Address -->
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input id="email" class="form-control" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                            </div>

                            <!-- Password -->
                            <div class="form-group mt-4">
                                <label for="password">Password</label>
                                <input id="password" class="form-control" type="password" name="password" required>
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group mt-4">
                                <label for="password_confirmation">Confirm Password</label>
                                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
