<div class="d-flex justify-content-between align-items-right">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            @if($page != 'Index')
            <li class="breadcrumb-item"><a href="{{ route('admin.pelanggan.pelanggan') }}">Daftar Pelanggan</a></li>
            @endif
            <li class="breadcrumb-item active">
                @if($page == 'Index') Daftar Pelanggan
                @endif
            </li>
        </ol>
    </nav>
</div>