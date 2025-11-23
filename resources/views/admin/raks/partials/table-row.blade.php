<tr>
    <td class="align-middle">
        <span class="font-weight-bold text-primary">{{ $rak->kode_rak }}</span>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @if($rak->foto)
            <img src="{{ asset('storage/' . $rak->foto) }}" 
                 alt="{{ $rak->nama_rak }}" 
                 class="rounded mr-2"
                 style="width: 50px; height: 50px; object-fit: cover;">
            @else
            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mr-2 text-white" 
                 style="width: 50px; height: 50px; min-width: 50px;">
                <i class="fas fa-box"></i>
            </div>
            @endif
            <div>
                <div class="font-weight-bold">{{ $rak->nama_rak }}</div>
                <small class="text-muted">{{ Str::limit($rak->deskripsi, 30) }}</small>
            </div>
        </div>
    </td>
    <td class="align-middle">
        <span class="badge badge-info">{{ $rak->jenis_rak }}</span>
    </td>
    <td class="align-middle">
        <div>{{ $rak->lokasi_gudang }}</div>
        @if($rak->zona_gudang)
        <small class="text-muted">Zona: {{ $rak->zona_gudang }}</small>
        @endif
    </td>
    <td class="align-middle">
        <small>
            {{ $rak->panjang }}m × {{ $rak->lebar }}m × {{ $rak->tinggi }}m<br>
            <span class="text-muted">{{ $rak->jumlah_tingkat }} tingkat</span>
        </small>
    </td>
    <td class="align-middle">
        <strong>{{ number_format($rak->kapasitas_berat) }} kg</strong>
    </td>
    <td class="align-middle">
        <strong class="text-success">{{ $rak->harga_format }}</strong>
    </td>
    <td class="align-middle">
        @if($rak->status == 'tersedia')
            <span class="badge badge-success px-3 py-2">Tersedia</span>
        @elseif($rak->status == 'terisi')
            <span class="badge badge-warning px-3 py-2">Terisi</span>
        @else
            <span class="badge badge-danger px-3 py-2">Maintenance</span>
        @endif
    </td>
    <td class="align-middle text-center">
        <div class="btn-group" role="group">
            <a href="{{ route('raks.show', $rak->id) }}" 
               class="btn btn-sm btn-info" 
               title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('raks.edit', $rak->id) }}" 
               class="btn btn-sm btn-warning" 
               title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" 
                    class="btn btn-sm btn-danger" 
                    onclick="confirmDelete({{ $rak->id }})"
                    title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <form id="delete-form-{{ $rak->id }}" 
              action="{{ route('raks.destroy', $rak->id) }}" 
              method="POST" 
              style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </td>
</tr>