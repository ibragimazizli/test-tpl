@extends('layouts.app')

@section('title', __('messages.about-us'))

@section('content')
    <div class="about-us">
        @include('partials/mobile-menu')
        @include('partials/payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')

        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('about-us') }}" class="breadcrumb-item">{{ __('messages.about-us') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.about-us') }}</h1>
                </div>

                <div class="tab-box">
                    <div class="tab-header">
                        <button class="tab-btn active" data-target="#aboutTab">{{ __('messages.about-us') }}</button>
                        <button class="tab-btn" data-target="#privacyTab">{{ __('messages.privacy_policy') }}</button>
                    </div>

                    <div class="tab-content">
                        <div id="aboutTab" class="tab-section active">
                            <div class="about-container">
                                {!! __('messages.about_text') !!}
                            </div>
                        </div>

                        <div id="privacyTab" class="tab-section">
                            <div class="about-container">
                                {!! __('messages.privacy_policy_text') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        @include('partials/footer')
    </div>
@endsection

<style>
    .tab-box {
        margin-top: 30px;
    }

    .tab-header {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        border-bottom: 2px solid #eee;
    }

    .tab-btn {
        padding: 10px 20px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        color: #333;
        font-size: 16px;
        cursor: pointer;
        transition: 0.3s;
    }

    .tab-btn.active {
        border-color: #0598a0;
        color: #0598a0;
        background-color: #f6fafa;
    }

    .tab-content {
        background-color: #fff;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .tab-section {
        display: none;
    }

    .tab-section.active {
        display: block;
    }

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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabSections = document.querySelectorAll('.tab-section');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                // reset active
                tabButtons.forEach(b => b.classList.remove('active'));
                tabSections.forEach(s => s.classList.remove('active'));

                // activate clicked
                btn.classList.add('active');
                document.querySelector(btn.dataset.target).classList.add('active');
            });
        });
    });
</script>
