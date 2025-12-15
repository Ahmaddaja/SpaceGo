@extends('layouts.main')

@section('title-content')
    <x-breadcump-rak page="Create" module="Rak" routePrefix="raks" />
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('raks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    @include('admin.raks.partials.form-basic-info')
                    @include('admin.raks.partials.form-specifications')
                </div>

                <div class="col-lg-4">
                    @include('admin.raks.partials.form-location-price')
                    @include('admin.raks.partials.form-photo')
                    @include('admin.raks.partials.form-actions', ['submitText' => 'Simpan Rak'])
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
    // ✅ TAMBAHKAN: Function untuk cancel dengan cleanup
    function cancelCreateRak() {
        // Create modern confirmation modal
        const modalHtml = `
            <div class="modal-overlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;transition:opacity 0.3s ease, background 0.3s ease;">
                <div class="modal-content" style="background:white;width:90%;max-width:400px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0);overflow:hidden;transform:translateY(20px) scale(0.95);opacity:0;transition:all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                    <div class="modal-header" style="padding:20px;border-bottom:1px solid #f0f0f0;">
                        <h5 style="margin:0;font-size:18px;color:#333;font-weight:600;">Konfirmasi Pembatalan</h5>
                    </div>
                    <div class="modal-body" style="padding:20px;">
                        <div style="margin-bottom:20px;">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" style="color:#ff9800;margin:0 auto 15px;display:block;transform:scale(0.8);opacity:0;transition:all 0.3s ease 0.1s;">
                                <path d="M12 9V11M12 15H12.01M5.07183 19H18.9282C20.4678 19 21.4301 17.3333 20.6603 16L13.7321 4C12.9623 2.66667 11.0377 2.66667 10.2679 4L3.33975 16C2.56995 17.3333 3.53216 19 5.07183 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <p style="text-align:center;color:#555;line-height:1.5;margin:0;transform:translateY(10px);opacity:0;transition:all 0.3s ease 0.15s;">
                                Apakah Anda yakin ingin membatalkan pembuatan rak?<br>
                                <span style="font-size:14px;color:#777;">Semua foto yang telah diupload akan dihapus secara permanen.</span>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding:16px 20px;background:#f8f9fa;display:flex;gap:12px;justify-content:flex-end;transform:translateY(10px);opacity:0;transition:all 0.3s ease 0.2s;">
                        <button type="button" class="btn-cancel" style="padding:10px 20px;background:#f5f5f5;border:1px solid #ddd;border-radius:6px;color:#666;font-weight:500;cursor:pointer;transition:all 0.2s;transform:translateY(0);">
                            Kembali
                        </button>
                        <button type="button" class="btn-confirm" style="padding:10px 20px;background:#dc3545;border:none;border-radius:6px;color:white;font-weight:500;cursor:pointer;transition:all 0.2s;transform:translateY(0);">
                            Ya, Batalkan
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.createElement('div');
        modalContainer.innerHTML = modalHtml;
        document.body.appendChild(modalContainer);
        
        // Trigger animation after DOM is added
        setTimeout(() => {
            const modalOverlay = modalContainer.querySelector('.modal-overlay');
            const modalContent = modalContainer.querySelector('.modal-content');
            const svgIcon = modalContainer.querySelector('svg');
            const modalText = modalContainer.querySelector('.modal-body p');
            const modalFooter = modalContainer.querySelector('.modal-footer');
            
            modalOverlay.style.background = 'rgba(0,0,0,0.5)';
            modalOverlay.style.opacity = '1';
            
            modalContent.style.boxShadow = '0 10px 30px rgba(0,0,0,0.15)';
            modalContent.style.transform = 'translateY(0) scale(1)';
            modalContent.style.opacity = '1';
            
            svgIcon.style.transform = 'scale(1)';
            svgIcon.style.opacity = '1';
            
            modalText.style.transform = 'translateY(0)';
            modalText.style.opacity = '1';
            
            modalFooter.style.transform = 'translateY(0)';
            modalFooter.style.opacity = '1';
        }, 10);
        
        // Button event handlers
        modalContainer.querySelector('.btn-cancel').addEventListener('click', function() {
            closeModalWithAnimation(modalContainer);
        });
        
        modalContainer.querySelector('.btn-confirm').addEventListener('click', function() {
            // Show loading
            showLoadingOverlay();
            
            // Cleanup temp photos
            fetch('{{ route('raks.photos.clear-temp') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.message);
                // Redirect ke index
                window.location.href = '{{ route('raks.index') }}';
            })
            .catch(error => {
                console.error('Error:', error);
                // Tetap redirect meskipun error
                window.location.href = '{{ route('raks.index') }}';
            });
        });
        
        // Close modal on overlay click
        modalContainer.querySelector('.modal-overlay').addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                closeModalWithAnimation(modalContainer);
            }
        });
        
        // Function to close modal with animation
        function closeModalWithAnimation(modal) {
            const modalOverlay = modal.querySelector('.modal-overlay');
            const modalContent = modal.querySelector('.modal-content');
            const svgIcon = modal.querySelector('svg');
            const modalText = modal.querySelector('.modal-body p');
            const modalFooter = modal.querySelector('.modal-footer');
            
            // Reverse animations
            modalContent.style.transform = 'translateY(20px) scale(0.95)';
            modalContent.style.opacity = '0';
            
            svgIcon.style.transform = 'scale(0.8)';
            svgIcon.style.opacity = '0';
            
            modalText.style.transform = 'translateY(10px)';
            modalText.style.opacity = '0';
            
            modalFooter.style.transform = 'translateY(10px)';
            modalFooter.style.opacity = '0';
            
            modalOverlay.style.opacity = '0';
            modalOverlay.style.background = 'rgba(0,0,0,0)';
            
            // Remove from DOM after animation
            setTimeout(() => {
                if (document.body.contains(modal)) {
                    document.body.removeChild(modal);
                }
            }, 300);
        }
    }

    // ✅ TAMBAHKAN: Auto cleanup saat user leave page tanpa save
    let isFormSubmitted = false;

    // Track form submission
    document.querySelector('form')?.addEventListener('submit', function() {
        isFormSubmitted = true;
    });

    // Cleanup saat user leave page
    window.addEventListener('beforeunload', function(e) {
        // Jika ada temp photos dan form belum di-submit
        const hasTempPhotos = {{ !empty(session('temp_rak_photos')) ? 'true' : 'false' }};

        if (hasTempPhotos && !isFormSubmitted) {
            // Send beacon untuk cleanup (tidak block navigation)
            const formData = new FormData();
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            navigator.sendBeacon('{{ route('raks.photos.clear-temp') }}', formData);
        }
    });

    // Helper function untuk loading overlay
    function showLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0);
            backdrop-filter: blur(0px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease, background 0.3s ease, backdrop-filter 0.3s ease;
        `;
        overlay.innerHTML = `
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0); text-align: center; min-width: 200px; transform: translateY(20px) scale(0.95); opacity: 0; transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                <div class="spinner" style="border: 3px solid #f3f3f3;
                    border-top: 3px solid #3498db;
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 15px;"></div>
                <style>
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
                <p style="margin: 0; color: #333; font-weight: 500; font-size: 16px; transform: translateY(10px); opacity: 0; transition: all 0.3s ease 0.1s;">Membersihkan data...</p>
            </div>
        `;
        document.body.appendChild(overlay);
        
        // Trigger animation
        setTimeout(() => {
            overlay.style.background = 'rgba(0,0,0,0.7)';
            overlay.style.backdropFilter = 'blur(2px)';
            overlay.style.opacity = '1';
            
            const contentDiv = overlay.querySelector('div');
            contentDiv.style.boxShadow = '0 10px 40px rgba(0,0,0,0.1)';
            contentDiv.style.transform = 'translateY(0) scale(1)';
            contentDiv.style.opacity = '1';
            
            const text = overlay.querySelector('p');
            text.style.transform = 'translateY(0)';
            text.style.opacity = '1';
        }, 10);
    }
</script>
@endpush
