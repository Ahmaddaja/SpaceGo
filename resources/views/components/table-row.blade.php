<tr>
    <td class="align-middle">
        <span class="font-weight-bold text-primary">{{ $gudang->kode_gudang }}</span>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @if($gudang->foto)
            <img src="{{ asset('storage/' . $gudang->foto) }}" 
                 alt="{{ $gudang->nama_gudang }}" 
                 class="rounded mr-2"
                 style="width: 50px; height: 50px; object-fit: cover;">
            @else
            <div class="bg-secondary rounded d-flex align-items-center justify-content-center mr-2 text-white" 
                 style="width: 50px; height: 50px; min-width: 50px;">
                <i class="fas fa-warehouse"></i>
            </div>
            @endif
            <div>
                <div class="font-weight-bold">{{ $gudang->nama_gudang }}</div>
                <small class="text-muted">{{ Str::limit($gudang->deskripsi, 30) }}</small>
            </div>
        </div>
    </td>
    <td class="align-middle">
        <div>{{ $gudang->kota }}</div>
        <small class="text-muted">{{ $gudang->provinsi }}</small>
    </td>
    <td class="align-middle">
        <strong>{{ $gudang->raks_count ?? 0 }}</strong> Rak
    </td>
    <td class="align-middle">
        @if($gudang->is_active)
            <span class="badge badge-success px-3 py-2">Aktif</span>
        @else
            <span class="badge badge-secondary px-3 py-2">Tidak Aktif</span>
        @endif
    </td>
    <td class="align-middle text-center">
        <div class="btn-group" role="group">
            <a href="{{ route('gudangs.show', $gudang->id) }}" 
               class="btn btn-sm btn-info" 
               title="Detail">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('gudangs.edit', $gudang->id) }}" 
               class="btn btn-sm btn-warning" 
               title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            <button type="button" 
                    class="btn btn-sm btn-danger" 
                    onclick="confirmDelete({{ $gudang->id }})"
                    title="Hapus">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <form id="delete-form-{{ $gudang->id }}" 
              action="{{ route('gudangs.destroy', $gudang->id) }}" 
              method="POST" 
              style="display: none;">
            @csrf
            @method('DELETE')
        </form>
    </td>
</tr>