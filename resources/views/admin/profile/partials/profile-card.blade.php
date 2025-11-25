<div class="col-lg-4 col-12 mb-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <div class="mb-3">
                <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold profile-avatar" 
                     data-toggle="modal" data-target="#photoUploadModal" style="cursor: pointer; width: 120px; height: 120px; overflow: hidden;">
                    @if(Auth::user()->foto)
                        <img src="{{ Storage::disk('public')->exists(Auth::user()->foto) ? Storage::url(Auth::user()->foto) : asset(Auth::user()->foto) }}?t={{ time() }}" 
                             alt="Profile Photo" id="profile-photo-display" class="rounded-circle w-100 h-100" style="object-fit: cover;">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white font-weight-bold" id="profile-initials">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div class="mt-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#photoUploadModal">
                        <i class="fas fa-camera mr-1"></i> Ubah Foto
                    </button>
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

<!-- Include Photo Upload Modal -->
@include('admin.profile.partials.photo-modal')