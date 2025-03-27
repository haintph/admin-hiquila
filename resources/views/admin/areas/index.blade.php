@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="d-flex card-header justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">Danh Sách Khu Vực</h4>
                        </div>
                        <a href="{{ route('areas.create') }}" class="btn btn-sm btn-outline-light rounded">Thêm Khu Vực</a>
                    </div>
                    <div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover table-centered">
                                <thead class="bg-light-subtle">
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Khu Vực</th>
                                        <th>Ảnh Khu Vực</th>
                                        <th>Trạng Thái</th>
                                        <th>VIP</th>
                                        <th>Hút Thuốc</th>
                                        <th>Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($areas as $area)
                                        <tr>
                                            <td>{{ $area->area_id }}</td>
                                            <td>{{ $area->name }}</td>
                                            <td> <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                                <img src="{{ asset('storage/' . $area->image) }}" alt="" class="avatar-md">
                                           </div></td>
                                            <td>
                                                <span
                                                    class="badge {{ $area->status == 'Hoạt động' ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }} fs-12">
                                                    {{ $area->status }}
                                                </span>
                                            </td>
                                            <td>{{ $area->is_vip ? 'Có' : 'Không' }}</td>
                                            <td>{{ $area->is_smoking ? 'Có' : 'Không' }}</td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="{{ route('areas.edit', $area->area_id) }}"
                                                        class="btn btn-soft-primary btn-sm">
                                                        <iconify-icon icon="solar:pen-2-broken"
                                                            class="align-middle fs-18"></iconify-icon>
                                                    </a>
                                                    <form action="{{ route('areas.destroy', $area->area_id) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-soft-danger btn-sm"
                                                            onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"
                                                                class="align-middle fs-18"></iconify-icon>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer border-top">
                        {{ $areas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
