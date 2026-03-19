@extends('b2b::web.layouts.app')

@section('content')
<!-- VIEW: Role Management -->
<div id="view-role_management" class="view-section active space-y-6">
    <div
        class="flex flex-col md:flex-row justify-between items-center bg-white p-6 md:p-8 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 gap-4 dir-dependent-flex">
        <div class="dir-dependent-text">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight" data-i18n="role_management">
                {{ trans('client.role_management') }}
            </h2>
            <p class="text-gray-500 font-bold mt-1">{{ trans('client.manage_your_team_permissions') }}</p>
        </div>
        <button onclick="openAddUserModal()"
            class="px-6 py-3 bg-gray-900 text-white text-sm font-black rounded-xl shadow-lg hover:bg-black transition-transform hover:-translate-y-0.5 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
            </svg>
            <span data-i18n="add_user">{{ trans('client.add_user') }}</span>
        </button>
    </div>

    @if($employees->count() > 0)
    <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden p-4">
        <div class="overflow-x-auto w-full">
            <table id="users-dataTable" class="w-full text-sm whitespace-nowrap text-right tbl-rtl-aware display">
                <thead>
                    <tr class="bg-gray-50/50 text-gray-400 font-bold uppercase tracking-widest text-xs border-b border-gray-100">
                        <th class="py-4 px-6" data-i18n="th_user">{{ trans('client.user') }}</th>
                        <th class="py-4 px-6" data-i18n="th_branch">{{ trans('client.branch') }}</th>
                        <th class="py-4 px-6" data-i18n="th_status">{{ trans('client.status') }}</th>
                        <th class="py-4 px-6" data-i18n="actions">{{ trans('client.actions') }}</th>
                    </tr>
                </thead>
                <tbody id="users-table" class="divide-y divide-gray-50">
                    @foreach($employees->groupBy('user_id') as $userId => $userEmployees)
                    @php 
                        $firstEmployee = $userEmployees->first();
                        $userPermissions = $userEmployees->pluck('permission.name')->filter()->implode(', ');
                        $permissionIds = $userEmployees->pluck('permission_id')->toArray();
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3 justify-start dir-dependent-flex">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-[#1c75bc]">
                                    {{ substr($firstEmployee->user->fullname, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-gray-900">{{ $firstEmployee->user->fullname }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold" dir="ltr">{{ $firstEmployee->user->phone }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-6">
                            <p class="font-bold text-gray-600">{{ $firstEmployee->branch->name ?? trans('client.all_branches') }}</p>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2 justify-start dir-dependent-flex">
                                <span class="w-2 h-2 rounded-full {{ $firstEmployee->user->is_active ? 'bg-green-500' : 'bg-gray-300' }}"></span>
                                <span class="font-bold {{ $firstEmployee->user->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $firstEmployee->user->is_active ? trans('client.active') : trans('client.inactive') }}
                                </span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2 justify-start dir-dependent-flex">
                                <button onclick="openEditUserModal({
                                    id: {{ $userId }},
                                    fullname: '{{ addslashes($firstEmployee->user->fullname) }}',
                                    phone: '{{ $firstEmployee->user->phone }}',
                                    branch_id: {{ $firstEmployee->branch_id ?? 'null' }},
                                    permission_ids: {{ json_encode($permissionIds) }}
                                })"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="{{ trans('client.edit') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button onclick="openChangePasswordModal({{ $userId }}, '{{ addslashes($firstEmployee->user->fullname) }}')"
                                    class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="{{ trans('client.change_password') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </button>
                                <button onclick="handleDeleteEmployee({{ $userId }})"
                                    class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="{{ trans('client.delete') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                                <form id="delete-employee-{{ $userId }}" action="{{ route('client.employees.delete', $userId) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white p-12 rounded-3xl border border-gray-100 shadow-sm text-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <h3 class="text-xl font-black text-gray-900 mb-2">{{ trans('client.no_employees_yet') }}</h3>
        <p class="text-gray-500 font-bold mb-6">{{ trans('client.start_adding_team_desc') }}</p>
        <button onclick="openAddUserModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-[#1c75bc] text-white font-bold rounded-xl shadow-lg hover:bg-[#155a91] transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            {{ trans('client.add_first_employee') }}
        </button>
    </div>
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        if ($('#users-dataTable').length) {
            $('#users-dataTable').DataTable({
                "language": {
                    "url": "{{ app()->getLocale() == 'ar' ? '//cdn.datatables.net/plug-ins/1.13.7/i18n/ar.json' : '' }}"
                },
                "dom": 'rtp',
                "ordering": false
            });
        }
    });

    function openAddUserModal() {
        $('#user-modal-title').text("{{ trans('client.add_new_user') }}");
        $('#userForm').attr('action', "{{ route('client.employees.store') }}");
        $('#user-method-field').html('');
        
        // Reset form
        $('#userForm')[0].reset();
        $('.user-permission-checkbox').prop('checked', false);
        $('#user-fullname-input').prop('readonly', false).removeClass('bg-gray-50');
        $('#user-phone-input').prop('readonly', false).removeClass('bg-gray-50');
        
        openModal('user-modal');
    }

    function openEditUserModal(data) {
        $('#user-modal-title').text("{{ trans('client.edit_user_permissions') }}");
        $('#userForm').attr('action', "{{ url(app()->getLocale() . '/business/employees') }}/" + data.id);
        $('#user-method-field').html('@method("PUT")');
        
        // Populate fields
        $('#user-fullname-input').val(data.fullname).prop('readonly', true).addClass('bg-gray-50');
        $('#user-phone-input').val(data.phone).prop('readonly', true).addClass('bg-gray-50');
        $('#user-branch-id').val(data.branch_id);
        
        // Permissions
        $('.user-permission-checkbox').prop('checked', false);
        if (data.permission_ids) {
            data.permission_ids.forEach(pId => {
                $(`.user-permission-checkbox[value="${pId}"]`).prop('checked', true);
            });
        }
        
        openModal('user-modal');
    }

    function handleDeleteEmployee(id) {
        if (confirm("{{ trans('client.confirm_delete_employee') }}")) {
            document.getElementById('delete-employee-' + id).submit();
        }
    }

    function openChangePasswordModal(id, name) {
        $('#password-modal-title-user').text("تغيير كلمة مرور: " + name);
        $('#passwordForm').attr('action', "{{ url(app()->getLocale() . '/business/employees') }}/" + id + "/password");
        $('#passwordForm')[0].reset();
        openModal('password-modal');
    }
</script>
@endpush
@endsection