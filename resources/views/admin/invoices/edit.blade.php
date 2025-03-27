@extends('admin.layouts.master')

@section('content')
    <div class="container-xxl">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card">
                    <div class="card-body">
                        <div class="pb-3 mb-4 position-relative border-bottom">
                            <div class="row justify-content-between">
                                <div class="col-lg-5">
                                    <form action="{{ route('invoices.addDish', $invoice->invoice_id) }}" method="POST">
                                        @csrf
                                        <div class="mb-3 position-relative">
                                            <label for="dish_search" class="form-label text-dark">Chọn món ăn:</label>
                                            <input type="text" id="dish_search" class="form-control"
                                                placeholder="Nhập tên món ăn..." autocomplete="off">
                                            <input type="hidden" name="dish_id" id="dish_id">
                                            <div id="dish_list" class="list-group position-absolute w-100 d-none"></div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="quantity">Số lượng:</label>
                                            <input type="number" name="quantity" id="quantity" class="form-control"
                                                min="1" required>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-cart-plus"></i> Thêm món
                                        </button>
                                    </form>
                                </div>
                                <div class="col-lg-5">
                                    <h5 class="text-primary"><i class="fas fa-chair"></i> Số bàn:
                                        <strong>{{ $invoice->table->table_number }}</strong>
                                    </h5>
                                    <h5 class="text-danger mt-2"><i class="fas fa-money-bill-wave"></i> Tổng tiền:
                                        <strong>{{ number_format($invoice->total_price, 0, ',', '.') }} VND</strong>
                                    </h5>
                                    <a href="{{ route('vnpay.payment', $invoice->invoice_id) }}" class="btn btn-primary mt-3 w-100">
                                        <i class="fas fa-qrcode"></i> Thanh Toán Qua VNPAY
                                    </a>                                    
                                    <a href="{{ route('paypal.payment', $invoice->invoice_id) }}"
                                        class="btn btn-warning mt-3 w-100">
                                        <i class="fab fa-paypal"></i> Thanh Toán Qua PayPal
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="table-responsive table-borderless text-nowrap table-centered">
                                    <table class="table mb-0">
                                        <thead class="bg-light bg-opacity-50">
                                            <tr>
                                                <th class="border-0 py-2">Món ăn</th>
                                                <th class="border-0 py-2">Số lượng</th>
                                                <th class="border-0 py-2">Giá</th>
                                                <th class="border-0 py-2">Thành tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->items as $item)
                                                <tr>
                                                    <td>{{ $item->dish->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                                                    <td>{{ number_format($item->quantity * $item->price, 0, ',', '.') }} VND
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $("#dish_search").on("keyup", function() {
                let search = $(this).val();
                if (search.length < 1) {
                    $("#dish_list").html("").addClass("d-none");
                    return;
                }

                $.ajax({
                    url: "{{ route('dishes.search') }}",
                    type: "GET",
                    data: {
                        search: search
                    },
                    success: function(data) {
                        let list = "";
                        data.forEach(function(dish) {
                            list +=
                                `<a href="#" class="list-group-item list-group-item-action dish-item" data-id="${dish.id}">${dish.name} - ${new Intl.NumberFormat().format(dish.price)} VND</a>`;
                        });

                        $("#dish_list").html(list).removeClass("d-none");
                    }
                });
            });

            $(document).on("click", ".dish-item", function(e) {
                e.preventDefault();
                $("#dish_search").val($(this).text());
                $("#dish_id").val($(this).data("id"));
                $("#dish_list").html("").addClass("d-none");
            });

            $(document).click(function(e) {
                if (!$(e.target).closest(".mb-3").length) {
                    $("#dish_list").html("").addClass("d-none");
                }
            });
        });
    </script>
@endsection
