@props([
    'location',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h3 class="mb-6 card-title text-base-content">
    <i data-lucide="zap" class="w-5 h-5 mr"></i>
            Aksi Cepat
        </h3>
        
        <div class="space-y-3">
            <a href="{{ route('locations.edit', $location) }}" class="justify-start w-full btn">
    <i data-lucide="edit-3" class="mr-2 w-4 h-4"></i>
                Edit Location
</a>
            

                           <div class="divider"></div>
            
            <form action="{{ route('locations.destroy', $location) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus location ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="justify-start w-full btn text-error">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Hapus Location
                </button>
            </form>
        </div>
    </div>
    </div>

         <script>
func    tion copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
        alert('Email berhasil disalin!');
    });
}
</script>