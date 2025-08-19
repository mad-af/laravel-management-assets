@props([
    'user',
    'class' => ''
])

<div class="shadow-xl card bg-base-100 {{ $class }}">
    <div class="card-body">
        <h2 class="mb-4 text-lg font-semibold card-title">Aksi Cepat</h2>
        
        <div class="space-y-3">
            <button class="justify-start w-full btn btn-warning" onclick="openEditDrawer({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')">
                <i data-lucide="edit" class="mr-2 w-4 h-4"></i>
                Edit User
            </button>
            
            <button class="justify-start w-full btn btn-info" onclick="copyToClipboard('{{ $user->email }}')">
                <i data-lucide="copy" class="mr-2 w-4 h-4"></i>
                Copy Email
            </button>
            
            <div class="divider"></div>
            
            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="justify-start w-full btn btn-error">
                    <i data-lucide="trash-2" class="mr-2 w-4 h-4"></i>
                    Hapus User
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Email berhasil disalin!');
    });
}
</script>