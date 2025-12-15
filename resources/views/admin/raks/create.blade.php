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
            if (confirm('Batalkan pembuatan rak? Foto yang diupload akan dihapus.')) {
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
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    `;
            overlay.innerHTML = `
        <div style="background: white; padding: 20px; border-radius: 8px; text-align: center;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2 mb-0">Membersihkan foto...</p>
        </div>
    `;
            document.body.appendChild(overlay);
        }
    </script>
@endpush
