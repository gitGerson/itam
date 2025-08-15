<div id="notification-welcome" class="notification-box">
    <div class="notification-dialog android-style">
        <div class="notification-header">
            <div class="in">
                <img src="{{ Avatar::create('Tong Tji') }}" alt="image" class="imaged w24">
                <strong>Tong Tji</strong>
                <span>just now</span>
            </div>
            <a href="#" class="close-button">
                <ion-icon name="close"></ion-icon>
            </a>
        </div>
        <div class="notification-content">
            <div class="in">
                <h3 class="subtitle">Welcome to Tong Tji</h3>
                <div class="text">
                    Tong Tji is a PWA ready.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Trigger welcome notification after 5 seconds
        setTimeout(() => {
            notification('notification-welcome', 5000);
        }, 2000);
    </script>
@endpush
