{{-- resources/views/layouts/sections/menu/verticalMenu.blade.php --}}

@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\File;
  use Illuminate\Support\Facades\Route;

  $user = Auth::user();
  $menuData = json_decode('{"menu": []}'); // Menú vacío por defecto

  if ($user) {
      $menuFilePath = '';

      if ($user->hasRole('Administrador')) {
          $menuFilePath = base_path('resources/menu/adminMenu.json');
      } elseif ($user->hasRole('Proveedor')) {
          $menuFilePath = base_path('resources/menu/proveedorMenu.json');
      } elseif ($user->hasRole('Repartidor')) {
          $menuFilePath = base_path('resources/menu/repartidorMenu.json');
      } elseif ($user->hasRole('Cliente_Corporativo')) {
          $menuFilePath = base_path('resources/menu/clienteMenu.json');
      }

      if ($menuFilePath && File::exists($menuFilePath)) {
          $menuData = json_decode(File::get($menuFilePath));
      }
  }
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  @if (!isset($navbarFull))
    <div class="app-brand demo">
      <a href="{{ url('/') }}" class="app-brand-link">
        <span class="app-brand-logo demo">@include('_partials.macros')</span>
        <span class="app-brand-text demo menu-text fw-bold ms-3">{{ config('variables.templateName') }}</span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
        <i class="ti menu-toggle-icon d-none d-xl-block"></i>
        <i class="ti ti-x d-block d-xl-none"></i>
      </a>
    </div>
  @endif
  <div class="menu-inner-shadow"></div>
  <ul class="menu-inner py-1">
    @if (isset($menuData->menu))
      @foreach ($menuData->menu as $menu)
        {{-- adding active and open class if child is active --}}

        {{-- menu headers --}}
        @if (isset($menu->menuHeader))
          <li class="menu-header small">
            <span class="menu-header-text">{{ __($menu->menuHeader) }}</span>
          </li>
        @else
          {{-- active menu method --}}
          @php
            $activeClass = null;
            $currentRouteName = Route::currentRouteName();

            if (isset($menu->slug) && $currentRouteName === $menu->slug) {
                $activeClass = 'active';
            } elseif (isset($menu->submenu)) {
                if (gettype($menu->slug) === 'array') {
                    foreach ($menu->slug as $slug) {
                        if (str_contains($currentRouteName, $slug) and strpos($currentRouteName, $slug) === 0) {
                            $activeClass = 'active open';
                        }
                    }
                } else {
                    if (str_contains($currentRouteName, $menu->slug) and strpos($currentRouteName, $menu->slug) === 0) {
                        $activeClass = 'active open';
                    }
                }
            }
          @endphp

          {{-- main menu --}}
          <li class="menu-item {{ $activeClass }}">
            <a href="{{ isset($menu->url) ? url($menu->url) : 'javascript:void(0);' }}"
              class="{{ isset($menu->submenu) ? 'menu-link menu-toggle' : 'menu-link' }}"
              @if (isset($menu->target) and !empty($menu->target)) target="_blank" @endif>
              @isset($menu->icon)
                <i class="{{ $menu->icon }}"></i>
              @endisset
              <div>{{ isset($menu->name) ? __($menu->name) : '' }}</div>
              @isset($menu->badge)
                <div class="badge bg-{{ $menu->badge[0] }} rounded-pill ms-auto">{{ $menu->badge[1] }}</div>
              @endisset
            </a>

            {{-- submenu --}}
            @isset($menu->submenu)
              @include('layouts.sections.menu.submenu', ['menu' => $menu->submenu])
            @endisset
          </li>
        @endif
      @endforeach
    @endif
  </ul>
</aside>
