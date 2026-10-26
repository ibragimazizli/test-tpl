@extends('layouts.app')

@section('title', __('messages.contact'))

@section('content')
    <div class="contact">
        @include('partials/mobile-menu')
        @include('partials/payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')

        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('insurance-companies') }}" class="breadcrumb-item">{{ __('messages.breadcrumb_contact') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <img src="{{ asset('images/icons/icon-contact-3d.png') }}" alt="" />
                    <h1 class="title">{{ __('messages.contact_insurance_companies') }}</h1>
                    <p class="description">
                        {{ __('messages.contact_description') }}
                    </p>
                </div>
            </div>

            <div class="container">
                <div class="contact-list">
                    @foreach ($articles as $article)
                        <div class="contact-item">
                            <div class="contact-item__image">
                                <a href="{{$article->link }}" target="_blank" style="width: 140px !important;">
                                    <img src="{{ asset('images/logos/'. $article->file) }}" alt="{{ $article->title }}" />
                                </a>
                            </div>

                            <div class="contact-item__content">
                                <h3 class="contact-item-name"><a href="{{$article->link }}" target="_blank">{{ $article->title }}</a></h3>
                                @foreach (explode(';', $article->phone) as $phone)
                                    @php
                                        $cleanPhone = trim($phone);
                                    @endphp
                                    @if(!empty($cleanPhone))
                                        <p class="contact-item-phone">
                                            <a href="tel:{{ $cleanPhone }}">{{ $cleanPhone }}</a>
                                        </p>
                                    @endif
                                @endforeach
                                <p class="contact-item-mail"><a href="mailto:{{ $article->email }}">{{ $article->email }}</a></p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>

        @include('partials/footer')
    </div>
@endsection
