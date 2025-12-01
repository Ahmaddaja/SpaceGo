<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="mb-0 font-weight-bold">
            <i class="fas fa-credit-card text-primary mr-2"></i>Detail Pembayaran
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Metode Pembayaran</label>
                    <div class="font-weight-bold">
                        @if($transaction->payment_type)
                            <span class="badge badge-info">
                                {{ ucfirst(str_replace('_', ' ', $transaction->payment_type)) }}
                            </span>
                        @else
                            <span class="text-muted">Belum dipilih</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="text-muted small">Fraud Status</label>
                    <div class="font-weight-bold">
                        @if($transaction->fraud_status)
                            <span class="badge badge-{{ $transaction->fraud_status == 'accept' ? 'success' : 'danger' }}">
                                {{ strtoupper($transaction->fraud_status) }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($transaction->midtrans_response)
            <div class="col-12">
                <div class="mb-3">
                    <label class="text-muted small">Midtrans Response</label>
                    <div class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                        <pre class="mb-0 small">{{ json_encode($transaction->midtrans_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>