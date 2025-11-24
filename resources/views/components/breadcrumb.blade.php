<div class="d-flex justify-content-between align-items-center">
    <h1 class="m-0">
        @if($page == 'Index') Kelola Gudang
        @elseif($page == 'Create') Tambah Gudang Baru
        @elseif($page == 'Edit') Edit Gudang
        @elseif($page == 'Show') Detail Gudang
        @endif
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent p-0 m-0">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            @if($page != 'Index')
            <li class="breadcrumb-item"><a href="{{ route('gudangs.index') }}">Kelola Gudang</a></li>
            @endif
            <li class="breadcrumb-item active">
                @if($page == 'Index') Kelola Gudang
                @elseif($page == 'Create') Tambah
                @elseif($page == 'Edit') Edit
                @elseif($page == 'Show') Detail
                @endif
            </li>
        </ol>
    </nav>
</div>