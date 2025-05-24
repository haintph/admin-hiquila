@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa giao dịch kho')

@section('content')
<div class="container mt-4">
    <h2>Chỉnh sửa giao dịch kho</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $currentInventory = $inventories->firstWhere('id', $inventoryLog->inventory_id);
    @endphp

    <form action="{{ route('inventory_logs.update', $inventoryLog->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Nguyên liệu</label>
            <select name="inventory_id" class="form-control" required>
                @foreach ($inventories as $inventory)
                    <option value="{{ $inventory->id }}" 
                        data-quantity="{{ $inventory->quantity }}"
                        data-min_quantity="{{ $inventory->min_quantity }}"
                        {{ $inventoryLog->inventory_id == $inventory->id ? 'selected' : '' }}>
                        {{ $inventory->name }} ({{ $inventory->quantity }} {{ $inventory->unit }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Loại giao dịch</label>
            <select name="type" class="form-control" id="transactionType" required>
                <option value="import" {{ $inventoryLog->type == 'import' ? 'selected' : '' }}>Nhập kho</option>
                <option value="export" {{ $inventoryLog->type == 'export' ? 'selected' : '' }}>Xuất kho</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="quantity" class="form-control" id="quantityInput" step="0.01" value="{{ $inventoryLog->quantity }}" required>
            <small class="text-danger d-none" id="warningMessage">⚠️ Số lượng xuất kho lớn hơn tồn kho tối thiểu!</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Tồn tối thiểu</label>
            <input type="number" class="form-control" id="minQuantityInput" value="{{ $currentInventory->min_quantity ?? 0 }}" readonly>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá nhập (nếu có)</label>
            <input type="number" name="cost" class="form-control" step="0.01" value="{{ $inventoryLog->cost }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Ghi chú</label>
            <input type="text" name="note" class="form-control" value="{{ $inventoryLog->note }}">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('inventory_logs.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const transactionType = document.getElementById("transactionType");
        const quantityInput = document.getElementById("quantityInput");
        const minQuantityInput = document.getElementById("minQuantityInput");
        const warningMessage = document.getElementById("warningMessage");

        transactionType.addEventListener("change", checkQuantity);
        quantityInput.addEventListener("input", checkQuantity);

        function checkQuantity() {
            let selectedInventory = document.querySelector('select[name="inventory_id"] option:checked');
            let currentQuantity = parseFloat(selectedInventory.getAttribute("data-quantity")) || 0;
            let minQuantity = parseFloat(selectedInventory.getAttribute("data-min_quantity")) || 0;
            let enteredQuantity = parseFloat(quantityInput.value) || 0;

            if (transactionType.value === "export" && (currentQuantity - enteredQuantity < minQuantity)) {
                warningMessage.classList.remove("d-none");
            } else {
                warningMessage.classList.add("d-none");
            }
        }
    });
</script>
@endsection
