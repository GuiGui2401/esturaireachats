<div class="modal-header">
    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Make Payment') }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form class="was-validated " action="{{ route('purchase_history.make_payment') }}" method="post"
    enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="order_id" value="{{ $order->id }}">
    <div class="modal-body gry-bg px-3 pt-3 mx-auto c-scrollbar-light">
        <div class="align-items-center gutters-5 row">
            <!-- Manual Payment Methods -->
            @foreach (get_all_manual_payment_methods() as $method)
                <div class="col-6 col-md-4">
                    <label class="aiz-megabox d-block mb-3">
                        <input value="{{ $method->heading }}" type="radio" name="payment_option"
                            onchange="toggleManualPaymentData({{ $method->id }})" data-id="{{ $method->id }}"
                            checked>
                        <span class="d-block p-3 aiz-megabox-elem">
                            <img loading="lazy" src="{{ uploaded_asset($method->photo) }}" class="img-fluid mb-2">
                            <span class="d-block text-center">
                                <span class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                            </span>
                        </span>
                    </label>
                </div>
            @endforeach
        </div>

        <div id="manual_payment_data">
            <!-- Payment description -->
            <div class="card mb-3 p-3 d-none">
                <div id="manual_payment_description">

                </div>
            </div>

            <div class="card mb-3 p-3">
                <!-- Amount -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label>{{ translate('Amount') }} <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="number" lang="en" class="form-control mb-3 rounded-0" min="0"
                            step="0.01" name="amount" placeholder="{{ translate('Amount') }}" required>
                    </div>
                </div>
                <!-- Name -->
                <div class="row">
                    <div class="col-md-3">
                        <label>{{ translate('Name') }} <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control mb-3 rounded-0" name="name"
                            placeholder="{{ translate('Name') }}" required>
                    </div>
                </div>
                <!-- Transaction ID -->
                <div class="row">
                    <div class="col-md-3">
                        <label>{{ translate('Transaction ID') }} <span class="text-danger">*</span></label>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control mb-3 rounded-0" name="trx_id"
                            placeholder="{{ translate('Transaction ID') }}" required>
                    </div>
                </div>
                <!-- Payment screenshot -->
                <div class="form-group row">
                    <label class="col-md-3 col-form-label">{{ translate('Payment screenshot') }}</label>
                    <div class="col-md-9">
                        <div class="input-group" data-toggle="aizuploader" data-type="image" required>
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium rounded-0">
                                    {{ translate('Browse') }}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose image') }}</div>
                            <input type="hidden" name="photo" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Confirm Button -->
        <div class="form-group text-right">
            <button type="submit"
                class="btn btn-primary rounded-0 transition-3d-hover mr-1">{{ translate('Confirm') }}</button>
        </div>
    </div>
</form>

@foreach (get_all_manual_payment_methods() as $method)
    <div id="manual_payment_info_{{ $method->id }}" class="d-none">
        <div>@php echo $method->description @endphp</div>
        @if ($method->bank_info != null)
            <ul>
                @foreach (json_decode($method->bank_info) as $key => $info)
                    <li>{{ translate('Bank Name') }} - {{ $info->bank_name }}, {{ translate('Account Name') }} -
                        {{ $info->account_name }}, {{ translate('Account Number') }} - {{ $info->account_number }},
                        {{ translate('Routing Number') }} - {{ $info->routing_number }}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endforeach

<script type="text/javascript">
    $(document).ready(function() {
        toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
    });

    function toggleManualPaymentData(id) {
        $('#manual_payment_description').parent().removeClass('d-none');
        $('#manual_payment_description').html($('#manual_payment_info_' + id).html());
    }
</script>
