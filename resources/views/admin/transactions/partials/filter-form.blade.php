<form method="GET" action="{{ route('admin.transactions.index') }}" class="mb-3">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group mb-2">
                <input type="text" 
                       name="search" 
                       class="form-control form-control-sm" 
                       placeholder="Cari order ID, customer, rak..."
                       value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <select name="status" class="form-control form-control-sm">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="settlement" {{ request('status') == 'settlement' ? 'selected' : '' }}>Settlement</option>
                    <option value="capture" {{ request('status') == 'capture' ? 'selected' : '' }}>Capture</option>
                    <option value="deny" {{ request('status') == 'deny' ? 'selected' : '' }}>Deny</option>
                    <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>Cancel</option>
                    <option value="expire" {{ request('status') == 'expire' ? 'selected' : '' }}>Expire</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <input type="date" 
                       name="date_from" 
                       class="form-control form-control-sm" 
                       placeholder="Dari Tanggal"
                       value="{{ request('date_from') }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group mb-2">
                <input type="date" 
                       name="date_to" 
                       class="form-control form-control-sm" 
                       placeholder="Sampai Tanggal"
                       value="{{ request('date_to') }}">
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary btn-sm mr-1">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo"></i> Reset
            </a>
        </div>
    </div>
</form>