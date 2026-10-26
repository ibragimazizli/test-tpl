@if(!request()->query('terminal'))
<div class="modal pay-payment-modal">
    <div class="modal-top">
        <div class="modal-top__left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-credit-card-2.svg') }}" alt="" />
            </div>
            <h3 class="modal-title">{{ __('messages.payment') }}</h3>
        </div>
        <div class="modal-top__right" style="display: flex; gap: 10px">
                <button type="button" onclick="generatePDFFromModal('.pay-payment-modal')" id="download-payment-pdf"
                    style="background:transparent; border:none; cursor:pointer;" class="button-icon-transparent">
                <img src="{{ asset('images/icons/icon-download-2.svg') }}" alt="" />
            </button>
                <button type="button" class="modal-close">
                <img src="{{ asset('images/icons/icon-close.svg') }}" alt="" />
            </button>
        </div>
    </div>

    <div class="modal-main">
        <div class="modal-list">
            <input type="hidden" id="transaction-id" value="">
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.receiver') }}</div>
                <div class="modal-item-content receiver-name">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.payment_code') }}:</div>
                <div class="modal-item-content invoice-number">-</div>
            </div>

            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.payer') }}</div>
                <div class="modal-item-content payer-name">-</div>
            </div>

            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.plate') }}:</div>
                <div class="modal-item-content makePayment">-</div>
            </div>

            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.amount_due') }}</div>
                <div class="modal-item-content amount-due">-</div>
            </div>

            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.last_payment_date') }}</div>
                <div class="modal-item-content due-date">-</div>
            </div>

            <div class="modal-item modal-item-full  no-print">
                <div class="modal-item-title">
                    {{ __('messages.enter_last_six_numbers_of_card_number') }}
                </div>
                <input
                    id="card-no"
                    type="text"
                    class="input input--full"
                    placeholder="XXXX XX"
                />
            </div>
        </div>
    </div>
    <div class="alert alert-error alert_pay_payment_modal" style="display: none">
        <div class="alert-left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
            </div>
        </div>
        <div class="alert-right">
        </div>
    </div>

    <div class="modal-bottom">
        <button class="button button-primary button--full button-pay2 disabled" >
            {{ __('messages.pay_now') }}
        </button>
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <p>{{ __('messages.additional_charges') }}
           <span>{{ __('messages.payment_and_print_info') }}</span></p>
        </div>
        <span id="pdf-title-text" style="display:none;">{{ __('messages.payment_information') }}</span>
        <span id="pdf-info-text" style="display:none;">{{ __('messages.information') }}</span>
        <span id="pdf-footer-text" style="display:none;">{!! __('messages.pdf_footer_text') !!}</span>
{{--        <div class="download">--}}
{{--            <div class="download-item">--}}
{{--                <img src="{{ asset('images/icons/icon-doc.svg') }}" alt="" />--}}
{{--                <h5 class="download-title">{{ __('messages.tariff_information') }}</h5>--}}
{{--            </div>--}}
{{--            <div class="download-right">--}}
{{--                <a href="#" class="download-item">--}}
{{--                    <img src="{{ asset('images/icons/icon-download.svg') }}" alt="" />--}}
{{--                    <h5 class="download-title">{{ __('messages.download') }}</h5>--}}
{{--                </a>--}}
{{--                <p>0.7 mb</p>--}}
{{--            </div>--}}
{{--        </div>--}}
    </div>
</div>
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
    .info-box {
        display: flex;
        align-items: center;
        gap: 8px;
        background-color: #f5fafa;
        border-left: 4px solid #59b4b0;
        padding: 10px 12px;
        font-family: Arial, sans-serif;
        color: #333;
        border-radius: 4px;
        max-width: 600px;
    }

    .info-box i {
        color: #59b4b0;
        opacity: 0.6;
        font-size: 16px;
    }

    .info-box p {
        margin: 0;
        font-size: 13px;
        line-height: 1.5;
    }
    .info-box span {
        color: #e62020;
    }
</style>
<script>
    const alert_pay_payment_modal = document.querySelector(".alert_pay_payment_modal");
    document.querySelector('#card-no').addEventListener('input', function () {
        const btn = document.querySelector('.button-pay2');
        const pattern = /\d+/g;
        const match = this.value.match(pattern);
        const cleaned = match ? match[0].slice(0, 6) : '';
        this.value = cleaned;
        const isValid = cleaned.length === 6;
        btn.disabled = !isValid;
        if (isValid) {
            btn.classList.remove('disabled');
        } else {
            btn.classList.add('disabled');
        }
    });
    document.querySelector('.button-pay2').addEventListener('click', async function () {
        const cardNumber = document.querySelector('#card-no').value;
        const transactionId = window.paymentState.transactionId;
        const invoiceList = window.paymentState.invoiceList;

        if (cardNumber.length !== 6 || !transactionId || invoiceList.length === 0) {
            showAlert(alert_pay_payment_modal, '{{ __('messages.invoice_information_problem') }}', [
                '{{ __('messages.invoice_or_card_number_data_problem') }}'], 'error');
            return false
        }
        showLoader()

        const redirectUrl = `/payment-status/${transactionId}`;

        try {
            const response = await callService('/callService/payment/payInvoices', {
                input: {
                    transactionId,
                    errorRedirectUrl: redirectUrl,
                    successRedirectUrl: redirectUrl,
                    invoices: invoiceList,
                    params: {
                        CARD_NUMBER: cardNumber
                    }
                }
                });
            const data = response.data;
            if(data?.response && data.response.code ==='0') {
                if (data.redirectUrl) {
                    window.paymentState = {
                        invoiceList: [],
                        transactionId: '',
                    };
                    document.querySelector('.pay-payment-modal').classList.remove('active');
                    document.querySelector('.overlay').classList.remove('active');
                    window.location.href = data.redirectUrl;
                } else {
                    const errorMsg = '{{ __('messages.REDIRECT_URL_NOT_EXISTS_IN_SERVICE') }}';
                    showAlert(alert_pay_payment_modal, '{{ __('messages.payment_service_failed') }}', [errorMsg], 'error');
                    return false;
                }
            }else{
                const backendMessage = data.response.message || '{{ __('messages.payment_service_failed') }}';
                showAlert(alert_pay_payment_modal, '{{ __('messages.payment_service_failed') }}', [backendMessage], 'error');
            }

        } catch (error) {
            if (error.response && error.response.data?.response) {
                const backendMessage = error.response.data.response.message;
                showAlert(alert_pay_payment_modal, "{{ __('messages.service_error') }}", [backendMessage], 'error');
            }else{
                const errorMessage = '{{ __('messages.payment_service_failed') }}';
                showAlert(alert_pay_payment_modal, '{{ __('messages.payment_service_failed') }}', [errorMessage], 'error');
            }


        }finally {
            hideLoader();
        }
    });




</script>
@endif
