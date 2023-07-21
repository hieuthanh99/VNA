<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- <x-application-logo class="block h-9 w-auto fill-current text-gray-800" /> -->
                        <svg width="69" height="41" viewBox="0 0 69 41" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M41.8754 32.3469C43.7982 30.9828 47.7641 28.02 48.0946 19.2048C48.1396 18.5888 48.2898 18.5007 48.2898 18.5007C48.38 18.1487 49.1611 16.6966 51.6248 15.3912C57.9041 12.0764 63.1619 12.5751 64.0332 12.7658C65.28 13.0591 65.25 14.1152 64.6641 14.3938C61.9901 15.7139 60.533 19.6155 59.4814 21.5956C58.1595 24.0891 56.4019 26.6412 54.0884 28.6947C51.2492 31.2321 48.2748 33.6816 43.1072 34.8257C42.972 34.855 42.5664 34.943 42.5814 35.1924C42.5964 35.5884 44.0536 35.4564 44.4141 35.4124C47.6138 34.855 51.775 32.1122 54.1185 29.9561C55.0799 29.0467 55.6808 29.0173 55.9061 28.9733C57.2882 28.7387 60.4278 28.7827 63.8379 31.0415C65.0547 31.8335 66.1964 32.8602 66.9174 33.7843C67.5183 34.5323 68.3445 35.559 68.4347 36.1164C68.5248 36.6591 67.8638 37.3045 66.8724 36.9085C66.3466 36.6884 65.6556 36.5124 64.8143 36.5418C62.4108 36.4684 54.7644 40.0326 47.5988 40.0326C39.0962 40.0326 34.8299 36.4978 34.214 36.1751C33.6131 36.4831 29.3468 40.0326 20.8442 40.0326C13.6786 40.0326 6.03228 36.4684 3.62873 36.5418C2.78748 36.5124 2.09646 36.6738 1.57068 36.9085C0.564189 37.3191 -0.0817676 36.6738 0.00836584 36.1164C0.0984993 35.559 0.924723 34.5323 1.51059 33.7989C2.23166 32.8749 3.37335 31.8628 4.59015 31.0561C7.98517 28.7973 11.1248 28.7533 12.5219 28.988C12.7322 29.032 13.3481 29.0467 14.3095 29.9707C16.623 32.1415 20.7991 34.8697 24.0139 35.427C24.3594 35.471 25.8166 35.603 25.8316 35.207C25.8316 34.9577 25.441 34.8697 25.3058 34.8403C20.1532 33.6963 17.1788 31.2468 14.3396 28.7093C12.0262 26.6559 10.2686 24.1037 8.9466 21.6103C7.89504 19.6301 6.43788 15.7286 3.76393 14.4232C3.16304 14.1445 3.14801 13.0738 4.39486 12.7951C5.26615 12.5897 10.5239 12.1057 16.7882 15.4059C19.2519 16.7113 20.048 18.1634 20.1382 18.5301C20.1382 18.5301 20.2884 18.6181 20.3184 19.2341C20.6489 28.0493 24.6298 31.0121 26.5527 32.3762C27.3338 32.9629 28.3103 33.2122 28.5656 32.9482C28.6858 32.8162 28.3704 32.5375 28.16 32.4055C22.6168 28.8413 21.5503 24.1917 21.2498 19.2048C20.9644 14.2472 23.0976 9.93493 24.4496 6.41473C24.6448 5.91604 25.426 3.65724 24.8401 1.72113C24.4496 0.415726 24.8702 0.107708 25.5912 0.00503579C26.3424 -0.0976367 27.469 1.39845 27.8596 1.86781C28.16 2.21983 29.1665 3.34922 30.6537 6.26806C33.6131 12.003 33.6432 18.3541 33.6432 19.0874C33.5831 23.2237 32.8169 23.473 30.7889 24.4264C29.1064 25.0718 28.1751 26.3772 28.1751 27.6679C28.1751 28.9587 29.1665 29.9414 30.9842 30.6894C31.1044 30.7188 31.3147 30.7921 31.4199 30.8068C32.216 30.9975 32.8921 30.8214 32.8921 30.5281C32.8921 30.2641 32.0959 30.1614 32.0959 30.1614C30.3232 29.7361 29.0914 28.7533 29.0914 27.6533C29.0914 26.1425 31.2696 24.6024 34.0488 24.6024H34.3492C37.1283 24.6024 39.2915 26.1278 39.2915 27.6533C39.2915 28.768 38.0597 29.7361 36.3021 30.1614C36.3021 30.1614 35.5059 30.2641 35.5059 30.5281C35.5059 30.8214 36.1669 30.9975 36.9631 30.8068C37.0682 30.7921 37.2635 30.7188 37.3987 30.6748C39.2164 29.9267 40.2079 28.944 40.2079 27.6533C40.2079 26.3625 39.2765 25.0718 37.594 24.4264C35.581 23.473 34.8149 23.209 34.7548 19.0874C34.7548 18.3687 34.7849 12.0177 37.7292 6.28272C39.2314 3.37856 40.2229 2.23449 40.5384 1.88247C40.929 1.41312 42.0556 -0.0829688 42.8067 0.0197036C43.5428 0.137044 43.9484 0.430394 43.5578 1.7358C42.972 3.65724 43.7531 5.9307 43.9484 6.4294C45.2854 9.9496 47.4486 14.2618 47.1482 19.2195C46.8627 24.2064 45.7811 28.856 40.2379 32.4055" fill="#DF9F20"/>
                        </svg>

                    </a>
                </div>

             
                <!-- Navigation Links -->
                <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                @if(auth()->user()->role === 'staff')
                    <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link :href="route('reports.create')" :active="request()->routeIs('reports.create')">
                            {{ __('Báo Cáo') }}
                        </x-nav-link>
                    </div>
                    {{-- <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.index')">
                            {{ __('Báo Cáo index') }}
                        </x-nav-link>
                    </div> --}}
                @endif
                @if(auth()->user()->role === 'admin')
                <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.index')">
                        {{ __('Phòng Ban') }}
                    </x-nav-link>
                </div>
                <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('centers.index')" :active="request()->routeIs('centers.index')">
                        {{ __('Báo Cáo Tổng') }}
                    </x-nav-link>
                </div>
                <div class="nav-item hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.index')">
                        {{ __('Tổ Chức') }}
                    </x-nav-link>
                </div>
                @endif

            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="name-user inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="name">{{ Auth::user()->name }}</div>

                            <div class="dropdown-user ml-1" style= "color: #fff;">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
<style>
    .name-user {
        background-color: unset;
    }
    
    .name {
        color: #fff;
    }
    .bg-white .name-user {
        background-color: unset;
    }
    .name:hover {
        opacity: 0.5;
    }

    .dropdown-user {
        color: #fff;
    }

    .dropdown-user:hover {
        opacity: 0.5;
    }

    nav {
        background-image: linear-gradient(195deg,#006885 0%,#006885 100%);
    }

    .bg-white.name-user {
        background-color: unset !important;
        border: none !important;
    }

    .nav-item a {
        color: #fff;
        text-decoration: none;
    }

    .nav-item a:hover{
        opacity: 0.5;
        color: #fff;
    }
</style>