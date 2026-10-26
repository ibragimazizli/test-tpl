<div class="modal payment-info-modal" id="vehicleModal">
    <div class="modal-top">
        <div class="modal-top__left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-contract.svg') }}" alt="" />
            </div>
            <h3 class="modal-title">{{ __('messages.border_contract') }}</h3>
        </div>
        <div class="modal-top__right" style="display: flex; gap: 10px">
            <button type="button" onclick="generatePDFFromModal('#vehicleModal')"
                style="background:transparent; border:none; cursor:pointer;" class="button-icon-transparent">
                <img src="{{ asset('images/icons/icon-download-2.svg') }}" alt="" />
            </button>
            <button type="button" class="modal-close"
                onclick="document.getElementById('vehicleModal').classList.remove('active')">
                <img src="{{ asset('images/icons/icon-close.svg') }}" alt="" />
            </button>
        </div>
    </div>

    <div class="modal-main">
        <div class="modal-list">
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.insurance_company') }}:</div>
                <div class="modal-item-content company-name">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.model') }}:</div>
                <div class="modal-item-content vehicle-model">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.make') }}:</div>
                <div class="modal-item-content vehicle-make">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.vehicle_type') }}:</div>
                <div class="modal-item-content vehicle-type">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.contract_status') }}:</div>
                <div class="modal-item-content contract-status">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.insurance_period') }}:</div>
                <div class="modal-item-content insurance-period">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.plate') }}:</div>
                <div class="modal-item-content plate-number">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.contract_number') }}:</div>
                <div class="modal-item-content contract-number">-</div>
            </div>
            <div class="modal-item">
                <div class="modal-item-title">{{ __('messages.expiration_date') }}:</div>
                <div class="modal-item-content expiration_date">-</div>
            </div>
            <span id="pdf-footer-text2" style="display:none;">{!! __('messages.pdf_footer_text_2') !!}</span>
        </div>
    </div>
</div>
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
