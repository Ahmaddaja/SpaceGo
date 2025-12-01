@if(session('success'))
<div class="success-alert text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center shadow-sm">
    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
    <span class="font-medium">{{ session('success') }}</span>
</div>
@endif

@if($errors->any())
<div class="error-alert text-red-700 px-6 py-4 rounded-2xl mb-6 flex items-center shadow-sm">
    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
    <div>
        <strong class="font-medium">Error!</strong> Silakan periksa form kembali.
    </div>
</div>
@endif