<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 font-weight-bold">Daftar Rak di Gudang Ini</h5>
        <span class="badge badge-primary">{{ $gudang->raks_count ?? 0 }} Rak</span>
    </div>
    <div class="card-body">
        @if($gudang->raks && $gudang->raks->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Kode Rak</th>
                        <th>Nama Rak</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($gudang->raks as $rak)
                    <tr>
                        <td class="align-middle">
                            <span class="font-weight-bold text-primary">{{ $rak->kode_rak }}</span>
                        </td>
                        <td class="align-middle">{{ $rak->nama_rak }}</td>
                        <td class="align-middle">
                            <span class="badge badge-info">{{ $rak->jenis_rak }}</span>
                        </td>
                        <td class="align-middle">
                            @if($rak->status == 'tersedia')
                                <span class="badge badge-success">Tersedia</span>
                            @elseif($rak->status == 'terisi')
                                <span class="badge badge-warning">Terisi</span>
                            @else
                                <span class="badge badge-danger">Maintenance</span>
                            @endif
                        </td>
                        <td class="align-middle text-center">
                            <a href="{{ route('raks.show', $rak->id) }}" 
                               class="btn btn-sm btn-info" 
                               title="Detail Rak">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-4">
            <i class="fas fa-box fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada rak terdaftar di gudang ini</p>
            <a href="{{ route('raks.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-2"></i>Tambah Rak
            </a>
        </div>
        @endif
    </div>
</div>