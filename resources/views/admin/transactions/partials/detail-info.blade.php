<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-info-circle text-primary mr-2"></i>Informasi Transaksi
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Order ID</label>
                    <div class="font-weight-bold">{{ $transaction->order_id }}</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Status Transaksi</label>
                    <div>
                        <span class="badge badge-{{ $transaction->getStatusBadgeColor() }} badge-lg">
                            <i class="{{ $transaction->getStatusIcon() }}"></i>
                            {{ strtoupper($transaction->transaction_status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Tanggal Transaksi</label>
                    <div class="font-weight-bold">
                        {{ $transaction->transaction_time->format('d F Y, H:i') }} WIB
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Total Pembayaran</label>
                    <div class="font-weight-bold text-success h4 mb-0">
                        {{ $transaction->formatted_amount }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>