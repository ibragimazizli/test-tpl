@extends('layouts.app')

@section('title', __('messages.payment_status'))

@section('content')
    <div class="faq">
        @include('partials/mobile-menu')
        @include('partials.payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')
        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('payment-status') }}" class="breadcrumb-item">{{ __('messages.payment_status') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header" style="margin-bottom: 56px;">
                    <h1 class="title">{{ __('messages.payment_status') }}</h1>
                    <p class="description">
                        {{ __('messages.payment_status_description') }}
                    </p>
                </div>
                <div class="page-content">
                    <div class="row">
                        <div class="col-md-6 offset-md-3 col-sm-12 form-group" id="loadingStatus" style="display: none;">
                            <span class="spinner-border text-primary spinner-border-sm mt-3 mr-2"></span>
                            Məlumatlar yoxlanılır. Zəhmət olmasa gözləyin
                        </div>

                        <div class="col-md-12 col-sm-12 form-group">
                            <div class="alert alert-info alert_payment_status" style="display: none">
                                <div class="alert-left">
                                    <div class="icon">
                                        <img src="{{ asset('images/icons/icon-payment.svg') }}" alt="" />
                                    </div>
                                </div>
                                <div class="alert-right">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include('partials/footer')
    </div>

    <script>
        const alert_payment_status = document.querySelector(".alert_payment_status");
        const transactionId = "{{ $transactionId }}";
        async function checkPaymentStatus() {
            showLoader();
            try {
                const response = await callService('/callService/payment/invoiceStatus', {
                    input: {
                        searchParam: transactionId,
                        paymentChannel: 'HOP',
                        searchType: 'BY_TRANSACTION'
                    }
                });
                const result = response.data;

                if (result?.invoiceStatus) {
                    const status = result.invoiceStatus;
                    const status2 = result.status;
                    if (['SUCCESS', 'PAID', 'DUE_DATE_PASS'].includes(status)) {
                        showAlert(alert_payment_status, "{{ __('messages.success_payment_status') }}", [
                            status2
                        ], 'success');
                    } else {
                        showAlert(alert_payment_status, "{{ __('messages.un_success_payment_status') }}", [
                            status2
                        ], 'error', '/images/icons/icon-payment-failed.svg');
                    }
                } else if (result?.response?.message !== 'ok') {
                    const backendMessage = result.response?.message || "Servisdən xəta alındı.";
                    showAlert(alert_payment_status, "{{ __('messages.service_error') }}", [backendMessage], "error", '/images/icons/icon-warning.svg');
                    return false;
                }
            } catch (error) {
                if (error.response && error.response.data?.response) {
                    const backendMessage = error.response.data.response.message;
                    showAlert(alert_payment_status, "{{ __('messages.SERVICE_ERROR') }}", [backendMessage], 'error', '/images/icons/icon-warning.svg');
                }
            }
            finally {
                hideLoader();
            }
        }
        setTimeout(() => {
            checkPaymentStatus();
        }, 2000);
    </script>
@endsection
