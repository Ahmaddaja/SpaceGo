<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Informasi Rak</h5>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td width="200" class="font-weight-bold">Kode Rak</td>
                <td>: {{ $rak->kode_rak }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Nama Rak</td>
                <td>: {{ $rak->nama_rak }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Jenis Rak</td>
                <td>: <span class="badge badge-info">{{ $rak->jenis_rak }}</span></td>
            </tr>
            <tr>
                <td class="font-weight-bold">Lokasi Gudang</td>
                <td>: {{ $rak->lokasi_gudang }}</td>
            </tr>
            {{-- @if($rak->zona_gudang)
            <tr>
                <td class="font-weight-bold">Zona Gudang</td>
                <td>: {{ $rak->zona_gudang }}</td>
            </tr>
            @endif --}}
            <tr>
                <td class="font-weight-bold">Status</td>
                <td>: 
                    @if($rak->status == 'tersedia')
                        <span class="badge badge-success px-3 py-2">Tersedia</span>
                    @elseif($rak->status == 'terisi')
                        <span class="badge badge-warning px-3 py-2">Terisi</span>
                    @else
                        <span class="badge badge-danger px-3 py-2">Maintenance</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="font-weight-bold">Harga Sewa/Bulan</td>
                <td>: <strong class="text-success">{{ $rak->harga_format }}</strong></td>
            </tr>
            <tr>
                {{-- <td class="font-weight-bold">Status Aktif</td>
                <td>: 
                    @if($rak->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                </td> --}}
            </tr>
        </table>

        @if($rak->deskripsi)
        <hr>
        <h6 class="font-weight-bold">Deskripsi</h6>
        <p class="text-muted">{{ $rak->deskripsi }}</p>
        @endif
    </div>
</div>