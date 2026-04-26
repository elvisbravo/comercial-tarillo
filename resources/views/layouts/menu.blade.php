<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>

                <li>
                    <a href="{{url('home')}}">
                        <i data-feather="home"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>

                @foreach(App\Permisos::getMenu() as $item)
                    <li>
                        @if($item->submodulos->count() > 0)
                            <a href="javascript: void(0);" class="has-arrow">
                                @if(\Illuminate\Support\Str::contains($item->icon, 'fa-') || \Illuminate\Support\Str::contains($item->icon, 'fas ') || \Illuminate\Support\Str::contains($item->icon, 'fab '))
                                    <i class="{{ $item->icon }}"></i>
                                @else
                                    <i data-feather="{{ $item->icon }}"></i>
                                @endif
                                <span data-key="t-{{ $item->id }}">{{ $item->name }}</span>
                            </a>
                            <ul class="sub-menu" aria-expanded="false">
                                @foreach($item->submodulos as $sub)
                                    <li><a href="{{ $sub->url != '#' ? url($sub->url) : 'javascript:void(0);' }}" data-key="t-sub-{{ $sub->id }}">{{ $sub->name }}</a></li>
                                @endforeach
                            </ul>
                        @else
                            <a href="{{ $item->url != '#' ? url($item->url) : 'javascript:void(0);' }}">
                                @if(\Illuminate\Support\Str::contains($item->icon, 'fa-') || \Illuminate\Support\Str::contains($item->icon, 'fas ') || \Illuminate\Support\Str::contains($item->icon, 'fab '))
                                    <i class="{{ $item->icon }}"></i>
                                @else
                                    <i data-feather="{{ $item->icon }}"></i>
                                @endif
                                <span data-key="t-{{ $item->id }}">{{ $item->name }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach

            </ul>

        </div>
        <!-- Sidebar -->
    </div>
</div>