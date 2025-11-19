<aside class="sidebar">
        <div class="logo">
            <svg viewBox="0 0 400 100" style="width: 100%; max-width: 200px; height: auto; margin-bottom: 10px;">
                <rect width="400" height="100" fill="#000"/>
                <text x="20" y="70" font-size="55" font-weight="900" fill="white" font-family="Arial, sans-serif" letter-spacing="2">HORUS</text>
                <text x="240" y="70" font-size="55" font-weight="900" fill="#e74c3c" font-family="Arial, sans-serif" letter-spacing="2">LABS</text>
                <text x="20" y="92" font-size="11" fill="white" font-family="Arial, sans-serif" letter-spacing="1">FORENSICS - CYBERSECURITY - DRONES - IA</text>
            </svg>
            <div class="logo-subtitle" style="display: none;">Plateforme de gestion</div>
        </div>

        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
            </a></li>
            
            <li class="menu-category">Gestion des équipements</li>
            <li><a href="{{ route('equipment.index') }}" class="nav-link {{ request()->routeIs('equipment.*') ? 'active' : '' }}">
                <i class="fas fa-laptop me-2"></i>Équipements
            </a></li>
            
            <li><a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags me-2"></i>Catégories
            </a></li>
            
            <li><a href="{{ route('locations.index') }}" class="nav-link {{ request()->routeIs('locations.*') ? 'active' : '' }}">
                <i class="fas fa-map-marker-alt me-2"></i>Emplacements
            </a></li>
            
            <li class="menu-category">Opérations</li>
            <li><a href="{{ route('equipment-movements.index') }}" class="nav-link {{ request()->routeIs('equipment-movements.*') ? 'active' : '' }}">
                <i class="fas fa-exchange-alt me-2"></i>Mouvements
            </a></li>
            
            <li><a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                <i class="fas fa-tools me-2"></i>Maintenance
            </a></li>
            
            <li class="menu-category">Rapports</li>
            <li><a href="#" class="nav-link">
                <i class="fas fa-chart-bar me-2"></i>Statistiques
            </a></li>
            <li><a href="#" class="nav-link">
                <i class="fas fa-file-export me-2"></i>Exports
            </a></li>
        </ul>
        <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
    </aside>