@extends('layouts.app')

@section('title', __('messages.tariffs'))

@section('content')
    <div class="tariffs">
        @include('partials/mobile-menu')
        @include('partials.payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')
        <main class="main">
            <div class="breadcrumb">
                <div class="container">
                    <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.breadcrumb_home') }}</a>
                    <a href="{{ route('tariffs') }}" class="breadcrumb-item">{{ __('messages.tariffs') }}</a>
                </div>
            </div>

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.tariffs') }}</h1>
                    <p class="description">
                        {{ __('messages.tariffs_description') }}
                    </p>
                </div>
                <div class="table-responsive">
                    <table class="tariff-table table">
                        <thead>
                        <tr>
                            <th>{{ __('messages.category') }}</th>
                            <th>{{ __('messages.duration_1_month') }}</th>
                            <th>{{ __('messages.duration_3_month') }}</th>
                            <th>{{ __('messages.duration_6_month') }}</th>
                            <th>{{ __('messages.duration_12_month') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ __('messages.bus_minibus_vehicles') }}</td>
                            <td>74</td>
                            <td>167</td>
                            <td>259</td>
                            <td>370</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.passenger_cars') }}</td>
                            <td>26</td>
                            <td>59</td>
                            <td>91</td>
                            <td>130</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.motorcycles') }}</td>
                            <td>19</td>
                            <td>43</td>
                            <td>67</td>
                            <td>95</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.trailers') }}</td>
                            <td>10</td>
                            <td>23</td>
                            <td>35</td>
                            <td>50</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.tractors_and_construction') }}</td>
                            <td>19</td>
                            <td>43</td>
                            <td>67</td>
                            <td>95</td>
                        </tr>
                        <tr>
                            <td>{{ __('messages.trucks') }}</td>
                            <td>97</td>
                            <td>218</td>
                            <td>340</td>
                            <td>485</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        @include('partials/footer')
    </div>
@endsection
<style>
    .tariff-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    /*table, th, td {*/
    /*    border: 1px solid #ccc;*/
    /*}*/

    .tariff-table th {
        background-color: #0598a0;
        color: #fff;
        padding: 12px;
        text-align: left;
    }

    .tariff-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .tariff-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .tariff-table tr:hover {
        background-color: #f1f1f1;
    }
    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-responsive table {
        min-width: 600px;
    }
</style>
