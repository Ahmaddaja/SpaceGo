<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">Informasi Gudang</h5>
    </div>
    <div class="card-body">
        <table class="table table-borderless">
            <tr>
                <td width="200" class="font-weight-bold">Kode Gudang</td>
                <td>: {{ $gudang->kode_gudang }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Nama Gudang</td>
                <td>: {{ $gudang->nama_gudang }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Alamat</td>
                <td>: {{ $gudang->alamat }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Kota</td>
                <td>: {{ $gudang->kota }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Provinsi</td>
                <td>: {{ $gudang->provinsi }}</td>
            </tr>
            @if($gudang->kode_pos)
            <tr>
                <td class="font-weight-bold">Kode Pos</td>
                <td>: {{ $gudang->kode_pos }}</td>
            </tr>
            @endif
            <tr>
                <td class="font-weight-bold">Jumlah Rak</td>
                <td>: <strong class="text-primary">{{ $gudang->raks_count ?? 0 }} Rak</strong></td>
            </tr>
            <tr>
                {{-- <td class="font-weight-bold">Status Aktif</td>
                <td>: 
                    @if($gudang->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                </td> --}}
            </tr>
        </table>

        @if($gudang->deskripsi)
        <hr>
        <h6 class="font-weight-bold">Deskripsi</h6>
        <p class="text-muted">{{ $gudang->deskripsi }}</p>
        @endif
    </div>
</div>