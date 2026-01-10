<div class="modal fade" id="top_up_modal" tabindex="-1" aria-modal="true" role="dialog"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content rounded">

            <div class="modal-header pb-0 border-0 justify-content-end">
                <div class="btn btn-sm btn-icon btn-active-color-primary"
                     data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </div>
            </div>

            <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                <form id="top_up_form" class="form" action="#">

                    <div class="mb-13 text-center">
                        <h1 class="mb-3">
                            {{__('Top Up Merchant Balance')}}
                        </h1>
                        <div class="text-muted fw-semibold fs-6">
                            {{__('Enter the amount you want to add to this merchant\'s balance.')}}
                        </div>
                    </div>

                    <div class="d-flex flex-column mb-8 fv-row fv-plugins-icon-container">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">
                                {{__('Amount')}}
                            </span>
                        </label>

                        <div class="d-flex flex-stack gap-5 mb-3">
                            <button type="button" class="btn btn-light-primary w-100" data-kt-modal-topup="option">20</button>
                            <button type="button" class="btn btn-light-primary w-100" data-kt-modal-topup="option">50</button>
                            <button type="button" class="btn btn-light-primary w-100" data-kt-modal-topup="option">100</button>
                        </div>

                        <input type="hidden" name="merchant_id" id="top_up_merchant_id">

                        <input type="text"
                               id="top_up_input"
                               class="form-control form-control-solid"
                               placeholder="Введите сумму"
                               name="topup_amount">
                    </div>

                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">
                            {{__('Cancel')}}
                        </button>
                        <button type="submit" id="top_up_submit" class="btn btn-primary">
                            <span class="indicator-label">
                                {{__('Top Up')}}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom_js')
    <script>
        $(document).ready(function() {
            $('[data-kt-modal-topup="option"]').click(function(e) {
                e.preventDefault();
                $('[name="topup_amount"]').val($(this).text());
            });

            Inputmask({
                alias: "currency",
                prefix: "₼ ",
                rightAlign: false,
                digits: 2,
                removeMaskOnSubmit: true
            }).mask("#top_up_input");

            // top up balance
            $('#top_up_modal').on('show.bs.modal', function (event) {
                const button = $(event.relatedTarget);
                const merchantId = button.data('merchant-id');
                const modal = $(this);

                modal.find('#top_up_merchant_id').val(merchantId);
                modal.find('[name="topup_amount"]').val('');
            });

            $('#top_up_form').submit(function(e) {
                e.preventDefault();
                applyWait($('body'));

                const merchantId = $('#top_up_merchant_id').val();
                const amount = $('[name="topup_amount"]').val();

                $.ajax({
                    url: '{{route('dashboard.merchants.topup')}}',
                    method: 'POST',
                    data: {
                        merchant_id: merchantId,
                        amount: amount,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        removeWait($('body'));

                        // Преобразуем копейки в манаты для отображения
                        const balanceInManats = (response.balance / 100).toFixed(2);
                        $(`#merchant_balance_${merchantId}`).text(`${balanceInManats} ₼`);

                        Swal.fire({
                            icon: 'success',
                            title: 'Баланс обновлен',
                            text: response.message
                        });
                        $('#top_up_modal').modal('hide');
                    },
                    error: function (xhr, status, error) {
                        removeWait($('body'));
                        const message = getAjaxErrorMessage(xhr) || 'Произошла ошибка';
                        Swal.fire({
                            icon: 'error',
                            title: 'Ошибка!',
                            html: message
                        });
                    }
                });
            });
        });
    </script>
@endpush
