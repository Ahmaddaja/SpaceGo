<div class="col-lg-4 col-12 mb-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
            <div class="mb-3">
                <div class="position-relative d-inline-block">
                    {{-- Profile Photo Container --}}
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold" 
                         style="width: 120px; height: 120px; cursor: pointer; overflow: hidden; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
                         onclick="openPhotoModal()">
                         
                        @if(auth()->user()->profile_photo && Storage::exists('public/profile-photos/' . auth()->user()->profile_photo))
                            <img src="{{ asset('storage/profile-photos/' . auth()->user()->profile_photo) }}" 
                                 alt="Profile Photo" 
                                 id="profilePhotoPreview"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                            <span id="profile-initials" style="display: none; font-size: 2rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        @else
                            <img src="{{ asset('assets/images/default-avatar.png') }}" 
                                 alt="Profile Photo" 
                                 id="profilePhotoPreview"
                                 style="display: none; width: 100%; height: 100%; object-fit: cover;">
                            <span id="profile-initials" style="font-size: 2rem;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </span>
                        @endif
                    </div>
                    
                    {{-- Upload/Edit Button --}}
                    <button type="button" class="btn btn-primary btn-sm rounded-circle position-absolute" 
                            style="bottom: 5px; right: 5px; width: 32px; height: 32px; border: 2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"
                            onclick="openPhotoModal()"
                            title="Edit Foto Profil">
                        <i class="fas fa-camera" style="font-size: 0.8rem;"></i>
                    </button>
                </div>
            </div>

            <h4 class="font-weight-bold mb-1" style="color: #333;">{{ auth()->user()->name }}</h4>
            <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
            <span class="badge badge-primary px-3 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                {{ ucfirst(auth()->user()->role) }}
            </span>

            {{-- Photo Action Buttons --}}
            <div class="mt-3">
                <button type="button" class="btn btn-outline-primary btn-sm mr-2" onclick="openPhotoModal()">
                    <i class="fas fa-camera mr-1"></i> Ubah Foto
                </button>
                @if(auth()->user()->profile_photo && Storage::exists('public/profile-photos/' . auth()->user()->profile_photo))
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteProfilePhoto()">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
                @endif
            </div>

            <hr class="my-4">

            <div class="text-left">
                <div class="mb-3">
                    <small class="info-label" style="color: #6c757d; font-weight: 500;">Join Since</small>
                    <div class="info-value" style="color: #333; font-weight: 600;">{{ auth()->user()->created_at->format('d M Y') }}</div>
                </div>
                <div class="mb-3">
                    <small class="info-label" style="color: #6c757d; font-weight: 500;">Last Updated</small>
                    <div class="info-value" style="color: #333; font-weight: 600;">{{ auth()->user()->updated_at->format('d M Y, H:i') }}</div>
                </div>
                <div>
                    <small class="info-label" style="color: #6c757d; font-weight: 500;">Status</small>
                    <div><span class="badge badge-success px-3 py-2" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">Active</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Photo Upload Modal -->
<div class="modal fade" id="photoUploadModal" tabindex="-1" role="dialog" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoUploadModalLabel">Ubah Foto Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <!-- Photo Preview -->
                <div class="mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 150px; height: 150px; overflow: hidden; border: 2px dashed #dee2e6;">
                        <img id="modalPhotoPreview" 
                             src="{{ auth()->user()->profile_photo ? asset('storage/profile-photos/' . auth()->user()->profile_photo) : asset('assets/images/default-avatar.png') }}" 
                             alt="Preview" 
                             style="width: 100%; height: 100%; object-fit: cover; display: block;">
                        <span id="modalInitials" style="font-size: 2.5rem; display: none;">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </span>
                    </div>
                </div>

                <!-- Upload Form -->
                <form id="photoUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photoInput" name="profile_photo" accept="image/*" onchange="previewPhoto(this)">
                            <label class="custom-file-label" for="photoInput" id="photoInputLabel">Pilih foto...</label>
                        </div>
                        <small class="form-text text-muted">Format: JPEG, PNG, GIF. Maksimal: 2MB</small>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-secondary mr-2" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-success" id="uploadButton" onclick="uploadPhoto()" disabled>
                            <i class="fas fa-upload mr-1"></i> Upload Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>