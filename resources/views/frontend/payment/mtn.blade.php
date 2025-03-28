 @extends('frontend.layouts.app')

 @section('content')
     <section class="container card border-1 rounded-2">
         <div class="aiz-editor-data">
             <img id="modalImage" src="{{ static_asset('assets/img/cards/mtn.png') }}" class="img-fit mb-2">
         </div>
         <div class="pb-5 pt-4 px-3 px-md-5">
             <h3>{{ translate('Make your payment securely! Fill in the form') }}</h3>
             <form action="{{ route('mtn.process.payment') }}" class="form-default was-validated" role="form" method="POST">
                 @csrf
                 <input value="mtnpay" class="online_payment" type="radio" id="payment_option" name="payment_option"
                     checked>
                 <div class="form-group mb-2">
                     <input type="number" class="form-control required-field" value="{{ old('reference', $reference) }}"
                         placeholder="Your phone number" name="reference" required>
                 </div>
                 <div class="form-group mb-2">
                     <input type="hidden" class="form-control" value="{{ old('amount', $amount) }}" name="amount"
                         readonly>
                     <input type="text" class="form-control bg-info" value="{{ old('price', $price) }}" name="price"
                         readonly>
                 </div>
                 <div class="pt-3 px-4 fs-14">
                     <label class="aiz-checkbox">
                         <input type="checkbox" required id="agree_checkbox_modal" onclick="syncCheckbox()">
                         <span class="aiz-square-check"></span>
                         <span>{{ translate('I agree to the') }}</span>
                     </label>
                     <a href="{{ route('terms') }}" class="fw-700">{{ translate('terms and conditions') }}</a>,
                     <a href="{{ route('returnpolicy') }}" class="fw-700">{{ translate('return policy') }}</a> &
                     <a href="{{ route('privacypolicy') }}" class="fw-700">{{ translate('privacy policy') }}</a>
                 </div>
                 <button type="submit"
                     class="btn btn-primary btn-block mt-3 rounded-0 px-4">{{ translate('Complete Order') }}</button>
             </form>
             <a href="{{ route('cart') }}" class="mt-3 fs-14 fw-700 d-flex align-items-center text-primary"
                 style="max-width: fit-content;">
                 <i class="las la-arrow-left fs-20 mr-1"></i>
                 {{ translate('Back to Previous Page') }}
             </a>
         </div>
     </section>
 @endsection
