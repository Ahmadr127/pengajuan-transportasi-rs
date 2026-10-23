@props(['items', 'itemClass' => 'user-nav-item', 'navClass' => 'user-bottom-nav d-md-none'])
<nav class="{{ $navClass }}">
    @foreach($items as $menu)
        <a href="{{ $menu->href() }}" class="{{ $itemClass }} {{ $menu->isActive() ? 'active' : '' }}">
            <x-menu-icon :name="$menu->icon" />
            <span>{{ $menu->label }}</span>
        </a>
    @endforeach
</nav>
