 <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
     <div class="app-brand demo">
         <a href="{{ route('home') }}" class="app-brand-link">
             <span class="app-brand-logo demo">
                 <span class="text-primary">
                     <img src="{{ asset('assets/logo.png') }}" alt="" class="img-fluid w-50">
                 </span>
             </span>
         </a>

         <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
             <i class="bx bx-chevron-left d-block d-xl-none align-middle"></i>
         </a>
     </div>

     <div class="menu-divider mt-0"></div>

     <div class="menu-inner-shadow"></div>

     <ul class="menu-inner py-1">
         <!-- Dashboard -->
         <li class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
             <a href="{{ route('home') }}" class="menu-link">
                 <i class="menu-icon tf-icons bx bx-home-smile"></i>
                 <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
             </a>
         </li>

          <!-- Mobile Kit -->
         <li class="menu-item {{ request()->routeIs('mobile.*') ? 'active' : '' }}">
             <a href="{{ route('mobile.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons bx bx-user"></i>
                 <div class="text-truncate" data-i18n="mobile">Mobile</div>
             </a>
         </li>

         <!-- Management -->
         <li class="menu-header small text-uppercase">
             <span class="menu-header-text">Management</span>
         </li>

         <!-- Users -->
         <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
             <a href="{{ route('users.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons bx bx-user"></i>
                 <div class="text-truncate" data-i18n="Users">Users</div>
             </a>
         </li>

         <!-- Roles (RBAC) -->
         @if(auth()->user()->hasPermission('roles.view'))
         <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
             <a href="{{ route('roles.index') }}" class="menu-link">
                 <i class="menu-icon tf-icons bx bx-shield"></i>
                 <div class="text-truncate" data-i18n="Roles">Roles</div>
             </a>
         </li>
         @endif

     </ul>
 </aside>
