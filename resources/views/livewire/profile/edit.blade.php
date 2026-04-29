<div>
    @if (session()->has('success'))
        <script>
            document.addEventListener('livewire:init', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: '{{ session("success") }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                });
            });
        </script>
    @endif

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-6">Thông tin cá nhân</h2>

            <form wire:submit="updateProfile" class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Họ tên</label>
                    <input type="text" wire:model="name"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" wire:model="email"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Cập nhật thông tin
                </button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <h2 class="text-xl font-semibold text-slate-900 mb-6">Đổi mật khẩu</h2>

            <form wire:submit="updatePassword" class="space-y-4 max-w-lg">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mật khẩu hiện tại</label>
                    <input type="password" wire:model="current_password"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Mật khẩu mới</label>
                    <input type="password" wire:model="password"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Xác nhận mật khẩu mới</label>
                    <input type="password" wire:model="password_confirmation"
                        class="block w-full rounded-md border-0 px-3 py-2 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm">
                </div>

                <button type="submit"
                    class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                    Đổi mật khẩu
                </button>
            </form>
        </div>
    </div>
</div>
