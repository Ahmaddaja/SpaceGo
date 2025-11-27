<tr>
    <td class="text-center align-middle">
        {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $loop->iteration }}
    </td>
    <td class="align-middle">
        <strong class="text-primary">{{ $transaction->order_id }}</strong>
    </td>
    <td class="align-middle">
        <div class="d-flex align-items-center">
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white font-weight-bold mr-2" 
                 style="width: 32px; height: 32px; font-size: 12px;">
                {{ strtoupper(substr($transaction->user->name ?? 'U', 0, 2)) }}
            </div>
            <div>
                <div class="font-weight-bold">{{ $transaction->user->name ?? 'N/A' }}</div>
                <small class="text-muted">{{ $transaction->user->email ?? '-' }}</small>
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
            {{ strtoupper($transaction->transaction_status) }}
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