@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Edit User</h1>
            <p class="mt-1 text-gray-400">Update user information</p>
        </div>
        <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500 transition">
            Back to Users
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-300 mb-2">
                    Full Name <span class="text-red-400">*</span>
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-2">
                    Email Address <span class="text-red-400">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-2">
                    New Password
                </label>
                <input type="password" id="password" name="password"
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-400">Leave blank to keep current password</p>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Confirmation -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-2">
                    Confirm New Password
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Role -->
            <div class="mb-6">
                <label for="role_id" class="block text-sm font-medium text-gray-300 mb-2">
                    User Role <span class="text-red-400">*</span>
                </label>
                <select id="role_id" name="role_id" required
                    class="w-full px-4 py-2 bg-slate-700 border border-slate-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role_id') border-red-500 @enderror">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Account Info -->
            <div class="mb-6 p-4 bg-slate-700/50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-300 mb-3">Account Information</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Account Status:</span>
                        @if($user->email_verified_at)
                            <span class="ml-2 text-green-400">Verified</span>
                        @else
                            <span class="ml-2 text-amber-400">Unverified</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-gray-400">Joined:</span>
                        <span class="ml-2 text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Last Updated:</span>
                        <span class="ml-2 text-white">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                <div>
                    @if($user->id !== auth()->id())
                    <button type="button" onclick="document.getElementById('deleteForm').submit();" 
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-500 transition">
                        Delete User
                    </button>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('users.index') }}" class="px-6 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition">
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($user->id !== auth()->id())
    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
</div>

<script>
document.getElementById('deleteForm')?.addEventListener('submit', function(e) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        e.preventDefault();
    }
});
</script>
@endsection
