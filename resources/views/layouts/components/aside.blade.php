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

         <!-- Images -->
         @if (auth()->user()->hasPermission('images.view'))
             <li class="menu-item {{ request()->routeIs('images.*') ? 'active' : '' }}">
                 <a href="{{ route('images.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-image"></i>
                     <div class="text-truncate" data-i18n="Images">Image Gallery</div>
                 </a>
             </li>
         @endif

         <!-- Master Data -->
         @if (auth()->user()->hasPermission('master.categories.view') || auth()->user()->hasPermission('master.products.view'))
             <li class="menu-header small text-uppercase">
                 <span class="menu-header-text">Master Data</span>
             </li>
         @endif

         <!-- Categories -->
         @if (auth()->user()->hasPermission('master.categories.view'))
             <li class="menu-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                 <a href="{{ route('categories.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-category"></i>
                     <div class="text-truncate" data-i18n="Categories">Kategori</div>
                 </a>
             </li>
         @endif

         <!-- Products -->
         @if (auth()->user()->hasPermission('master.products.view'))
             <li class="menu-item {{ request()->routeIs('products.*') ? 'active' : '' }}">
                 <a href="{{ route('products.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-package"></i>
                     <div class="text-truncate" data-i18n="Products">Produk</div>
                 </a>
             </li>
         @endif

         <!-- Management -->
         <li class="menu-header small text-uppercase">
             <span class="menu-header-text">Management</span>
         </li>

         <!-- Users -->
         @if (auth()->user()->hasPermission('management.users.view'))
             <li class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                 <a href="{{ route('users.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-user"></i>
                     <div class="text-truncate" data-i18n="Users">Users</div>
                 </a>
             </li>
         @endif

         <!-- Roles (RBAC) -->
         @if (auth()->user()->hasPermission('management.roles.view'))
             <li class="menu-item {{ request()->routeIs('roles.*') ? 'active' : '' }} d-none">
                 <a href="{{ route('roles.index') }}" class="menu-link">
                     <i class="menu-icon tf-icons bx bx-shield"></i>
                     <div class="text-truncate" data-i18n="Roles">Role Templates</div>
                 </a>
             </li>
         @endif

     </ul>
 </aside>
