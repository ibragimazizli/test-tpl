@extends('layouts.app')

@section('title', __('messages.registration'))

@section('content')
    <div class="registration">
        @include('partials/mobile-menu')
        @include('partials/payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/confirmation-modal')
        @include('partials/header')

        <main class="main">
            @php
                $isTerminal = request()->query('source') === 'terminal';
            @endphp
            @if(!$isTerminal)
            <div class="breadcrumb">
                <div class="container">
                        <a href="{{ route('home') }}" class="breadcrumb-item active">{{ __('messages.home') }}</a>
                        <a href="{{ route('registration') }}" class="breadcrumb-item">{{ __('messages.contract_registration') }}</a>
                </div>
            </div>
            @endif

            <div class="container container--md">
                <div class="page-header">
                    <h1 class="title">{{ __('messages.new_border_contract_registration') }}</h1>
                </div>

                <div class="stepper">
                    <div class="stepper-item">
                        <div class="stepper-item__icon">
                            <i class="fa-solid fa-check fa-xl"></i>
                        </div>

                        <h4>{{ __('messages.get_border_insurance') }}</h4>
                    </div>

                    <div class="stepper-item">
                        <div class="stepper-item__icon">
                            <i class="fa-solid fa-check fa-xl"></i>
                        </div>

                        <h4>{{ __('messages.personal_data') }}</h4>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="select-companies">
                        <label for="insurance-company" class="required">{{ __('messages.select_insurance_company') }}</label>

                        <div class="select-companies-list" style="width: 100%; overflow-x: auto;">
                            @foreach ($articles as $article)
                                <div class="companies-item" data-company-tin="{{ $article->tin }}" data-company-name="{{ $article->title }}">
                                    <img loading="lazy" src="{{ asset('images/logos/' . $article->file) }}" alt="{{ $article->title }}" />
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="inputs first-tab-first-items">
                        <div class="inputs-row">
                            <div class="inputs-item">
                                <label for="plate-number" class="required">{{ __('messages.plate_number') }}</label>
                                <input autocomplete="off" type="text" placeholder="{{ __('messages.plate_number') }}"
                                    id="plate-number"
                                    name="plate-number"
                                    class="input onlyLettersNumbers maxLength-10"
                                    data-required="true"
                                />
                            </div>

                            <div class="inputs-item">
                                <label for="doc-number" class="required">{{ __('messages.vehicle_registration_document_number') }}</label>
                                <input autocomplete="off" type="text" placeholder="{{ __('messages.document_number') }}"
                                    id="doc-number"
                                    name="doc-number"
                                    class="input onlyLettersNumbers maxLength-20"
                                    data-required="true"
                                />
                            </div>
                            <div class="inputs-item">
                                <label for="vehicle-type" class="required">{{ __('messages.vehicle_type') }}</label>

                                <div class="dropdown">
                                    <button class="dropdown__button" type="button" data-vehicle-type>
                                        <span>{{ __('messages.vehicle_type') }}</span>
                                        <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                    </button>

                                    <ul class="dropdown__list">
                                        <div class="dropdown__list-items" data-vehicle-type></div>
                                    </ul>

                                    <input autocomplete="off" class="dropdown__input_hidden" type="text" name="vehicle-type"
                                        value="" data-required="true" />
                                </div>
                            </div>
                        </div>

                        <div class="inputs-actions">
                            <button  type="button" class="button button-outline button-search button-search-vehicle">
                                {{ __('messages.check') }}
                            </button>
                        </div>

                        <div class="alert alert-error alert_first_tab_first_items" style="display: none">
                            <div class="alert-left">
                                <div class="icon">
                                    <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                </div>
                            </div>
                            <div class="alert-right">
                                <h4 class="alert-title">{{ __('messages.all_input_values_needed') }}</h4>
                                <p class="alert-text">
                                    {{ __('messages.please_insert_plate_and_doc_number_and_vehicle_type') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="inputs first-tab-second-items">
                        <div class="inputs-row">
                            <div class="inputs-item">
                                <label for="vehicle-make" class="required">{{ __('messages.vehicle_make') }}</label>
                                <div class="dropdown">
                                    <button class="dropdown__button" type="button" data-make>
                                        <span>{{ __('messages.vehicle_make') }}</span>
                                        <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                    </button>

                                    <ul class="dropdown__list search">
                                        <div class="dropdown-search-input-wrapper">
                                            <input autocomplete="off" type="text"
                                                class="input input--full dropdown-search-input notUppercase"
                                                   placeholder="{{ __('messages.search_make') }}" data-search-make />
                                            <div class="dropdown-search-input-icon">
                                                <img src="{{ asset('images/icons/icon-search.svg') }}" alt="" />
                                            </div>
                                        </div>
                                        <div class="dropdown__list-items" data-make></div>
                                    </ul>

                                    <input autocomplete="off" class="dropdown__input_hidden" type="text" name="vehicle-make"
                                        value="" data-required="true" />
                                </div>
                            </div>

                            <div class="inputs-item">
                                <label for="vehicle-model" class="required">{{ __('messages.vehicle_model') }}</label>
                                <div class="dropdown">
                                    <button class="dropdown__button" type="button" data-model>
                                        <span>{{ __('messages.vehicle_model') }}</span>
                                        <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                    </button>

                                    <ul class="dropdown__list search">
                                        <div class="dropdown-search-input-wrapper">
                                            <input autocomplete="off" type="text"
                                                class="input input--full dropdown-search-input notUppercase"
                                                   placeholder="{{ __('messages.search_model') }}" data-search-model />
                                            <div class="dropdown-search-input-icon">
                                                <img src="{{ asset('images/icons/icon-search.svg') }}" alt="" />
                                            </div>
                                        </div>
                                        <div class="dropdown__list-items" data-model></div>
                                    </ul>

                                    <input autocomplete="off" class="dropdown__input_hidden" type="text"
                                        name="vehicle-model" value="" data-required="true" />
                                </div>
                            </div>

                            <div class="inputs-item">
                                <label for="vehicle-registered-country" class="required">{{ __('messages.vehicle_registered_country') }}</label>
                                <div class="dropdown">
                                    <button class="dropdown__button" type="button" data-country>
                                        <span>{{ __('messages.vehicle_country') }}</span>
                                        <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                    </button>

                                    <ul class="dropdown__list search">
                                        <div class="dropdown-search-input-wrapper">
                                            <input autocomplete="off" type="text"
                                                class="input input--full dropdown-search-input notUppercase"
                                                   placeholder="{{ __('messages.search_country') }}" data-search-country />
                                            <div class="dropdown-search-input-icon">
                                                <img src="{{ asset('images/icons/icon-search.svg') }}" alt="" />
                                            </div>
                                        </div>
                                        <div class="dropdown__list-items" data-country></div>
                                    </ul>

                                    <input autocomplete="off" class="dropdown__input_hidden" type="text"
                                        name="vehicle-country" value="" data-required="true" />
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-error alert_first_tab_validate_items" style="display: none">
                            <div class="alert-left">
                                <div class="icon">
                                    <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                </div>
                            </div>
                            <div class="alert-right">
                                <h4 class="alert-title">{{ __('messages.please_fill_all_required_fields') }}</h4>
                            </div>
                        </div>
                        <div class="inputs-actions">
                            <button type="button" class="button button-primary button-next">{{ __('messages.next') }}</button>
                        </div>
                    </div>
                </div>

                <div class="tab-content">
                    <div class="tabs">
                        <div class="tabs-item active" data-tab="individual">
                            {{ __('messages.physical_person') }}
                        </div>
                        <div class="tabs-item" data-tab="legal-entity">{{ __('messages.legal_person') }}</div>
                    </div>

                    <div class="person-content active" id="individual">
                        <div class="citizen-list">
                            <div class="citizen-list-item">
                                <input type="radio" id="non-resident-citizen-foreign" name="citizen" value="0" checked />
                                <label for="non-resident-citizen-foreign">{{ __('messages.non_resident_foreign_citizen') }}</label>
                            </div>
                            <div class="citizen-list-item">

                                <input type="radio" id="citizen-aze" name="citizen" value="1" />
                                <label for="citizen-aze">{{ __('messages.azerbaijan_citizen') }}</label>
                            </div>
                            <div class="citizen-list-item">
                                <input type="radio" id="resident-citizen-foreign" name="citizen" value="2"  />
                                <label for="resident-citizen-foreign">{{ __('messages.resident_foreign_citizen') }}</label>
                            </div>

                        </div>

                        <div class="inputs second-tab-physical-first-items">
                            <div class="inputs-row citizen-inputs-container">
                                <div class="inputs-item">
                                    <label for="passport-number" class="required">{{ __('messages.passport_number') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.passport_number') }}"
                                        id="passport-number"
                                        name="passport-number"
                                        class="input onlyLettersNumbers"
                                        data-required="true"
                                    />
                                </div>

                                <div class="inputs-item pin-with-serial-number" >
                                    <label for="serial-number" class="required">{{ __('messages.serial_number') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.serial_number') }}"
                                        id="serial-number"
                                        name="serial-number"
                                        class="input onlyLettersNumbers"
                                        data-required="true"
                                    />
                                </div>
                                <div class="inputs-item pin-with-birthday" style="display: none">
                                    <label for="birthday" class="required">{{ __('messages.birthdate') }}</label>
                                    <input autocomplete="off" type="text" class="input" name="birthday" id="birthday"
                                        placeholder="DD-MM-YYYY" data-required="true" />
                                </div>
                            </div>

                            <div class="inputs-actions">
                                <button type="button" class="button button-outline button-search button-search-person">
                                    {{ __('messages.check') }}
                                </button>
                            </div>
                            <div class="alert alert_second_tab_input_items" style="display: none">
                                <div class="alert-left">
                                    <div class="icon">
                                        <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                    </div>
                                </div>
                                <div class="alert-right">
                                    <h4 class="alert-title">{{ __('messages.DATA_NOT_FOUND_ERROR') }}</h4>
                                    <p class="alert-text">
                                        {{ __('messages.DATA_NOT_FOUND_ERROR') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="inputs second-tab-physical-second-items">
                            <!-- All inputs inside one container -->
                            <div class="inputs-row citizen-details citizen-details-container active">

                                <!-- Only for non-resident foreign (value = 0) -->
                                <div class="inputs-item only-non-resident" style="display: none;">
                                    <label class="required" for="name">{{ __('messages.name') }}</label>
                                    <input autocomplete="off" type="text" data-required="true"
                                        placeholder="{{ __('messages.name') }}" id="name"
                                        class="input onlyName maxLength-20" />
                                </div>

                                <div class="inputs-item only-non-resident" style="display: none">
                                    <label class="required" for="surname">{{ __('messages.surname') }}</label>
                                    <input autocomplete="off" type="text" data-required="true"
                                        placeholder="{{ __('messages.surname') }}" id="surname"
                                        class="input onlyName maxLength-20" />
                                </div>
                                <div class="inputs-item only-non-resident" style="display: none">
                                    <label for="father-name">{{ __('messages.father_name') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.father_name') }}"
                                        id="father-name" class="input onlyName maxLength-20" />
                                </div>

                                <div class="inputs-item only-non-resident" style="display: none">
                                    <label class="required" for="living-address">{{ __('messages.living_address') }}</label>
                                    <input type="text" data-required="true" placeholder="{{ __('messages.living_address') }}" id="living-address" autocomplete="off" autocorrect="off" class="input onlyForAddress" />
                                </div>

                                <!-- For all types -->
                                <div class="inputs-item">
                                    <label class="required" for="mobile-number">{{ __('messages.mobile') }}</label>
                                    <input autocomplete="off" type="tel" data-required="true" id="mobile-number"
                                        name="mobile-number" class="mobile_number input input-tel notUppercase" />
                                </div>

                                <div class="inputs-item">
                                    <label for="email">{{ __('messages.email') }}</label>
                                    <input autocomplete="off" type="email" placeholder="{{ __('messages.email') }}"
                                        id="email" class="input notUppercase" />
                                </div>
                                <div class="inputs-item">
                                    <label class="required" for="radio-group-insurance">{{ __('messages.insurance_period') }}</label>
                                    <div class="radio-group-insurance" data-insurance-period>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="1" />
                                            <span>{{ __('messages.1_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="3" />
                                            <span>{{ __('messages.3_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="6" />
                                            <span>{{ __('messages.6_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="12" />
                                            <span>{{ __('messages.12_month') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div style="display: none" class="insurance-fee inputs-item second-tab-same-items">
                                    <label for="insurance_amount">{{ __('messages.insurance_amount') }}</label>
                                    <span name="insurance_amount" class="insurance_amount input notUppercase disabled"></span>
                                </div>
                                <div class="inputs-item only-non-resident" style="display: none">
                                    <label class="required" for="country-citizenship">{{ __('messages.select_country_of_citizenship') }}</label>
                                    <div class="dropdown">
                                        <button class="dropdown__button" type="button" data-country-citizenship>
                                            <span>{{ __('messages.country_of_citizenship') }}</span>
                                            <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                        </button>
                                        <ul class="dropdown__list search">
                                            <div class="dropdown-search-input-wrapper">
                                                <input autocomplete="off" type="text"
                                                    class="input input--full dropdown-search-input notUppercase"
                                                    placeholder="{{ __('messages.search_country') }}"
                                                    data-search-country-citizenship />
                                                <div class="dropdown-search-input-icon">
                                                    <img src="{{ asset('images/icons/icon-search.svg') }}" alt="" />
                                                </div>
                                            </div>
                                            <div class="dropdown__list-items" data-country-citizenship></div>
                                        </ul>
                                        <input autocomplete="off" class="dropdown__input_hidden" type="text"
                                            name="country-citizenship" value="" data-required="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="person-content" id="legal-entity">
                        <div class="citizen-list">
                            <div class="citizen-list-item">
                                <input
                                    type="radio"
                                    id="legal-entity-foreign"
                                    name="legal-entity"
                                    value="0"
                                    checked
                                />
                                <label for="legal-entity-foreign">{{ __('messages.foreign_legal_entities_citizen') }}</label>
                            </div>
                            <div class="citizen-list-item">
                                <input
                                    type="radio"
                                    id="legal-entity-aze"
                                    name="legal-entity"
                                    value="4"
                                />
                                <label for="legal-entity-aze"
                                >{{ __('messages.legal_entity_of_azerbaijan') }}</label
                                >
                            </div>
                        </div>

                        <div class="inputs second-tab-legal-first-items">
                            <div class="inputs-row">
                                <div class="inputs-item">
                                    <label for="tin-number" class="required">{{ __('messages.please_insert_tin_number') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.tin_number') }}"
                                        id="tin-number"
                                        class="input maxLength-20"
                                        data-required="true"
                                    />
                                </div>
                            </div>

                            <div class="inputs-actions">
                                <button class="button button-outline button-search button-search-person">
                                    {{ __('messages.check') }}
                                </button>
                            </div>

                            <div class="alert alert-error alert_second_tab_input_items" style="display: none">
                                <div class="alert-left">
                                    <div class="icon">
                                        <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                    </div>
                                </div>
                                <div class="alert-right">
                                    <h4 class="alert-title">{{ __('messages.DATA_NOT_FOUND_ERROR') }}</h4>
                                    <p class="alert-text">
                                        {{ __('messages.DATA_NOT_FOUND_ERROR') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="inputs second-tab-legal-second-items">
                            <div class="inputs-row citizen-details citizen-details-legal-container active">
                                <div class="inputs-item only-legal-non-resident" style="display: none;">
                                    <label for="entity-name" class="required">{{ __('messages.please_insert_official_name_of_entity') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.name_of_entity') }}"
                                        id="entity-name"
                                        class="input onlyLegalName"
                                        data-required="true"
                                    />
                                </div>
                                <div class="inputs-item only-legal-non-resident" style="display: none;">
                                    <label for="entity-address" class="required">{{ __('messages.please_insert_legal_address_of_entity') }}</label>
                                    <input autocomplete="off" type="text" placeholder="{{ __('messages.legal_address') }}"
                                        id="entity-address"
                                        class="input onlyForAddress"
                                        data-required="true"
                                    />
                                </div>
                                <div class="inputs-item">
                                    <label for="mobile-number" class="required">{{ __('messages.mobile') }}</label>
                                    <input autocomplete="off" type="tel" data-required="true" id="mobile-number"
                                        name="mobile-number" class="mobile_number input input-tel notUppercase" />
                                </div>
                                <div class="inputs-item">
                                    <label for="email">{{ __('messages.email') }}</label>
                                    <input autocomplete="off" type="email" placeholder="{{ __('messages.email') }}"
                                        id="email"
                                        class="input notUppercase"
                                    />
                                </div>
                                <div class="inputs-item">
                                    <label class="required" for="radio-group-insurance">{{ __('messages.insurance_period') }}</label>
                                    <div class="radio-group-insurance" data-insurance-period>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="1" />
                                            <span>{{ __('messages.1_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="3" />
                                            <span>{{ __('messages.3_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="6" />
                                            <span>{{ __('messages.6_month') }}</span>
                                        </label>
                                        <label class="radio-option">
                                            <input type="radio" name="insurance-period" value="12" />
                                            <span>{{ __('messages.12_month') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div style="display: none" class="insurance-fee inputs-item second-tab-same-items">
                                    <label for="insurance_amount">{{ __('messages.insurance_amount') }}</label>
                                    <span name="insurance_amount" class="insurance_amount input notUppercase disabled"></span>
                                </div>
                                <div class="inputs-item only-legal-non-resident" style="display: none">
                                    <label for="country-citizenship" class="required">{{ __('messages.select_country_of_citizenship') }}</label>
                                    <div class="dropdown">
                                        <button class="dropdown__button" type="button" data-country-citizenship>
                                            <span>{{ __('messages.country_of_citizenship') }}</span>
                                            <img src="{{ asset('images/icons/icon-chevron-down-gray.svg') }}" alt="" />
                                        </button>
                                        <ul class="dropdown__list search">
                                            <div class="dropdown-search-input-wrapper">
                                                <input autocomplete="off" type="text"
                                                    class="input input--full dropdown-search-input notUppercase"
                                                    placeholder="{{ __('messages.search_country') }}"
                                                    data-search-country-citizenship />
                                                <div class="dropdown-search-input-icon">
                                                    <img src="{{ asset('images/icons/icon-search.svg') }}" alt="" />
                                                </div>
                                            </div>
                                            <div class="dropdown__list-items" data-country-citizenship></div>
                                        </ul>
                                        <input autocomplete="off" class="dropdown__input_hidden" type="text"
                                            name="country-citizenship" value="" data-required="true" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="person-content active">
                        <div class="inputs second-tab-same-items">
                            <div class="inputs-row"></div>
                            <div class="alert alert-info alert_second_tab_info" style="margin: 16px 0;display: none">
                                <div class="alert-left">
                                    <div class="icon">
                                        <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                    </div>
                                </div>
                                <div class="alert-right">
                                    <p class="alert-title"> {!! __('messages.register_button_confirmation') !!}</p>
                                </div>
                            </div>
                            <div class="inputs-item checkbox-wrapper" style="display: none">
                                <label class="custom-checkbox required">
                                    <input type="checkbox" id="agree" />
                                    <span class="checkmark"></span>
                                    {{ __('messages.agree_to_terms') }}
                                </label>
                            </div>
                            <div class="alert alert-error alert_second_tab_validate_items" style="display: none">
                                <div class="alert-left">
                                    <div class="icon">
                                        <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                                    </div>
                                </div>
                                <div class="alert-right">
                                    <h4 class="alert-title">{{ __('messages.please_fill_all_required_fields') }}</h4>
                                </div>
                            </div>

                            <div class="inputs-actions">
                                <button class="button button-outline button-back">
                                    {{ __('messages.previous') }}
                                </button>

                                <button class="button button-primary button-register">
                                    {{ __('messages.register') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <style>
            .iti-full-width {
                width: 100% !important;
            }
            .required::after {
                content: " *";
                color: red;
            }
        </style>
        @include('partials/footer')
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Terminal mode detection
                const urlParams = new URLSearchParams(window.location.search);
                window.isTerminalMode = urlParams.get('source') === 'terminal';


                const appLocale = "{{ app()->getLocale() }}";
                const savedLocale = localStorage.getItem('currentLocale');
                if (savedLocale !== appLocale) {
                    localStorage.removeItem('countryList');
                    localStorage.setItem('currentLocale', appLocale);
                }
                const insuranceCompanies = document.querySelectorAll(
                    ".select-companies .companies-item"
                );
                const alertBox = document.querySelector(".alert_first_tab_validate_items");

                const checkButton = document.querySelector(".button-search-vehicle");
                const plateInput = document.getElementById("plate-number");
                const docInput = document.getElementById("doc-number");
                const alertMessage = document.querySelector(".alert_first_tab_first_items");

                const makeDropdown = document.querySelector("[data-make]");
                const makeList = document.querySelector(".dropdown__list-items[data-make]");
                const makeInput = document.querySelector("[name='vehicle-make']");
                const makeSearch = document.querySelector("[data-search-make]");

                const modelDropdown = document.querySelector("[data-model]");
                const modelList = document.querySelector(".dropdown__list-items[data-model]");
                const modelInput = document.querySelector("[name='vehicle-model']");
                const modelSearch = document.querySelector("[data-search-model]");

                const countryDropdown = document.querySelector("[data-country]");
                const countryList = document.querySelector(".dropdown__list-items[data-country]");
                const countryInput = document.querySelector("[name='vehicle-country']");
                const countrySearch = document.querySelector("[data-search-country]");

                const typeDropdown = document.querySelector("[data-vehicle-type]");
                const typeList = document.querySelector(".dropdown__list-items[data-vehicle-type]");
                const typeInput = document.querySelector("[name='vehicle-type']");

                const vehicleTypes = [
                    { name: "{{ __('messages.vehicleType_1') }}", oid: '1' },
                    { name: "{{ __('messages.vehicleType_2') }}", oid: '2' },
                    { name: "{{ __('messages.vehicleType_3') }}", oid: '3' },
                    { name: "{{ __('messages.vehicleType_4') }}", oid: '4' },
                    { name: "{{ __('messages.vehicleType_5') }}", oid: '5' },
                    { name: "{{ __('messages.vehicleType_6') }}", oid: '6' }
                ];
                populateDropdown(typeList, vehicleTypes, "name", "oid");
                if (!makeDropdown || !makeList || !makeInput || !makeSearch || !modelDropdown || !modelList || !modelInput || !modelSearch
                    || !countryDropdown || !countryList || !countryInput || !countrySearch) {
                    console.error("Refresh page please, dropdown elements not found in DOM. Check your HTML structure.");
                    return;
                }
                // second tab
                const tabItems = document.querySelectorAll(".tabs-item");
                const buttonRegister = document.querySelector(".button-register");
                // bunu servis olanda qosmaq lazimdir ki melmatlarin olub olmamasini bilek
                const alert_second_tab_validate_items = document.querySelector(".alert_second_tab_validate_items");
                const alert_second_tab_info = document.querySelector(".alert_second_tab_info");
                const checkbox_wrapper = document.querySelector(".checkbox-wrapper");
                const buttonsBack = document.querySelectorAll(".button-back");
                const buttonNext = document.querySelector(".button-next");
                const stepperItems = document.querySelectorAll(".stepper-item");
                const tabContents = document.querySelectorAll(".tab-content");
                const firstTabFirstItems = document.querySelector(".first-tab-first-items");
                const firstTabSecondItems = document.querySelector(".first-tab-second-items");
                firstTabFirstItems.style.visibility = "hidden";
                firstTabFirstItems.style.height = "0";
                firstTabSecondItems.style.visibility = "hidden";
                firstTabSecondItems.style.height = "0";
                // 2.nd tab items end
                window.initBirthdayPicker('birthday');
                let isLegal=0;
                let isPersonValid=false
                checkButton.addEventListener("click", async function (event) {
                    event.preventDefault();
                    const carNumber = plateInput.value.trim();
                    const docNumber = docInput.value.trim();
                    const vehicleType = typeInput.value;
                    alertMessage.style.display = "none";
                    if (!carNumber || !docNumber || !vehicleType) {
                        showAlert(alertMessage, "{{ __('messages.please_insert_plate_and_doc_number_and_vehicle_type') }}", [
                            "{{ __('messages.all_input_values_needed') }}"
                        ], 'error');
                        return false;
                    }
                    if(carNumber.length>10){
                        showAlert(alertMessage, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.plate_number_can_not_be_greater_than_10_characters') }}"
                        ], 'error');
                        return false;
                    }
                    if(docNumber.length>20){
                        showAlert(alertMessage, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.document_number_can_not_be_greater_than_20_characters') }}"
                        ], 'error');
                        return false;
                    }
                    showLoader();
                    try {
                        const response = await callService('/callService/his/borderVehicleInfo', {
                            input: {
                                plate: carNumber,
                                certNo: docNumber,
                                vehicleTypeId: vehicleType
                            },
                        });
                        const carInfo = response.data.carInfo;
                        const data = response.data;
                        if(data?.response && data.response.code !=='0') {
                            const backendMessage = data.response.message || '{{ __('messages.service_failed') }}';
                            showAlert(alertMessage, '{{ __('messages.service_failed') }}', [backendMessage], 'error');
                            return false;
                        }
                        resetDropdownsAndInputsFirstTab('vehicleType')
                        if(isPersonValid){
                            resetInputsAndDropdownsOnTabChange();
                            isLegal=0;
                        }
                        firstTabSecondItems.style.visibility = "visible";
                        firstTabSecondItems.style.height = "auto";
                        if(!carInfo){
                            showAlert(alertMessage, "{{ __('messages.no_car_info_found') }}", [
                                "{{ __('messages.check_your_input_or_add_new_vehicle') }}"
                            ], 'info');
                        }
                        await fetchWithCache("countryList", "/migration/countries", countryList, "name", "oid");
                        await fetchWithCache("carBrands", "/his/getVehicleDetails", makeList, "brandName", "oid");
                        await preselectVehicleData(carInfo);
                    } catch (error) {
                        if (error.response && error.response.data?.response) {
                            const backendMessage = error.response.data.response.message;
                            showAlert(alertMessage, "{{ __('messages.SERVICE_ERROR') }}", [backendMessage], 'error');
                        }
                    }finally {
                        hideLoader();
                    }

                });
                async function fetchWithCache(key, url, dropdownElement, nameKey, valueKey) {
                    const cachedData = localStorage.getItem(key);
                    if (cachedData) {
                        const parsedData = JSON.parse(cachedData);
                        populateDropdown(dropdownElement, parsedData, nameKey, valueKey);
                        return;
                    }
                    showLoader()
                    try {
                        const response = await callService("/callService" + url, {});
                        const data = response.data[key] || [];

                        if (data.length > 0) {
                            // data.sort((a, b) => {
                            //     return a[nameKey].localeCompare(b[nameKey], 'az', { sensitivity: 'base' });
                            // });
                            localStorage.setItem(key, JSON.stringify(data));
                            populateDropdown(dropdownElement, data, nameKey, valueKey);
                        } else {
                            showAlert(alertMessage, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                                "{{ __('messages.no_data_available') }}"
                            ], 'error');
                        }
                    } catch (error) {
                        if(error.response && error.response.data?.response) {
                            const backendMessage = error.response.data.response.message;
                            showAlert(alertMessage, "{{ __('messages.SERVICE_ERROR') }}", [backendMessage], 'error');
                        }
                    }finally {
                        hideLoader();
                    }
                }
                async function fetchVehicleModels(brandOid) {
                    try {
                        showLoader();
                        const response = await callService("/callService/his/getVehicleDetails", {
                            input: { brandOid },
                        });
                        const carModels = response.data.carModels || [];
                        if (modelDropdown) {
                            modelDropdown.querySelector("span").textContent = "{{ __('messages.vehicle_model') }}";
                            modelDropdown.closest(".dropdown").querySelector("input").value = "";
                            modelInput.value = "";
                            modelInput.dataset.text = "";
                        }
                        if (carModels.length > 0) {
                            populateDropdown(modelList, carModels, "modelName", "oid");
                        } else {
                            modelList.innerHTML = `<li class="dropdown__list-item disabled">{{ __('messages.no_models_available') }}</li>`;
                        }
                    } catch (error) {
                        if(error.response && error.response.data?.response) {
                            const backendMessage = error.response.data.response.message;
                            showAlert(alertMessage, "{{ __('messages.SERVICE_ERROR') }}", [backendMessage], 'error');
                        }
                    }
                    finally {
                        hideLoader();
                    }
                }
                async function preselectVehicleData(carInfo) {
                    const { vehicleMarka, vehicleModel, vehicleType, vehicleTypeId, countryCode } = carInfo;
                    let makeOid=''
                    if (vehicleMarka) {
                        const makeItem = [...makeList.children].find(item => item.textContent === vehicleMarka);
                        if (makeItem) {
                            selectDropdownItem(makeItem, makeDropdown, makeInput);
                            makeOid = makeItem.getAttribute("data-value")
                            await fetchVehicleModels(makeOid);
                        }
                    }
                    if (vehicleModel && makeOid) {
                        const modelItem = [...modelList.children].find(item => item.textContent === vehicleModel);
                        if (modelItem) {
                            selectDropdownItem(modelItem, modelDropdown, modelInput);
                        }
                    }
                    if (countryCode) {
                        const countryItem = [...countryList.children].find(item => item.getAttribute("data-value")*1 === countryCode*1);
                        if (countryItem) {
                            selectDropdownItem(countryItem, countryDropdown, countryInput);
                        }
                    }
                }

                function populateDropdown(listElement, dataArray, textKey, valueKey) {
                    listElement.innerHTML = ""; // Clear the existing list items
                    dataArray.forEach((item) => {
                        const listItem = document.createElement("li");
                        listItem.classList.add("dropdown__list-item");
                        listItem.setAttribute("data-value", item[valueKey]);
                        listItem.setAttribute("data-text", item[textKey]);
                        listItem.textContent = item[textKey];
                        listElement.appendChild(listItem);
                    });
                    const dropdownItems = document.querySelectorAll(".dropdown__list-item");
                    dropdownItems.forEach((item) => {
                        item.addEventListener("click", (e) => {
                            const dropdown = e.target.closest(".dropdown");
                            const hiddenInput = dropdown.querySelector(".dropdown__input_hidden");
                            const buttonSpan = dropdown.querySelector(".dropdown__button span");
                            hiddenInput.value = item.dataset.value;
                            buttonSpan.textContent = item.textContent;
                            hiddenInput.dataset.text = item.dataset.text;
                            dropdown.querySelector(".dropdown__list").classList.remove("active");
                        });
                    });
                }

                function selectFirstOption(dropdown, input, text, value) {
                    dropdown.querySelector("span").textContent = text;
                    input.value = value;
                }
                function selectDropdownItem(item, dropdown, input) {
                    dropdown.querySelector("span").textContent = item.textContent;
                    input.value = item.getAttribute("data-value");
                    input.dataset.text = item.getAttribute("data-text");
                }
                function handleDropdownSelection(event, dropdown, input, callback) {
                    if (event.target.classList.contains("dropdown__list-item")) {
                        const selectedText = event.target.textContent;
                        const selectedValue = event.target.getAttribute("data-value");
                        const selectedOriginalText = event.target.getAttribute("data-text");
                        alertBox.style.display = "none";
                        alert_second_tab_validate_items.style.display = "none";
                        dropdown.querySelector("span").textContent = selectedText;
                        input.value = selectedValue;
                        input.dataset.text=selectedOriginalText
                        if (callback) callback(selectedValue);
                    }
                }

                function handleDropdownSearch(event, listElement) {
                    const searchTerm = event.target.value.toLowerCase();
                    const items = listElement.querySelectorAll(".dropdown__list-item");

                    items.forEach((item) => {
                        const text = item.textContent.toLowerCase();
                        item.style.display = text.includes(searchTerm) ? "block" : "none";
                    });
                }

                makeList.addEventListener("click", (event) => handleDropdownSelection(event, makeDropdown, makeInput, fetchVehicleModels));
                modelList.addEventListener("click", (event) => handleDropdownSelection(event, modelDropdown, modelInput, null));
                countryList.addEventListener("click", (event) => handleDropdownSelection(event, countryDropdown, countryInput, null));

                makeSearch.addEventListener("input", (event) => handleDropdownSearch(event, makeList));
                modelSearch.addEventListener("input", (event) => handleDropdownSearch(event, modelList));
                countrySearch.addEventListener("input", (event) => handleDropdownSearch(event, countryList));

                fetchWithCache("countryList", "/migration/countries", countryList, "name", "oid");
                fetchWithCache("carBrands", "/his/getVehicleDetails", makeList, "brandName", "oid");
                function resetDropdownsAndInputsFirstTab(input) {
                    alertBox.style.display = "none";
                    alertMessage.style.display = "none";
                    makeInput.value = "";
                    makeInput.dataset.text = "";
                    makeDropdown.querySelector("span").textContent = "{{ __('messages.vehicle_make') }}";
                    modelInput.value = "";
                    modelInput.dataset.text = "";
                    modelDropdown.querySelector("span").textContent = "{{ __('messages.vehicle_model') }}";
                    modelList.innerHTML = "";
                    countryInput.value = "";
                    countryDropdown.querySelector("span").textContent = "{{ __('messages.vehicle_country') }}";
                    if(input !== 'vehicleType') {
                        typeInput.value = "";
                        typeInput.dataset.text = "";
                        typeList.innerHTML = "";
                        typeDropdown.querySelector("span").textContent = "{{ __('messages.vehicle_type') }}";
                        populateDropdown(typeList, vehicleTypes, "name", "oid");
                    }
                    firstTabSecondItems.style.visibility = "hidden";
                    firstTabSecondItems.style.height = "0";
                }
                plateInput.addEventListener("input", resetDropdownsAndInputsFirstTab);
                docInput.addEventListener("input", resetDropdownsAndInputsFirstTab);
                typeList.addEventListener("click", () => resetDropdownsAndInputsFirstTab('vehicleType'));
                let voenInsurer = '';
                insuranceCompanies.forEach((item) => {
                    item.addEventListener("click", () => {
                        insuranceCompanies.forEach((item) => item.classList.remove("active"));

                        item.classList.add("active");
                        plateInput.value = "";
                        docInput.value = "";
                        resetDropdownsAndInputsFirstTab();
                        if(isPersonValid){
                            resetInputsAndDropdownsOnTabChange();
                            isLegal=0;
                        }

                        const companyName = item.getAttribute("data-company-name");
                        const companyTin = item.getAttribute("data-company-tin");
                        if (companyTin) {
                            voenInsurer = companyTin;
                            firstTabFirstItems.style.visibility = "visible";
                            firstTabFirstItems.style.height = "auto";
                            plateInput.focus();
                        }
                    });
                });

                let currentTabIndex = 0;
                stepperItems[currentTabIndex]?.classList.add("active");
                tabContents[currentTabIndex]?.classList.add("active");
                buttonNext.addEventListener("click", async () => {
                    const firstTab = document.querySelector(".first-tab-first-items");
                    const secondTab = document.querySelector(".first-tab-second-items");

                    alertBox.style.display = "none";
                    const requiredFields = [
                        ...firstTab.querySelectorAll("input[data-required='true']"),
                        ...secondTab.querySelectorAll("input[data-required='true']")
                    ];
                    const missingFieldsFirstTab = [];

                    requiredFields.forEach((field) => {
                        if (!field.value.trim()) {
                            const label = firstTab.querySelector(`label[for="${field.name}"]`) || field.closest(".inputs-item")?.querySelector("label");
                            if (label) {
                                missingFieldsFirstTab.push(label.textContent.trim());
                            }
                        }
                    });

                    if (missingFieldsFirstTab.length > 0) {
                        showAlert(alertBox, "{{ __('messages.please_fill_all_required_fields') }}", missingFieldsFirstTab, "error");
                        return false;
                    }
                    const carNumber = plateInput.value.trim();
                    const docNumber = docInput.value.trim();
                    const vehicleType = typeInput.value;
                    // const isConfirmed = await window.confirmActionWithModal();
                    // if (!isConfirmed) return;
                    showLoader();

                    try {
                        const response = await callService('/callService/his/validateBorderVehiclePolicyStatus', {
                            input: {
                                certNumber: docNumber,
                                plateNumber: carNumber,
                                vehicleTypeId: vehicleType,
                                voenInsurer
                            }
                        });

                        const serviceResult = response.data;
                        if (serviceResult?.response && serviceResult.response.code ==='0') {
                            alertBox.style.display = "none";
                            alertMessage.style.display = "none";
                            tabContents.forEach((item) => item.classList.remove("active"));
                            currentTabIndex++;
                            window.scrollTo({ top: 0, behavior: "smooth" });
                            const personContents = document.querySelectorAll(".person-content");
                            const activeContents = document.querySelectorAll(".person-content.active");

                            const lastActive = activeContents[activeContents.length - 1];
                            if(!isPersonValid) {
                                personContents.forEach((el) => {
                                    if (el !== lastActive) {
                                        el.classList.remove("active");
                                    }
                                });
                                tabItems.forEach((item) => item.classList.remove("active"));
                                tabItems.forEach((item) => {
                                    if (item.getAttribute("data-tab") === "individual") {
                                        item.classList.add("active");
                                    }
                                });
                                document.getElementById('individual')?.classList.add("active");
                                handleCitizenInputChangeCitizen(0);
                            }
                            stepperItems[currentTabIndex]?.classList.add("active");
                            tabContents[currentTabIndex]?.classList.add("active");
                            setTimeout(() => {
                                const activePersonContent = document.querySelector(".person-content.active");
                                if (activePersonContent) {
                                    const firstInput = Array.from(activePersonContent.querySelectorAll("input"))
                                        .find(input => input.offsetParent !== null && input.type !== 'hidden' && input.type !== 'radio' && input.type !== 'checkbox');
                                    if (firstInput) {
                                        firstInput.focus();
                                    }
                                }
                            }, 100);
                        } else {
                            const backendMessage = serviceResult.response?.message || "Servisdən xəta alındı.";
                            showAlert(alertBox, "{{ __('messages.service_error') }}", [backendMessage], "error");
                            return false;
                        }

                    } catch (error) {
                        const errorMsg = error.response?.data?.response?.message || "Naməlum xəta baş verdi.";
                        showAlert(alertBox, "{{ __('messages.service_error') }}", [errorMsg], "error");
                        return false;
                    } finally {
                        hideLoader();
                    }
                });
                buttonsBack.forEach((item) => {
                    item.addEventListener("click", () => {
                        tabContents.forEach((item) => item.classList.remove("active"));
                        stepperItems.forEach((item) => item.classList.remove("active"));
                        if(!isPersonValid){
                            resetInputsAndDropdownsOnTabChange();
                            isLegal=0;
                        }
                        currentTabIndex--;

                        stepperItems[currentTabIndex]?.classList.add("active");
                        tabContents[currentTabIndex]?.classList.add("active");
                    });
                });
                tabItems.forEach((item, index) => {
                    item.addEventListener("click", () => {
                        tabItems.forEach((item) => item.classList.remove("active"));
                        item.classList.add("active");

                        const tabName = item.getAttribute("data-tab");

                        const personContents = document.querySelectorAll(".person-content");
                        const activeContents = document.querySelectorAll(".person-content.active");

                        const lastActive = activeContents[activeContents.length - 1];

                        personContents.forEach((el) => {
                            if (el !== lastActive) {
                                el.classList.remove("active");
                            }
                        });
                        document.getElementById(tabName)?.classList.add("active");
                        let value = 1;
                        if(tabName === "individual"){
                            value = 0;
                            isLegal = 0;
                            handleCitizenInputChangeCitizen(value);
                        }else{
                            value = 0;
                            isLegal = 1;
                            handleCitizenInputChangeLegal(value);
                        }

                    });
                });
                let entityType = 1;
                function handleCitizenInputChangeCitizen(e){
                    const value = e*1 === 0 ? e : e.value*1;
                    entityType = value;
                    document.querySelectorAll('input[name="citizen"]').forEach((radio) => {
                        radio.checked = Number(radio.value) === value;
                    });
                    document.querySelectorAll(".insurance-fee").forEach((el) => {
                        el.style.display = "none";
                    });
                    document.querySelectorAll(".citizen-details-container .inputs-item").forEach((el) => {
                        if (!el.classList.contains("insurance-fee")) {
                            el.style.display = "flex";
                        }
                    });
                    document.querySelectorAll(".citizen-inputs-container .inputs-item").forEach((el) => {
                        if (!el.classList.contains("insurance-fee")) {
                            el.style.display = "flex";
                        }
                    });
                    if (value !== 0) {
                        document.querySelectorAll(".citizen-details-container .only-non-resident").forEach((el) => {
                            el.style.display = "none";
                        });
                    }
                    if (value === 1) {
                        document.querySelector(".citizen-inputs-container .pin-with-birthday").style.display = "none";
                    }
                    if (value !== 1) {
                        document.querySelector(".citizen-inputs-container .pin-with-serial-number").style.display = "none";
                    }
                    const passportInput = document.querySelector('#passport-number');
                    const passportLabel = document.querySelector('label[for="passport-number"]');
                    if (passportInput && passportLabel) {
                        if (value === 1 || value === 2) {
                            passportLabel.innerText = '{{ __('messages.id_number') }}';
                            passportInput.placeholder = '{{ __('messages.id_number') }}';
                        } else {
                            passportLabel.innerText = '{{ __('messages.passport_number') }}';
                            passportInput.placeholder = '{{ __('messages.passport_number') }}';
                        }
                    }
                    resetInputsAndDropdownsOnTabChange();
                }
                function handleCitizenInputChangeLegal(e){
                    const value = e*1 === 0 ? e : e.value*1;
                    entityType = value;
                    document.querySelectorAll('input[name="legal-entity"]').forEach((radio) => {
                        radio.checked = Number(radio.value) === value;
                    });
                    document.querySelectorAll(".insurance-fee").forEach((el) => {
                        el.style.display = "none";
                    });
                    document.querySelectorAll(".citizen-details-legal-container .inputs-item").forEach((el) => {
                        if (!el.classList.contains("insurance-fee")) {
                            el.style.display = "flex";
                        }
                    });
                    if (value === 4) {
                        document.querySelectorAll(".citizen-details-legal-container .only-legal-non-resident").forEach((el) => {
                            el.style.display = "none";
                        });
                    }
                    resetInputsAndDropdownsOnTabChange();
                }
                let insuranceFee = ''
                let insurancePeriod = ''
                let mobNum = ''
                async function populateActiveTabAndRadio(azeOrForeign = "aze") {
                    const activeSecondTab = document.querySelector(".person-content.active");
                    if (!activeSecondTab) return;

                    const activeRadioGroup = activeSecondTab.querySelector(".citizen-details.active");
                    if (!activeRadioGroup) return;
                    activeRadioGroup.style.display='grid';
                    buttonRegister.style.display = 'block';
                    const mobileNumber = activeRadioGroup.querySelector(".mobile_number");

                    const countryCitizenshipDropdown = activeRadioGroup.querySelector("[data-country-citizenship]");
                    const countryCitizenshipList = activeRadioGroup.querySelector(".dropdown__list-items[data-country-citizenship]");
                    const countryCitizenshipInput = activeRadioGroup.querySelector("[name='country-citizenship']");
                    const countryCitizenshipSearch = activeRadioGroup.querySelector("[data-search-country-citizenship]");
                    if (!mobileNumber){
                        console.error("Refresh page please, 2.nd tab dropdown elements not found in DOM. Check your HTML structure.");
                        return;
                    }
                    if(azeOrForeign !=='aze'){
                        countryCitizenshipList.addEventListener("click", (event) => handleDropdownSelection(event, countryCitizenshipDropdown, countryCitizenshipInput, null));
                        countryCitizenshipSearch.addEventListener("input", (event) => handleDropdownSearch(event, countryCitizenshipList));
                        await fetchWithCache("countryList", "/migration/countries", countryCitizenshipList, "name", "oid");
                    }

                    if (!mobileNumber.dataset.phoneInitialized) {
                        window.phone = await initPhoneInput(mobileNumber, azeOrForeign);
                        document.querySelectorAll('.iti').forEach(el => {
                            el.classList.add('iti-full-width');
                        })
                        mobileNumber.dataset.phoneInitialized = "true"; // mark as initialized
                    }
                }
                document.body.addEventListener('input', function(event) {
                    const mobileNumber = event.target.closest('.mobile_number');
                    if (mobileNumber && mobileNumber.dataset.phoneInitialized) {
                        mobileNumber.value = mobileNumber.value.replace(/[^0-9+\-\s()]/g, '');
                        alert_second_tab_validate_items.style.display = "none";
                        if (window.phone) {
                            const number = window.phone.getFullNumber();
                            const countryData = window.phone.instance.getSelectedCountryData();

                            // if (countryData.iso2 === "az" && number.length > 13) {
                            //     mobileNumber.value = mobileNumber.value.slice(0, 13);
                            // }
                        }
                    }
                });
                document.body.addEventListener('change', function(event) {
                    const mobileNumber = event.target.closest('.mobile_number');
                    if (mobileNumber && mobileNumber.dataset.phoneInitialized) {
                        alert_second_tab_validate_items.style.display = "none";
                        if (window.phone) {
                            const number = window.phone.getFullNumber();
                            if (window.phone.isValid()) {
                                mobNum = number;
                                console.log("✅ Valid Number: " + number);
                            } else {
                                showAlert(alert_second_tab_validate_items, "{{ __('messages.wrong_information') }}", [
                                    window.phone.getValidationError()
                                ], 'error');
                            }
                        }
                    }
                });
                document.querySelectorAll('input[name="citizen"]').forEach((radio) => {
                    radio.addEventListener("change", function () {
                        handleCitizenInputChangeCitizen(this);
                    });
                });
                document.querySelectorAll('input[name="legal-entity"]').forEach((radio) => {
                    radio.addEventListener("change", function () {
                        handleCitizenInputChangeLegal(this);
                    });
                });
                async function initPhoneInput(input, configType = 'all') {
                    if (!input) return null;

                    let options = {
                        initialCountry: "az",
                        nationalMode: false,
                        customPlaceholder:  () => '',
                        loadUtils: () => import("{{ asset('vendor/intl-tel-input-25.3.1/build/js/utils.js') }}"),
                    };

                    if (configType === "aze") {
                        options = {
                            ...options
                        };
                    } else {
                        options = {
                            ...options,
                            geoIpLookup: (success, failure) => {
                                fetch("https://ipapi.co/json")
                                    .then((res) => res.json())
                                    .then((data) => success(data.country_code))
                                    .catch(() => failure());
                            }
                        };
                    }
                    const iti = intlTelInput(input, options);
                    input.value = '+' + iti.getSelectedCountryData().dialCode;
                    input.addEventListener('countrychange', () => {
                        input.value = '+' + iti.getSelectedCountryData().dialCode;
                    });
                    return {
                        getFullNumber: () => iti.getNumber(),
                        isValid: () => iti.isValidNumber(),
                        getValidationError: () => {
                            const errorMessages = {
                                0: "{{ __('messages.no_error') }}",
                                1: "{{ __('messages.invalid_country_code') }}",
                                2: "{{ __('messages.number_is_too_short') }}",
                                3: "{{ __('messages.number_is_too_long') }}",
                                4: "{{ __('messages.invalid_number') }}",
                            };
                            return "{{ __('messages.invalid_mobile_number') }}";
                        },
                        instance: iti,
                    };
                }
                function resetInputsAndDropdownsOnTabChange(hasExcludedInputs = false) {
                    const activeSecondTab = document.querySelector(".person-content.active");
                    if (!activeSecondTab) return;

                    const activeRadioGroup = activeSecondTab.querySelector(".citizen-details.active");
                    if (!activeRadioGroup) return;
                    const alert_second_tab_input_items = activeSecondTab.querySelector(".alert_second_tab_input_items");
                    const insuranceAmountSpan = activeRadioGroup.querySelector(".insurance_amount");
                    const insuranceSpan = activeRadioGroup.querySelector(".insurance-fee");
                    const mobileNumber = activeRadioGroup.querySelector(".mobile_number");

                    const countryCitizenshipDropdown = activeRadioGroup.querySelector("[data-country-citizenship]");
                    const countryCitizenshipInput = activeRadioGroup.querySelector("[name='country-citizenship']");

                    const serialNumberInput = activeSecondTab.querySelector("#serial-number");
                    const birthdayInput = activeSecondTab.querySelector("#birthday");
                    const passportNumberInput = activeSecondTab.querySelector("#passport-number");
                    const tinNumberInput = activeSecondTab.querySelector("#tin-number");
                    const name = activeRadioGroup.querySelector("#name");
                    const surname = activeRadioGroup.querySelector("#surname");
                    const fatherName = activeRadioGroup.querySelector("#father-name");
                    const livingAddress = activeRadioGroup.querySelector("#living-address");
                    const entityAddress = activeRadioGroup.querySelector("#entity-address");
                    const entityName = activeRadioGroup.querySelector('#entity-name')
                    const email = activeRadioGroup.querySelector("#email");
                    let arr = [mobileNumber, countryCitizenshipInput, serialNumberInput, birthdayInput,
                        passportNumberInput, tinNumberInput, name, surname, fatherName, livingAddress, email, entityName, entityAddress];
                    if(hasExcludedInputs){
                        let excludedInputs = [passportNumberInput, tinNumberInput, serialNumberInput, birthdayInput];
                        arr = arr.filter(item => !excludedInputs.includes(item));
                    }
                    arr.forEach(el => {
                        if (el) el.value = "";
                    });
                    isPersonValid = false
                    const insuranceRadios = activeRadioGroup.querySelectorAll("input[name='insurance-period']");
                    insuranceRadios.forEach(radio => radio.checked = false);
                    insurancePeriod = ''
                    countryCitizenshipDropdown.querySelector("span").textContent = "{{ __('messages.select_country_of_citizenship') }}";
                    if (insuranceAmountSpan) insuranceAmountSpan.textContent = '';
                    if (insuranceSpan) insuranceSpan.style.display = 'none';
                    alert_second_tab_validate_items.style.display = "none";
                    alert_second_tab_input_items.style.display = 'none'
                    alert_second_tab_info.style.display = 'none';
                    checkbox_wrapper.style.display = 'none';
                    checkbox_wrapper.querySelector("input").checked = false;
                    activeRadioGroup.style.display='none';
                    buttonRegister.style.display = 'none';
                    const allMobileInputs = activeSecondTab.querySelectorAll(".mobile_number");
                    allMobileInputs.forEach((input) => {
                        input.removeAttribute("data-phone-initialized");
                    });
                }
                document.body.addEventListener('input', function (event) {
                    if (
                        event.target &&
                        (event.target.id === 'passport-number' ||
                            event.target.id === 'serial-number' ||
                            event.target.id === 'birthday' ||
                            event.target.id === 'tin-number')
                    ) {
                        const input = event.target;
                        isPersonValid = false;
                        resetInputsAndDropdownsOnTabChange(true);
                        if(event.target.id !== 'birthday'){
                            const classList = Array.from(input.classList);
                            const maxLengthClass = classList.find(cls => cls.startsWith('maxLength-'));

                            if (maxLengthClass) {
                                const max = parseInt(maxLengthClass.split('-')[1], 10);

                                if (input.value.length > max) {
                                    input.value = input.value.slice(0, max);
                                }
                            }
                        }
                        if (event.target.id === 'passport-number') {
                            if(entityType===0){
                                input.classList.remove('maxLength-7');
                                input.classList.remove('maxLength-6');
                                if (!input.classList.contains('maxLength-20')) {
                                    input.classList.add('maxLength-20');
                                }
                            }
                            if(entityType===1){
                                input.classList.remove('maxLength-6');
                                input.classList.remove('maxLength-20');
                                if (!input.classList.contains('maxLength-7')) {
                                    input.classList.add('maxLength-7');
                                }
                            }
                            if(entityType===2){
                                input.classList.remove('maxLength-20');
                                input.classList.remove('maxLength-7');
                                if (!input.classList.contains('maxLength-6')) {
                                    input.classList.add('maxLength-6');
                                }
                            }

                        }
                        if (event.target.id === 'serial-number') {
                            if(entityType===2){
                                input.classList.remove('maxLength-10');
                                if (!input.classList.contains('maxLength-15')) {
                                    input.classList.add('maxLength-15');
                                }
                            }else{
                                input.classList.remove('maxLength-15');
                                if (!input.classList.contains('maxLength-10')) {
                                    input.classList.add('maxLength-10');
                                }
                            }

                        }
                        if (event.target.id === 'tin-number') {
                            if (entityType === 4) {
                                input.value = input.value.replace(/[^0-9]/g, '');
                                input.classList.remove('maxLength-20');
                                if (!input.classList.contains('maxLength-10')) {
                                    input.classList.add('maxLength-10');
                                }
                            } else if (entityType === 0) {
                                let cleaned = input.value.replace(/[^a-zA-Z0-9\-]/g, '');
                                const dashIndex = cleaned.indexOf('-');
                                if (dashIndex !== -1) {
                                    cleaned = cleaned.slice(0, dashIndex + 1) + cleaned.slice(dashIndex + 1).replace(/-/g, '');
                                }
                                input.value = cleaned;
                                input.classList.remove('maxLength-10');
                                if (!input.classList.contains('maxLength-20')) {
                                    input.classList.add('maxLength-20');
                                }
                            }
                        }
                    }
                });
                document.body.addEventListener('input', function(event) {
                    if (event.target && event.target.id === 'birthday') {
                        let field = event.target;
                        let value = field.value.replace(/\D/g, '');
                        if (value.length > 8) value = value.slice(0, 8);

                        if (value.length >= 5) {
                            field.value = `${value.slice(0, 2)}-${value.slice(2, 4)}-${value.slice(4)}`;
                        } else if (value.length >= 3) {
                            field.value = `${value.slice(0, 2)}-${value.slice(2)}`;
                        } else {
                            field.value = value;
                        }
                    }
                });
                document.body.addEventListener("change", async function (e) {
                    const clickedEl = e.target;
                    if (clickedEl.matches("input[name='insurance-period']")) {
                        e.preventDefault();
                        const activeSecondTab = document.querySelector(".person-content.active");
                        if (!activeSecondTab) return;

                        const insurancePeriodContainer = activeSecondTab.querySelector("[data-insurance-period]");
                        if (!insurancePeriodContainer) return;

                        const selectedRadio = insurancePeriodContainer.querySelector("input[name='insurance-period']:checked");
                        if (!selectedRadio) {
                            showAlert(alert_second_tab_validate_items, "{{ __('messages.please_select_needed_data') }}", [
                                "{{ __('messages.period_type_should_be_selected') }}"
                            ], 'error');
                            return;
                        }
                        const selectedId = selectedRadio.value;
                        insurancePeriod = selectedId;
                        const insuranceAmountSpan = activeSecondTab.querySelector(".insurance_amount");
                        const insuranceSpan = activeSecondTab.querySelector(".insurance-fee");
                        const vehicleType = typeInput.value;

                        if (!selectedId || !vehicleType) {
                            showAlert(alert_second_tab_validate_items, "{{ __('messages.please_select_needed_data') }}", [
                                "{{ __('messages.period_type_and_vehicle_type_should_be_selected') }}"
                            ], 'error');
                            return;
                        }

                        alert_second_tab_validate_items.style.display = "none";
                        insuranceSpan.style.display = 'none';

                        showLoader();
                        try {
                            const response = await callService("/callService/his/calculateBorderInsuranceFee", {
                                input: {
                                    insurancePeriod: selectedId,
                                    vehicleType: vehicleType,
                                }
                            });

                            const res = response.data;
                            if (res?.response && res.response.code !== '0') {
                                const backendMessage = res.response.message || '{{ __('messages.service_failed') }}';
                                showAlert(alert_second_tab_validate_items, '{{ __('messages.service_failed') }}', [backendMessage], 'error');
                                return false;
                            }

                            const bmData = response.data.bmInfo;
                            insuranceAmountSpan.textContent = bmData.insrFee;
                            insuranceFee = bmData.insrFee;
                            insuranceSpan.style.display = 'flex';

                        } catch (error) {
                            if (error.response && error.response.data?.response) {
                                const backendMessage = error.response.data.response.message;
                                showAlert(alert_second_tab_validate_items, "{{ __('messages.SERVICE_ERROR') }}", [backendMessage], 'error');
                            }
                        } finally {
                            hideLoader();
                        }
                    }
                });

                document.body.addEventListener("click", async function(e) {
                    if (e.target && e.target.classList.contains("button-search-person")) {
                        e.preventDefault();

                        const activeSecondTab = document.querySelector(".person-content.active");
                        if (!activeSecondTab) return;
                        const activeRadioGroup = activeSecondTab.querySelector(".citizen-details.active");
                        if (!activeRadioGroup) return;
                        const alert_second_tab_input_items = activeSecondTab.querySelector(".alert_second_tab_input_items");
                        const serialNumberInput = activeSecondTab.querySelector("#serial-number");
                        const birthdayInput = activeSecondTab.querySelector("#birthday");
                        const passportNumberInput = activeSecondTab.querySelector("#passport-number");
                        const tinNumberInput = activeSecondTab.querySelector("#tin-number");
                        let identificationNumber = '';
                        if (isLegal === 1) identificationNumber = (tinNumberInput && tinNumberInput.value) ? tinNumberInput.value.trim() : '';
                        else identificationNumber = (passportNumberInput && passportNumberInput.value) ? passportNumberInput.value.trim().toLocaleUpperCase() : '';
                        let serialNumber = (serialNumberInput && serialNumberInput.value) ? serialNumberInput.value.trim().toLocaleUpperCase() : '';
                        let birthDate = (birthdayInput && birthdayInput.value) ? birthdayInput.value.trim().toLocaleUpperCase() : '';

                        const countryCitizenshipDropdown = activeRadioGroup.querySelector("[data-country-citizenship]");
                        const countryCitizenshipList = activeRadioGroup.querySelector(".dropdown__list-items[data-country-citizenship]");
                        const countryCitizenshipInput = activeRadioGroup.querySelector("[name='country-citizenship']");
                        const name = activeRadioGroup.querySelector("#name");
                        const surname = activeRadioGroup.querySelector("#surname");
                        const fatherName = activeRadioGroup.querySelector("#father-name");
                        const livingAddress = activeRadioGroup.querySelector("#living-address");
                        const entityAddress = activeRadioGroup.querySelector("#entity-address");
                        const entityName = activeRadioGroup.querySelector('#entity-name');
                        alert_second_tab_input_items.style.display = "none";
                        isPersonValid = false
                        if (isLegal === 1 && !identificationNumber) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.please_insert_tin_number') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 1 && entityType === 4 && identificationNumber.length !== 10) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.ident_length_must_be_10') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 1 && entityType === 0 && identificationNumber.length < 8) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.ident_length_can_not_be_less_than_8') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && !identificationNumber) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.please_insert_fin_number') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType === 1 && identificationNumber.length !== 7) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.fin_number_should_be_7_length') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType === 2 && identificationNumber.length >= 7) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.fin_number_can_not_greater_or_equal_to_7_length') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType === 2 && identificationNumber.length < 5) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.fin_number_can_not_less_than_5_length') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType === 0 && identificationNumber.length > 20) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.passport_serial_and_number_can_not_greater_than_20_length') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType === 1 && !serialNumber) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.please_insert_serial_number') }}"
                            ], 'error');
                            return false;
                        }
                        if (isLegal === 0 && entityType !== 1 && !birthDate) {
                            showAlert(alert_second_tab_input_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                                "{{ __('messages.please_insert_birth_date') }}"
                            ], 'error');
                            return false;
                        }
                        if (birthDate && birthDate.trim()!=='') {
                            const [day, month, year] = birthDate.split('-');
                            birthDate = `${year}-${month}-${day}`;
                        }
                        showLoader();
                        try {
                            const response = await callService("/callService/his/getPersonDataForTpl", {
                                input: {
                                    identificationNumber,
                                    birthDate,
                                    serialNumber,
                                    isLegal,
                                    entityType
                                }
                            });
                            let responseData = response.data
                            if (responseData?.response && responseData.response.code !== '0') {
                                resetInputsAndDropdownsOnTabChange(true);
                                const backendMessage = responseData.response.message || '{{ __('messages.DATA_NOT_FOUND_ERROR') }}';
                                showAlert(alert_second_tab_input_items, '{{ __('messages.DATA_NOT_FOUND_ERROR') }}', [backendMessage], 'error');
                                return false;
                            }
                            isPersonValid = responseData.isValid
                            if(isPersonValid){
                                let azeOrForeign = (entityType === 4 || entityType === 1) ? "aze" : "foreign";
                                await populateActiveTabAndRadio(azeOrForeign);
                                alert_second_tab_info.style.display = "flex";
                                checkbox_wrapper.style.display = "flex";
                                if(responseData.personInfo){
                                    let countryCode= responseData.personInfo.country
                                    if (countryCode) {
                                        const countryItem = [...countryCitizenshipList.children].find(item => item.getAttribute("data-value")*1 === countryCode*1);
                                        if (countryItem) {
                                            selectDropdownItem(countryItem, countryCitizenshipDropdown, countryCitizenshipInput);
                                        }
                                    }
                                    if(entityType === 0 && isLegal === 0){
                                        name.value = responseData.personInfo.firstName
                                        surname.value = responseData.personInfo.lastName
                                        fatherName.value = responseData.personInfo.fatherName
                                        livingAddress.value = responseData.personInfo.address
                                    }
                                    if(entityType === 0 && isLegal === 1) {
                                        entityAddress.value = responseData.personInfo.address
                                        entityName.value = responseData.personInfo.companyName
                                    }

                                }
                            }
                        } catch (error) {
                            resetInputsAndDropdownsOnTabChange(true);
                            if (error.response && error.response.data?.response) {
                                const backendMessage = error.response.data.response.message;
                                showAlert(alert_second_tab_input_items, "{{ __('messages.SERVICE_ERROR') }}", [
                                    backendMessage
                                ], 'error');
                            }
                        }finally {
                            hideLoader();
                        }
                    }
                });

                buttonRegister.addEventListener("click", async () => {
                    alert_second_tab_validate_items.style.display = "none";
                    if (!isPersonValid) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.wrong_information') }}", [
                            "{{ __('messages.legal_or_physical_person_info_not_found') }}"
                        ], 'error');
                        return;
                    }
                    const activeSecondTab = document.querySelector(".person-content.active");
                    if (!activeSecondTab) return;

                    const activeRadioGroup = activeSecondTab.querySelector(".citizen-details.active");
                    if (!activeRadioGroup) return;
                    const mobileNumber = activeRadioGroup.querySelector(".mobile_number");
                    const countryCitizenshipInput = activeRadioGroup.querySelector("[name='country-citizenship']");

                    const serialNumberInput = activeSecondTab.querySelector("#serial-number");
                    const birthdayInput = activeSecondTab.querySelector("#birthday");
                    const passportNumberInput = activeSecondTab.querySelector("#passport-number");
                    const tinNumberInput = activeSecondTab.querySelector("#tin-number");
                    const name = activeRadioGroup.querySelector("#name");
                    const surname = activeRadioGroup.querySelector("#surname");
                    const fatherName = activeRadioGroup.querySelector("#father-name");
                    const entityAddress = activeRadioGroup.querySelector("#entity-address");
                    const entityName = activeRadioGroup.querySelector('#entity-name')
                    const livingAddress = activeRadioGroup.querySelector("#living-address");
                    const modalPayment = document.querySelector(".pay-payment-modal");
                    const overlay = document.querySelector(".overlay");
                    const agree = document.querySelector("#agree")?.checked || false;

                    if (!agree) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.please_fill_all_required_fields') }}", [
                            "{{ __('messages.agree_to_terms_required') }}"
                        ], 'error');
                        return false;
                    }
                    const requiredFields = Array.from(
                        activeSecondTab.querySelectorAll('[data-required="true"]')
                    ).filter(el => {
                        const wrapper = el.closest(".inputs-item, .dropdown");
                        return wrapper && wrapper.offsetParent !== null;
                    });
                    const missingFields = [];

                    requiredFields.forEach((field) => {
                        if (!field.value.trim()) {
                            const label = activeSecondTab.querySelector(`label[for="${field.name}"]`) || field.closest(".inputs-item")?.querySelector("label");
                            if (label) {
                                missingFields.push(label.textContent.trim());
                            }
                        }
                    });

                    if (missingFields.length > 0) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.please_fill_all_required_fields') }}", missingFields, 'error');
                        return false
                    }
                    let identNumber='';
                    if(isLegal === 1) identNumber = (tinNumberInput && tinNumberInput.value) ? tinNumberInput.value.trim() : '';
                    else identNumber = (passportNumberInput && passportNumberInput.value) ? passportNumberInput.value.trim() : '';
                    let currentPhoneNumber = '';
                    if (!mobNum) {
                        currentPhoneNumber = (mobileNumber && mobileNumber.value) ? mobileNumber.value.trim() : '';
                        if (currentPhoneNumber) {
                            currentPhoneNumber = currentPhoneNumber.replace(/[^0-9+\-\s()]/g, '');
                        }
                    } else {
                        currentPhoneNumber = mobNum;
                    }
                    const phoneNumber = currentPhoneNumber;
                    const email = activeRadioGroup.querySelector("#email").value ? activeRadioGroup.querySelector("#email").value.trim() : '';
                    if (phoneNumber.length < 5 ) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.mobile_number_should_be_inserted') }}"
                        ], 'error');
                        return false;
                    }

                    if (window.phone && !window.phone.isValid()) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            window.phone.getValidationError()
                        ], 'error');
                        return false;
                    }
                    if (isLegal === 0 && entityType === 1) {
                        const rawPhoneNumber = phoneNumber.replace(/[^0-9]/g, '');
                        // Azerbaijan numbers have 12 digits (+994...)
                        if (rawPhoneNumber.startsWith("994") && rawPhoneNumber.length !== 12) {
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.mobile_number_length_should_be_9_length') }}"
                        ], 'error');
                        return false;
                    }
                    }
                    if(email && isValidEmail(email) === false){
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.email_is_not_valid') }}"
                        ], 'error');
                        return false;
                    }
                    if(!insurancePeriod){
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.insurance_period_not_selected') }}"
                        ], 'error');
                        return false;
                    }
                    if(!insuranceFee){
                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_IS_INCORRECT') }}", [
                            "{{ __('messages.insurance_fee_is_not_calculated') }}"
                        ], 'error');
                        return false;
                    }
                    // const isConfirmed = await window.confirmActionWithModal();
                    // if (!isConfirmed) return;
                    const vehicle = {
                        plate: (plateInput && plateInput.value) ? plateInput.value.trim() : '',
                        regSerialNumber: (docInput && docInput.value) ? docInput.value.trim() : '',
                        type: typeInput ? typeInput.dataset.text?.trim() || '' : '',
                        typeId: (typeInput && typeInput.value) ? typeInput.value.trim() : '',
                        model: modelInput ? modelInput.dataset.text?.trim() || '' : '',
                        make: makeInput ? makeInput.dataset.text?.trim() || '' : '',
                        country: (countryInput && countryInput.value) ? countryInput.value.trim() : '',
                    };
                    let birthDateNewFormat = '';
                    if(birthdayInput && birthdayInput.value){
                        birthDateNewFormat = birthdayInput.value.trim();
                        const [day, month, year] = birthDateNewFormat.split('-');
                        birthDateNewFormat = `${year}-${month}-${day}`;
                    }
                    const person = {
                        firstName: (name && name.value) ? name.value.trim() : '',
                        lastName: (surname && surname.value) ? surname.value.trim() : '',
                        fatherName: (fatherName && fatherName.value) ? fatherName.value.trim() : (isLegal === 0 && entityType === 0 ? 'XXX' : ''),
                        identificationNumber: identNumber,
                        companyName: entityName ? entityName.value.trim() : '',
                        serialNumber: (serialNumberInput && serialNumberInput.value) ? serialNumberInput.value.trim() : '',
                        birthday: birthDateNewFormat,
                        isLegal,
                        address: isLegal ===1 ? ((entityAddress && entityAddress.value) ? entityAddress.value.trim() : '') : ((livingAddress && livingAddress.value) ? livingAddress.value.trim() : ''),
                        entityType,
                        country: (countryCitizenshipInput && countryCitizenshipInput.value) ? countryCitizenshipInput.value.trim() : '',
                    };
                    showLoader();
                    alert_second_tab_validate_items.style.display = "none";
                    const payload = {
                        vehicle: vehicle,
                        person: person,
                        voenInsurer: voenInsurer,
                        phoneNumber: phoneNumber,
                        email: email,
                        platformType: window.isTerminalMode ? '5': '4',
                        insurancePeriod: insurancePeriod,
                        insuranceFee: insuranceFee,
                    };

                    try {
                        const response = await callService('/callService/his/tplApiRegister', {
                            input: payload,
                        });
                        const responseData = response.data;
                        if (responseData?.response && responseData.response.code ==='0') {
                            let invoiceNo = response.data.paymentCode;
                            if (!invoiceNo) {
                                showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                                    "{{ __('messages.invoice_number_not_found') }}"
                                ], 'error');
                                return false;
                            }

                            // Terminal mode: send invoice to terminal device
                            if (window.isTerminalMode) {
                                try {
                                    window.location.href = "{{ env('TERMINAL_REDIRECT_URL', '/api/terminal/finish') }}" + "?paymentCode=" + invoiceNo;
                                } catch (terminalError) {
                                    console.error('Terminal Error:', terminalError);
                                    showAlert(alert_second_tab_validate_items, "{{ __('messages.terminal_error') }}", [
                                        "{{ __('messages.failed_to_communicate_with_terminal') }}"
                                    ], 'error');
                                }
                                return false;
                            }

                            // Web mode: show payment modal (original flow)
                            else {
                                try {
                                    const response2 = await callService('/callService/payment/invoiceInfo', {
                                        input: {
                                            accountType: null,
                                            dhsCode: '982000',
                                            fromDhs: true,
                                            invoiceNo: invoiceNo,
                                            account: null,
                                            invoiceType: "DOC1",
                                            paymentChannel: "HOP",
                                            searchType: "BY_DOC_NO"
                                        },
                                    });

                                    if (!response2.data.invoiceList || response2.data.invoiceList.length === 0) {
                                        showAlert(alert_second_tab_validate_items, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                                            "{{ __('messages.invoice_not_found') }}"
                                        ], 'error');
                                        return false;
                                    }
                                    window.paymentState = {
                                        invoiceList: [],
                                        transactionId: '',
                                    };

                                    const invoice = response2.data.invoiceList?.[0];
                                    const transactionId = invoice.transactionId.toString();

                                    if (invoice) {
                                        const invoiceRadio = document.querySelector("input[name='searchType'][value='invoice']");
                                        const codeInput = document.getElementById("code");
                                        const finalSearchBtn = document.getElementById("final-search-button");
                                        const invoiceGroup = document.getElementById("invoice-search-group");
                                        const plateGroup = document.getElementById("plate-search-group");

                                        if (invoiceRadio && codeInput && finalSearchBtn) {
                                            invoiceRadio.checked = true;
                                            invoiceGroup.style.display = 'block';
                                            plateGroup.style.display = 'none';
                                            codeInput.value = invoice.invoiceNo; // from response
                                            finalSearchBtn.style.display = 'block';
                                        }
                                        modalPayment.classList.add("active");
                                        overlay.classList.add("active");
                                        window.paymentState.transactionId = transactionId;
                                        window.paymentState.invoiceList = response2.data.invoiceList;
                                        document.querySelector('#transaction-id').value = transactionId;
                                        document.querySelector(".invoice-number").textContent = invoice.invoiceNo || "-";
                                        document.querySelector(".payer-name").textContent = invoice.payer?.fullName || "-";
                                        document.querySelector(".receiver-name").textContent = invoice.receiver?.fullName || "-";
                                        document.querySelector(".makePayment").textContent = invoice.additionalParams?.dqn || "-";
                                        document.querySelector(".amount-due").textContent = invoice.debt + " ₼";
                                        const rawDate = invoice.dueDate || "";
                                        const formattedDate = rawDate ? `${rawDate.slice(6, 8)}.${rawDate.slice(4, 6)}.${rawDate.slice(0, 4)} ${rawDate.slice(8, 10)}:${rawDate.slice(10, 12)}` : "-";
                                        document.querySelector(".due-date").textContent = formattedDate;
                                        resetInputsAndDropdownsOnTabChange();
                                        hideLoader();
                                    }

                                } catch (error) {
                                    if (error.response && error.response.data?.response) {
                                        const backendMessage = error.response.data.response.message;
                                        showAlert(alert_second_tab_validate_items, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                                    }
                                }
                            }
                        }else{
                            const backendMessage = responseData.response?.message || "{{ __('messages.DATA_NOT_FOUND_ERROR') }}";
                            showAlert(alert_second_tab_validate_items, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                            return false;
                        }
                    } catch (error) {
                        if (error.response && error.response.data?.response) {
                            const backendMessage = error.response.data.response.message;
                            showAlert(alert_second_tab_validate_items, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                            if(backendMessage === 'FAILED_ON_INSURANCE_COMPANY_SIGN'){
                                setTimeout(() => {
                                    window.location.href = "/registration";
                                }, 2000);
                            }
                        }
                    }finally {
                        hideLoader();
                    }
                });
            });
        </script>
    </div>
@endsection
