@extends('layouts.admin_app')
@section('content')
    <div class="p-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-6">
            {{ __('Profile Settings') }}
        </h2>

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Update Profile Info -->
            <div class="p-6 bg-white shadow rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 bg-white shadow rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 bg-white shadow rounded-xl border border-red-200">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
