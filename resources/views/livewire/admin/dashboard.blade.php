<div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Tổng người dùng</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_users'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Tổng báo cáo</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_reports'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Báo cáo hôm nay</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['reports_today'] }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Tổng dự án</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_projects'] }}</p>
        </div>
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Truy cập nhanh</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.users') }}"
                class="block rounded-lg border border-slate-200 p-4 text-center hover:border-blue-600 hover:shadow-md transition">
                <p class="font-semibold text-slate-900">Quản lý người dùng</p>
            </a>
            <a href="{{ route('admin.projects') }}"
                class="block rounded-lg border border-slate-200 p-4 text-center hover:border-blue-600 hover:shadow-md transition">
                <p class="font-semibold text-slate-900">Quản lý dự án</p>
            </a>
            <a href="{{ route('report.form') }}"
                class="block rounded-lg border border-slate-200 p-4 text-center hover:border-blue-600 hover:shadow-md transition">
                <p class="font-semibold text-slate-900">Tạo báo cáo</p>
            </a>
        </div>
    </div>
</div>
