<div class="max-w-4xl mx-auto mt-6">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <div class="flex items-center gap-3 mb-8 border-b pb-4">
                    <img class="w-10 h-10" src="{{ asset('img/teamwork.png') }}" alt="Billing">
                    <h1 class="text-2xl font-bold text-gray-800">Register New User</h1>
                </div>

                <form method="POST" action="{{ route('admin.register-user.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name or Username')" />
                        <x-text-input id="name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" :value="__('User Role')" />
                        <select id="role" name="role" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-700">
                           <option value="" disabled selected>Select a role</option>
                            <option value="admin">Admin</option>
                            <option value="billing_clerk">Billing Clerk</option>
                            <option value="receivable_clerk">Receivable Clerk</option>

                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end pt-4 border-t mt-6">
                        <x-primary-button class="ms-4 px-6 py-2.5 text-sm rounded-lg">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>






            </div>
        </div>