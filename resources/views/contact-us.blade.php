@extends('layouts.app')

@section('title', __('messages.contact_us'))

@section('content')
    <div class="contact_us">
        @include('partials/mobile-menu')
        @include('partials.payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')
        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('contact-us') }}" class="breadcrumb-item">{{ __('messages.contact_us') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.contact_us') }}</h1>
                </div>
                <div class="contact-us-wrapper">
                    <div class="contact-left">
                        <h2>{{ __('messages.get_in_touch') }}</h2>

                        <div class="contact-info">
                            <div class="contact-item">
                                <h3>{{ __('messages.address') }}</h3>
                                <p>{{ __('messages.contact_address_text') }}</p>
                            </div>

                            <div class="contact-item">
                                <h3>{{ __('messages.email') }}</h3>
                                <p><a href="mailto:info@isb.az">info@tpl.az</a></p>
                            </div>

                            <div class="contact-item">
                                <h3>{{ __('messages.phone') }}</h3>
                                <p><a href="tel:(+99412) 525 97 07">(+99412) 525 97 07</a></p>
                            </div>

                            <div class="contact-item">
                                <h3>{{ __('messages.greencard') }}</h3>
                                <p><a href="tel:(+99412) 525 97 07">(+99412) 525 97 07</a></p>
                                <p><a href="mailto:greencard@isb.az">greencard@isb.az</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="contact-right">
                        <div class="map">
                            <iframe width="100%" height="100%" frameborder="0" scrolling="no"
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3039.244315152703!2d49.808878!3d40.381277499999996!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40307dba30df9b4b%3A0x1c6fe9d47171176f!2s%C4%B0cbari%20S%C4%B1%C4%9Forta%20B%C3%BCrosu%20(Compulsory%20Insurance%20Bureau)!5e0!3m2!1sen!2saz!4v1746456926489!5m2!1sen!2saz"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('partials/footer')
    </div>
    <style>
        .contact-us-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-top: 30px;
        }

        .contact-left {
            flex: 1 1 400px;
        }

        .contact-right {
            flex: 1 1 500px;
            min-height: 400px;
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 20px;
        }

        .contact-item h3 {
            margin-bottom: 6px;
            color: #298d9c;
            font-size: 16px;
        }

        .contact-item p,
        .contact-item a {
            margin: 0;
            color: #333;
            font-size: 15px;
            line-height: 1.5;
            text-decoration: none;
        }

        .contact-right .map {
            width: 100%;
            height: 100%;
            min-height: 400px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
    </style>
@endsection

