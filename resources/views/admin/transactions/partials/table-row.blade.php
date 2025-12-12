<tr>
    <td class="text-center align-middle">
        {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
    </td>
    <td class="align-middle">
        <strong class="text-primary">{{ $transaction->order_id }}</strong>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            @php
                // Cek relasi customer/user
                $customer = $transaction->customer ?? $transaction->user ?? null;
                
                if ($customer) {
                    // Cek field foto dengan berbagai kemungkinan nama
                    $fotoField = $customer->foto ?? $customer->profile_photo ?? $customer->photo ?? $customer->profile_photo_path ?? null;
                    $hasFoto = $fotoField && !empty($fotoField);
                } else {
                    $hasFoto = false;
                    $fotoField = null;
                }
            @endphp
            
            @if($customer && $hasFoto)
                <!-- Tampilkan foto profil -->
                <img src="{{ asset('storage/' . $fotoField) }}" 
                     alt="Profile {{ $customer->name }}" 
                     class="rounded-circle mr-2"
                     style="width: 32px; height: 32px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            @else
                <!-- Tampilkan inisial jika tidak ada foto atau tidak ada customer -->
                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold mr-2" 
                     style="width: 32px; height: 32px; font-size: 12px; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    {{ strtoupper(substr($customer->name ?? 'C', 0, 1)) }}
                </div>
            @endif
            <div>
                <div class="font-weight-bold">{{ $customer->name ?? 'N/A' }}</div>
                <small class="text-muted">{{ $customer->email ?? '-' }}</small>
            </div>
        </div>
    </td>
    <td class="align-middle">
        @if($transaction->rak)
            <div>
                <strong>{{ $transaction->rak->nama_rak }}</strong>
                <br>
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt"></i> 
                    {{ $transaction->rak->gudang->nama_gudang ?? '-' }}
                </small>
            </div>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td class="align-middle text-right">
        <strong class="text-success">{{ $transaction->formatted_amount }}</strong>
    </td>
    <td class="align-middle text-center">
        <span class="badge badge-{{ $transaction->getStatusBadgeColor() }}">
            <i class="{{ $transaction->getStatusIcon() }}"></i>
                {{ $transaction->getStatusName() }}
        </span>
    </td>
    <td class="align-middle">
        @if($transaction->payment_type)
            <span class="badge badge-secondary">
                {{ ucfirst(str_replace('_', ' ', $transaction->payment_type)) }}
            </span>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td class="align-middle">
        <div>{{ $transaction->transaction_time->format('d M Y') }}</div>
        <small class="text-muted">{{ $transaction->transaction_time->format('H:i') }}</small>
    </td>
    <td class="align-middle text-center">
        <div class="btn-group btn-group-sm">
            <a href="{{ route('admin.transactions.show', $transaction->id) }}" 
               class="btn btn-info" 
               title="Detail">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </td>
</tr>