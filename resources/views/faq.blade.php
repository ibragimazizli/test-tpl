@extends('layouts.app')

@section('title', __('messages.faq'))

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
                    <a href="{{ route('faq') }}" class="breadcrumb-item">{{ __('messages.faq') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.faq') }}</h1>
                    <p class="description">
                        {{ __('messages.faq_description') }}
                    </p>
                </div>

                <div class="faq-list">
                    @foreach ($faqs as $index => $faq)
                        <div class="faq-item">
                            <div class="faq-item__top">
                                <h2>{{ $faq['question'] }}</h2>
                                <div class="icon">
                                    <img src="{{ asset('images/icons/icon-plus.svg') }}" alt="" class="icon-plus"/>
                                    <img src="{{ asset('images/icons/icon-minus.svg') }}" alt="" class="icon-minus"/>
                                </div>
                            </div>
                            <div class="faq-item__main">
                                <p>{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>

        @include('partials/footer')
    </div>
@endsection
