@extends('layouts.app')

@section('title', __('messages.manual_guide_for_buy_tpl'))

@section('content')
    <div class="manual-guide">
        @include('partials/mobile-menu')
        @include('partials/payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')

        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('manual-guide') }}" class="breadcrumb-item">{{ __('messages.breadcrumb_manual_guide') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.manual_guide_for_buy_tpl') }}</h1>
                    <p class="description">
                        {!! __('messages.manual_guide_description2') !!}
                    </p>
                </div>
                <div class="about-container">
                    {!! __('messages.manual_guide_description') !!}
                </div>
            </div>
            <div class="container">
{{--                <div class="video-list">--}}
{{--                    <a href="#" class="video-item">--}}
{{--                        <img src="{{ asset('images/manual-guide-1.png') }}" alt="" />--}}
{{--                    </a>--}}

{{--                    <a href="#" class="video-item">--}}
{{--                        <img src="{{ asset('images/manual-guide-2.png') }}" alt="" />--}}
{{--                    </a>--}}
{{--                </div>--}}
            </div>
        </main>

        @include('partials/footer')
    </div>
    <style>
        .about-container {
            line-height: 1.7;
            font-size: 16px;
        }

        .about-container h2 {
            font-size: 24px;
            margin-bottom: 16px;
            color: #0598a0;
        }

        .about-container ol, .about-container ul {
            margin-left: 20px;
            margin-bottom: 20px;
        }

        .about-container a {
            color: #0598a0;
            font-weight: 500;
            text-decoration: none;
        }

        .about-container a:hover {
            text-decoration: underline;
        }
    </style>
@endsection
