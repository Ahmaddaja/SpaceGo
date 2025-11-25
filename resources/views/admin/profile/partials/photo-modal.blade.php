<!-- Photo Upload Modal -->
<div class="modal fade" id="photoUploadModal" tabindex="-1" aria-labelledby="photoUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoUploadModalLabel">Ubah Foto Profile</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="photoUploadForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Current Photo Preview -->
                    <div class="text-center mb-4">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold profile-avatar-lg" id="current-photo-preview">
                            @if(Auth::user()->photo)
                                <img src="{{ asset(Auth::user()->photo) }}" alt="Current Photo" id="current-photo-img" class="rounded-circle">
                            @else
                                <span id="current-photo-initials">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- New Photo Preview -->
                    <div class="text-center mb-4 d-none" id="new-photo-preview-container">
                        <p class="text-muted small mb-2">Preview Foto Baru</p>
                        <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center text-white font-weight-bold profile-avatar-lg">
                            <img id="new-photo-preview" src="#" alt="New Photo Preview" class="rounded-circle d-none">
                        </div>
                    </div>

                    <!-- File Input -->
                    <div class="form-group">
                        <label for="photo-input-modal" class="form-label">Pilih Foto</label>
                        <input type="file" class="form-control-file" id="photo-input-modal" name="photo" accept="image/*">
                        <small class="form-text text-muted">
                            Format: JPG, PNG, GIF. Maksimal: 2MB. Disarankan: 200x200 pixels.
                        </small>
                    </div>

                    <!-- Error Message -->
                    <div class="alert alert-danger d-none" id="photo-upload-error"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submit-photo-upload" disabled>
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Upload Foto
                </button>
            </div>
        </div>
    </div>
</div>