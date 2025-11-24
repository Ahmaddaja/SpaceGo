<div class="col-lg-4 col-12 mb-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <div class="mb-3">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold profile-avatar" onclick="document.getElementById('photo-input').click()">
                    @if(Auth::user()->photo)
                        <img src="{{ asset(Auth::user()->photo) }}" alt="Profile Photo" id="profile-photo-display">
                    @else
                        <span id="profile-initials">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                    @endif
                </div>
            </div>

            <h4 class="font-weight-bold mb-1">{{ Auth::user()->name }}</h4>
            <p class="text-muted mb-3">{{ Auth::user()->email }}</p>
            <span class="badge badge-primary px-3 py-2">{{ ucfirst(Auth::user()->role) }}</span>

            <hr class="my-4">

            <div class="text-left">
                <div class="mb-3">
                    <small class="info-label">Join Since</small>
                    <div class="info-value">{{ Auth::user()->created_at->format('d M Y') }}</div>
                </div>
                <div class="mb-3">
                    <small class="info-label">Last Updated</small>
                    <div class="info-value">{{ Auth::user()->updated_at->format('d M Y, H:i') }}</div>
                </div>
                <div>
                    <small class="info-label">Status</small>
                    <div><span class="badge badge-success px-3 py-2">Active</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
