@extends('layouts.app')

@section('title', __('messages.home_title'))

@section('content')
    <div class="home">
        @include('partials/mobile-menu')
        @include('partials/payment-info-modal')
        @include('partials/pay-payment-modal')
        @include('partials/payment-search-modal')
        @include('partials/header')

        <main class="main">
            <div class="container" style="margin-bottom: 70px">
                <div class="card">
                    {!! __('messages.create_an_insurance')  !!}
                    <a href="{{ route('registration') }}" class="button button-tpl">
                        {{ __('messages.contract_registration') }}
                        <img src="{{ asset('images/icons/icon-add.svg') }}" alt="" />
                    </a>
                </div>

          <div class="card">
            <h2>
                {!! __('messages.check_contract')  !!}
            </h2>

              <div class="input-search-wrapper">

                  <input type="text" class="input-search onlyLettersNumbers" id="searchInput" placeholder="{{ __('messages.search') }}" />

                  <div class="dropdown" id="searchTypeDropdown">
                      <button class="dropdown__button" type="button" id="dropdownToggle">
                          <span id="dropdownLabel">{{ __('messages.plate_number') }}</span>
                          <img src="{{ asset('/images/icons/icon-chevron-down-solid.svg') }}" alt="" />
                      </button>

                      <ul class="dropdown__list" id="dropdownList">
                          <div class="dropdown__list-items">
                              <li class="dropdown__list-item searchItems" data-value="carNumber">
                                  {{ __('messages.plate_number') }}
                              </li>
                              <li class="dropdown__list-item searchItems" data-value="policyNumber">
                                  {{ __('messages.policy_number') }}
                              </li>
                          </div>
                      </ul>

                      <input type="hidden" class="dropdown__input_hidden" id="searchType" name="searchType" value="carNumber" />
                  </div>

                  <div class="buttons">
                       <button class="button-icon" id="searchBtn">
                          <img src="{{ asset('images/icons/icon-search-white.svg') }}" alt="" />
                      </button>
                  </div>
              </div>
              <div class="alert alert-error show_search_errors" style="display: none; width: 100%;">
                  <div class="alert-left">
                      <div class="icon">
                          <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
                      </div>
                  </div>
                  <div class="alert-right">
                      <h4 class="alert-title">{{ __('messages.please_fill_all_required_fields') }}</h4>
                  </div>
              </div>

          </div>
        <div class="companies">
                    <img src="{{ asset('images/footer_' . app()->getLocale() . '.png') }}" alt="footer"
                        class="companies-image" />
        </div>
        </div>
      </main>



        @include('partials/footer')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const plateCode = urlParams.get("plate-number");
            const searchInput = document.getElementById('searchInput');
            const dropdownLabel = document.getElementById('dropdownLabel');
            const dropdownToggle = document.getElementById('dropdownToggle');
            const dropdownList = document.getElementById('dropdownList');
            const searchTypeInput = document.getElementById('searchType');
            const searchBtn = document.getElementById('searchBtn');
            const modalPayment = document.querySelector(".payment-info-modal");
            const overlay = document.querySelector(".overlay");
            const show_search_errors = document.querySelector(".show_search_errors");
            // Toggle dropdown visibility
            dropdownToggle.addEventListener('click', () => {
                dropdownList.classList.toggle('active');
            });

            // Handle dropdown item selection
            dropdownList.querySelectorAll('.dropdown__list-item').forEach((item) => {
                item.addEventListener('click', () => {
                    const value = item.getAttribute('data-value');
                    dropdownLabel.textContent = item.textContent.trim();
                    searchTypeInput.value = value;
                    searchInput.value=''
                    show_search_errors.style.display = 'none';
                    dropdownList.classList.remove('active');
                });
            });

            document.querySelector('.company-name').textContent = '-';
            document.querySelector('.vehicle-model').textContent = '-';
            document.querySelector('.vehicle-make').textContent = '-';
            document.querySelector('.contract-status').textContent = '-';
            document.querySelector('.plate-number').textContent = '-';
            document.querySelector('.contract-number').textContent = '-';
            // Search button click
            searchBtn.addEventListener('click', handleSearch);
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    handleSearch();
                }
            });
            if (plateCode) {
                if (searchInput) searchInput.value = plateCode;
                handleSearch()
                const cleanUrl = window.location.origin + window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl);
            }
            async function handleSearch() {
                const inputValue = searchInput.value.trim();
                const selectedType = searchTypeInput.value;
                show_search_errors.style.display = 'none';
                if (!inputValue) {
                    showAlert(show_search_errors, "{{ __('messages.wrong_information') }}", [
                        "{{ __('messages.please_fill_required_field') }}"
                    ], 'error');
                    return false;
                }


                const payload = {
                    isGreenCard: false,
                    policyNumber: selectedType === 'policyNumber' ? inputValue : '',
                    carNumber: selectedType === 'carNumber' ? inputValue : '',
                };
                showLoader();
                try {
                    const response = await callService('/callService/his/getBorderVehiclePoliciesByVehicle', {
                        input :{
                            policyNumber: selectedType === 'policyNumber' ? inputValue : '',
                            carNumber: selectedType === 'carNumber' ? inputValue : '',
                        }
                    });
                    const data = response?.data?.resultHash[0]

                    if (!data) {
                        showAlert(show_search_errors, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                            "{{ __('messages.DATA_NOT_FOUND_ERROR') }}"
                        ], 'error');
                        return false;
                    }
                    modalPayment.classList.add("active");
                    overlay.classList.add("active");
                    document.querySelector(".expiration_date").textContent = data.effectiveToDate ? `${data.effectiveToDate.slice(6, 8)}.${data.effectiveToDate.slice(4, 6)}.${data.effectiveToDate.slice(0, 4)} ${data.effectiveToDate.slice(8, 10)}:${data.effectiveToDate.slice(10, 12)}` : "-";
                    document.querySelector('.company-name').textContent = data.insurerName || '-';
                    document.querySelector('.vehicle-model').textContent = data.model || '-';
                    document.querySelector('.vehicle-make').textContent = data.make || '-';
                    document.querySelector('.contract-status').textContent = data.policyStatus || '-';
                    document.querySelector('.plate-number').textContent = data.dqn || '-';
                    document.querySelector('.contract-number').textContent = data.serialNumber || '-';
                    document.querySelector('.vehicle-type').textContent = data.vehicleType || '-';
                    document.querySelector('.insurance-period').textContent = (data.insurancePeriod+" "+"{{ __('messages.month') }}") || '-';

                    document.getElementById('vehicleModal').classList.add('active');
                } catch (error) {
                    if (error.response && error.response.data?.response) {
                        const backendMessage = error.response.data.response.message;
                        showAlert(show_search_errors, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                    }
                }
                finally {
                    hideLoader();
                }
            };

            // Optional: click outside to close dropdown
            document.addEventListener('click', (e) => {
                if (!dropdownToggle.contains(e.target) && !dropdownList.contains(e.target)) {
                    dropdownList.classList.remove('active');
                }
            });
        });
    </script>


@endsection


