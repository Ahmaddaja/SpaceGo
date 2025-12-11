<div class="card border-0 shadow-sm">
    <div class="card-body">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-save mr-2"></i>{{ $submitText }}
        </button>
        {{-- ✅ UBAH dari <a> ke <button> --}}
        <button type="button" class="btn btn-secondary btn-block" id="cancel-button" onclick="handleCancelWithCleanup()">
            <i class="fas fa-times mr-2"></i>Batal
        </button>
        @if (isset($rak))
            <hr>
            <button type="button" class="btn btn-danger btn-block" onclick="confirmDelete({{ $rak->id }})">
                <i class="fas fa-trash mr-2"></i>Hapus Rak
            </button>
        @endif
    </div>
</div>

@if (isset($rak))
    @push('scripts')
        <script>
            function confirmDelete(id) {
                if (confirm('Apakah Anda yakin ingin menghapus rak ini?')) {
                    document.getElementById('delete-form-' + id).submit();
                }
            }
        </script>
    @endpush
@endif

@push('scripts')
    <script>
        // ✅ Track foto yang baru diupload dalam session ini
        let uploadedPhotosInSession = [];

        // ✅ Fungsi untuk handle tombol Batal dengan cleanup
        async function handleCancelWithCleanup() {
            if (uploadedPhotosInSession.length === 0) {
                // Tidak ada foto baru, langsung redirect
                window.location.href = "{{ route('raks.index') }}";
                return;
            }

            // Ada foto baru yang perlu dihapus
            if (!confirm('Anda telah mengupload ' + uploadedPhotosInSession.length +
                    ' foto. Batalkan dan hapus foto yang sudah diupload?')) {
                return;
            }

            // Show loading
            const cancelBtn = document.getElementById('cancel-button');
            const originalText = cancelBtn.innerHTML;
            cancelBtn.disabled = true;
            cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Membatalkan...';

            try {
                // Hapus semua foto yang diupload
                const deletePromises = uploadedPhotosInSession.map(fotoId => {
                    return fetch(`/raks/photos/${fotoId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                });

                await Promise.all(deletePromises);

                // Redirect setelah berhasil
                window.location.href = "{{ route('raks.index') }}";
            } catch (error) {
                console.error('Error cleaning up photos:', error);
                cancelBtn.disabled = false;
                cancelBtn.innerHTML = originalText;
                showAlert('Gagal menghapus foto. Silakan coba lagi.', 'error');
            }
        }

        // ✅ Peringatan jika user close tab/window
        window.addEventListener('beforeunload', function(e) {
            if (uploadedPhotosInSession.length > 0) {
                const message = 'Anda memiliki foto yang belum disimpan. Yakin ingin meninggalkan halaman?';
                e.returnValue = message;
                return message;
            }
        });

        // ✅ Reset tracking saat form berhasil disubmit
        const mainForm = document.querySelector('form');
        if (mainForm) {
            mainForm.addEventListener('submit', function() {
                uploadedPhotosInSession = [];
                // Hapus beforeunload warning
                window.onbeforeunload = null;
            });
        }
    </script>
@endpush
