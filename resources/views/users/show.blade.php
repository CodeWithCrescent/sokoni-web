@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">User Details</h1>
            <p class="mt-1 text-gray-400">View user information and activity</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition">
                Edit User
            </a>
            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-md hover:bg-slate-500 transition">
                Back to Users
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <div class="flex items-start space-x-6">
            <!-- Avatar -->
            <div class="w-24 h-24 rounded-full bg-blue-500/20 flex items-center justify-center text-blue-400 text-3xl font-bold flex-shrink-0">
                {{ substr($user->name, 0, 1) }}
            </div>
            
            <!-- User Info -->
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-2">
                    <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                    @php
                        $roleColors = [
                            'admin' => 'bg-purple-500/20 text-purple-400 border-purple-500/50',
                            'vendor' => 'bg-blue-500/20 text-blue-400 border-blue-500/50',
                            'customer' => 'bg-green-500/20 text-green-400 border-green-500/50',
                            'delivery' => 'bg-amber-500/20 text-amber-400 border-amber-500/50',
                        ];
                        $color = $roleColors[$user->role->name] ?? 'bg-gray-500/20 text-gray-400 border-gray-500/50';
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $color }}">
                        {{ ucfirst($user->role->name) }}
                    </span>
                    @if($user->email_verified_at)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-500/20 text-green-400 border border-green-500/50">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                    @endif
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $user->email }}
                    </div>
                    <div class="flex items-center text-gray-300">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Joined {{ $user->created_at->format('F d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Account Information -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Account Information</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-400">User ID</dt>
                    <dd class="text-white font-medium">#{{ $user->id }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-400">Email Status</dt>
                    <dd class="text-white font-medium">
                        @if($user->email_verified_at)
                            Verified on {{ $user->email_verified_at->format('M d, Y') }}
                        @else
                            <span class="text-amber-400">Not Verified</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-400">Role</dt>
                    <dd class="text-white font-medium">{{ ucfirst($user->role->name) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-400">Last Updated</dt>
                    <dd class="text-white font-medium">{{ $user->updated_at->diffForHumans() }}</dd>
                </div>
            </dl>
        </div>

        <!-- Activity Summary -->
        <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Activity Summary</h3>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm text-gray-400">Account Age</dt>
                    <dd class="text-white font-medium">{{ $user->created_at->diffForHumans(null, true) }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-400">Created At</dt>
                    <dd class="text-white font-medium">{{ $user->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-400">Last Login</dt>
                    <dd class="text-white font-medium">
                        @if($user->last_login_at ?? false)
                            {{ $user->last_login_at->diffForHumans() }}
                        @else
                            <span class="text-gray-500">Never</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Profile Status -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Profile Status</h3>
        
        @php
            $hasProfile = false;
            $profileLink = null;
            $profileType = null;
            
            switch($user->role->name) {
                case 'vendor':
                    $hasProfile = $user->vendor !== null;
                    $profileType = 'Vendor';
                    $profileLink = $hasProfile ? route('vendors.show', $user->vendor) : route('vendors.create');
                    break;
                case 'customer':
                    $hasProfile = $user->customer !== null;
                    $profileType = 'Customer';
                    $profileLink = $hasProfile ? route('customers.show', $user->customer) : route('customers.create');
                    break;
                case 'delivery':
                    $hasProfile = $user->deliveryPersonnel !== null;
                    $profileType = 'Delivery Personnel';
                    $profileLink = $hasProfile ? route('delivery-personnel.show', $user->deliveryPersonnel) : route('delivery-personnel.create');
                    break;
            }
        @endphp
        
        @if(in_array($user->role->name, ['vendor', 'customer', 'delivery']))
            @if($hasProfile)
                <div class="flex items-center justify-between p-4 bg-green-500/10 border border-green-500/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-white font-medium">{{ $profileType }} Profile Active</p>
                            <p class="text-sm text-gray-400">This user has a complete profile</p>
                        </div>
                    </div>
                    <a href="{{ $profileLink }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-500 transition">
                        View Profile
                    </a>
                </div>
            @else
                <div class="flex items-center justify-between p-4 bg-amber-500/10 border border-amber-500/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <p class="text-white font-medium">{{ $profileType }} Profile Missing</p>
                            <p class="text-sm text-gray-400">Create a {{ strtolower($profileType) }} profile for this user</p>
                        </div>
                    </div>
                    <a href="{{ $profileLink }}" class="px-4 py-2 bg-amber-600 text-white rounded-md hover:bg-amber-500 transition">
                        Create Profile
                    </a>
                </div>
            @endif
        @else
            <div class="flex items-center p-4 bg-blue-500/10 border border-blue-500/50 rounded-lg">
                <svg class="w-6 h-6 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-white font-medium">Admin User</p>
                    <p class="text-sm text-gray-400">Admin users do not require additional profiles</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-slate-800/40 backdrop-blur-sm rounded-lg border border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('users.edit', $user) }}" class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-blue-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Edit User</span>
            </a>
            
            @if($user->id !== auth()->id())
            <button onclick="if(confirm('Are you sure?')) document.getElementById('deleteForm').submit();" 
                class="flex flex-col items-center justify-center p-4 bg-slate-700/50 rounded-lg hover:bg-slate-700 transition-colors group">
                <svg class="w-8 h-8 text-red-400 mb-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span class="text-sm text-gray-300 group-hover:text-white">Delete User</span>
            </button>
            
            <form id="deleteForm" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
