   <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarPanel">
        <div class="offcanvas-body">
            <!-- profile box -->
            <div class="profileBox">
                <div class="image-wrapper">
                    <img src="{{ Avatar::create(Auth::user()->name) }}" alt="image" class="imaged rounded">
                </div>
                <div class="in">
                    <strong>{{ Auth::user()->name }}</strong>
                    <div class="text-muted">
                        <ion-icon name="location"></ion-icon>
                        Tegal
                    </div>
                </div>
                <a href="#" class="close-sidebar-button" data-bs-dismiss="offcanvas">
                    <ion-icon name="close"></ion-icon>
                </a>
            </div>
            <!-- * profile box -->

            <ul class="listview flush transparent no-line image-listview mt-2">
                <li>
                    <a href="index.html" class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="home-outline"></ion-icon>
                        </div>
                        <div class="in">
                            Discover
                        </div>
                    </a>
                </li>
                <li>
                    <a href="app-components.html" class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="cube-outline"></ion-icon>
                        </div>
                        <div class="in">
                            Components
                        </div>
                    </a>
                </li>
                <li>
                    <a href="app-pages.html" class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="layers-outline"></ion-icon>
                        </div>
                        <div class="in">
                            <div>Pages</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="page-chat.html" class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                        </div>
                        <div class="in">
                            <div>Chat</div>
                            <span class="badge badge-danger">5</span>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="moon-outline"></ion-icon>
                        </div>
                        <div class="in">
                            <div>Dark Mode</div>
                            <div class="form-check form-switch">
                                <input class="form-check-input dark-mode-switch" type="checkbox" id="darkmodesidebar">
                                <label class="form-check-label" for="darkmodesidebar"></label>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <!-- sidebar buttons -->
        <div class="sidebar-buttons">
            <a href="#" class="button">
                <ion-icon name="person-outline"></ion-icon>
            </a>
            <a href="#" class="button">
                <ion-icon name="archive-outline"></ion-icon>
            </a>
            <a href="#" class="button">
                <ion-icon name="settings-outline"></ion-icon>
            </a>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                document.getElementById('logout-form').submit();"
                class="button">
                <ion-icon name="log-out-outline"></ion-icon>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
        <!-- * sidebar buttons -->
    </div>