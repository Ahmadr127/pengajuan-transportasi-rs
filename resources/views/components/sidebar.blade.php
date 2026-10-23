@props(['items'])
<nav class="px-3 py-3">
    @foreach($items as $section => $links)
        @if($section)
            <div class="sp-sidebar-section">{{ $section }}</div>
        @endif

        @foreach($links as $menu)
            <a href="{{ $menu->href() }}" @click="sidebarOpen = false"
               class="sp-sidebar-link {{ $menu->isActive() ? 'active' : '' }}">
                <span class="sp-sidebar-icon">
                    <x-menu-icon :name="$menu->icon" />
                </span>
                <span>{{ $menu->label }}</span>
            </a>
        @endforeach
    @endforeach
</nav>
