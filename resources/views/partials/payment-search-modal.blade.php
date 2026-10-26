<div class="modal modal-payment">
    <div class="modal-top">
        <div class="modal-top__left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-credit-card-2.svg') }}" alt="" />
            </div>
            <h3 class="modal-title">{{ __('messages.payment') }}</h3>
        </div>
        <div class="modal-top__right">
            <button class="modal-close">
                <img src="{{ asset('images/icons/icon-close.svg') }}" alt="" />
            </button>
        </div>
    </div>

    <div class="modal-main">
        <div class="search-type-toggle">
            <label><input type="radio" name="searchType" value="plate" checked /> {{ __('messages.by_plate') }}</label>
            <label><input type="radio" name="searchType" value="invoice" /> {{ __('messages.by_invoice') }}</label>
        </div>

        <div id="invoice-search-group" class="search-section">
            <label for="code">{{ __('messages.payment_code_description') }}</label>
            <input
                id="code"
                type="text"
                class="input input--full modal-input onlyLettersNumbers"
                placeholder="{{ __('messages.payment_code_placeholder') }}"
            />
        </div>

        <div id="plate-search-group" class="search-section active">
            <label for="plate">{{ __('messages.search_by_plate_description') }}</label>
            <input
                id="plate"
                type="text"
                class="input input--full modal-input onlyLettersNumbers"
                placeholder="{{ __('messages.plate_placeholder') }}"
            />
            <button id="plate-search-button" class="button button-primary button--full" style="margin-top: 46px;">
                {{ __('messages.search_by_plate') }}
            </button>
            <div id="plate-invoice-list"></div>
        </div>
    </div>

    <div class="modal-bottom" id="modalBottom" style="display: none">
        <button class="button button-primary button--full" id="final-search-button">{{ __('messages.search_by_invoice') }}</button>
    </div>
    <div class="alert alert_payment_search_modal" style="display: none">
        <div class="alert-left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="" />
            </div>
        </div>
        <div class="alert-right">
            <h4 class="alert-title">{{ __('messages.DATA_NOT_FOUND_ERROR') }}</h4>
            <p class="alert-text">{{ __('messages.search_no_data') }}</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radioButtons = document.querySelectorAll("input[name='searchType']");
        const invoiceGroup = document.getElementById("invoice-search-group");
        const plateGroup = document.getElementById("plate-search-group");
        const plateInput = document.getElementById("plate");
        const codeInput = document.getElementById("code");
        const plateSearchBtn = document.getElementById("plate-search-button");
        const finalSearchBtn = document.getElementById("final-search-button");
        const modalBottom = document.getElementById("modalBottom");
        const plateInvoiceList = document.getElementById("plate-invoice-list");
        const alertContainer = document.querySelector(".alert_payment_search_modal");
        const modalSuccess = document.querySelector(".pay-payment-modal");
        const overlay = document.querySelector(".overlay");

        let selectedPlateInvoice = null;

        function resetAll() {
            alertContainer.style.display = 'none';
            selectedPlateInvoice = null;
            plateInvoiceList.innerHTML = '';
            finalSearchBtn.style.display = 'none';
            modalBottom.style.display = 'none';
        }

        function handleRadioChange(value) {
            resetAll();
            if (value === 'invoice') {
                invoiceGroup.classList.add('active');
                plateGroup.classList.remove('active');
                finalSearchBtn.style.display = 'block';
                modalBottom.style.display = 'flex';
                plateInput.value = '';
            } else {
                invoiceGroup.classList.remove('active');
                plateGroup.classList.add('active');
                codeInput.value = '';
            }
        }

        radioButtons.forEach(radio => {
            radio.addEventListener('change', function (e) {
                handleRadioChange(e.target.value);
            });
        });

        codeInput.addEventListener('input', function () {
            resetAll();
            finalSearchBtn.style.display = codeInput.value.trim() ? 'block' : 'none';
            modalBottom.style.display = codeInput.value.trim() ? 'flex' : 'none';
        });

        plateInput.addEventListener('input', function () {
            resetAll();
            plateSearchBtn.style.display = plateInput.value.trim() ? 'block' : 'none';
            modalBottom.style.display = 'none';
            finalSearchBtn.style.display = 'none';
        });

        plateSearchBtn.addEventListener('click', async () => {
            const plate = plateInput.value.trim();
            if (!plate) {
                showAlert(alertContainer, "{{ __('messages.SEARCH_DATA_NOT_FOUND') }}", [
                    "{{ __('messages.plate_number_required') }}"
                ], 'error');
                return;
            }

            resetAll();
            showLoader();

            let data = [];

            try {
                const response = await callService('/callService/his/getPaymentCodes', {
                    input: { plate }
                });
                const res = response.data;
                if(res?.response && res.response.code !=='0') {
                    const backendMessage = res.response.message || '{{ __('messages.service_failed') }}';
                    showAlert(alertContainer, '{{ __('messages.service_failed') }}', [backendMessage], 'error');
                    return false;
                }

                data = response?.data?.paymentCodes || [];
                if (data.length === 0) {
                    showAlert(alertContainer, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                        "{{ __('messages.no_invoices_found') }}"
                    ], 'error');
                    return false;
                }
            } catch (error) {
                if (error.response && error.response.data?.response) {
                    const backendMessage = error.response.data.response.message;
                    showAlert(alertContainer, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                } else {
                    showAlert(alertContainer, "{{ __('messages.service_error') }}", [
                        "Unexpected error occurred."
                    ], 'error');
                }
            } finally {
                hideLoader();
            }
            if( data.length>0) {
                const rows = data.map((item, index) => `
                <tr>
                    <td><input type="radio" name="plateInvoice" value="${item.paymentCode}" data-index="${index}"></td>
                    <td>${item.paymentCode}</td>
                    <td>${item.amount}</td>
                    <td>${item.docDate.slice(6, 8)}.${item.docDate.slice(4, 6)}.${item.docDate.slice(0, 4)}  ${item.docDate.slice(8, 10)}:${item.docDate.slice(10, 12)}</td>
                </tr>
            `).join('');

                plateInvoiceList.innerHTML = `
                <table class="plate-invoice-list">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ __('messages.invoice_no') }}</th>
                            <th>{{ __('messages.amount') }}</th>
                            <th>{{ __('messages.policy_registration_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            `;

                const radioInputs = document.querySelectorAll("input[name='plateInvoice']");

                radioInputs.forEach(radio => {
                    radio.addEventListener('change', () => {
                        selectedPlateInvoice = radio.value;
                        finalSearchBtn.style.display = 'block';
                        modalBottom.style.display = 'flex';

                        document.querySelectorAll(".plate-invoice-list tbody tr").forEach(row => {
                            row.classList.remove('selected');
                        });

                        radio.closest("tr").classList.add("selected");
                        plateSearchBtn.style.display = 'none';
                    });
                });
                if (radioInputs.length === 1) {
                    // radioInputs[0].checked = true;
                    // selectedPlateInvoice = radioInputs[0].value;
                    // finalSearchBtn.style.display = 'block';
                    // modalBottom.style.display = 'flex';
                    // radioInputs[0].closest("tr").classList.add("selected");
                    // plateSearchBtn.style.display = 'none';
                }
            }
        });

        finalSearchBtn.addEventListener('click', async () => {
            const mode = document.querySelector("input[name='searchType']:checked").value;
            const invoiceNo = mode === 'invoice' ? codeInput.value.trim() : selectedPlateInvoice;

            if (!invoiceNo) {
                showAlert(alertContainer, "{{ __('messages.SEARCH_DATA_NOT_FOUND') }}", [
                    "{{ __('messages.invoice_number_required') }}"
                ], 'error');
                return;
            }

            showLoader();
            try {
                const response = await callService('/callService/payment/invoiceInfo', {
                    input: {
                        accountType: null,
                        dhsCode: '982000',
                        fromDhs: true,
                        invoiceNo: invoiceNo,
                        account: null,
                        invoiceType: "DOC1",
                        paymentChannel: "HOP",
                        searchType: "BY_DOC_NO"
                    }
                });

                if (!response.data.invoiceList || response.data.invoiceList.length === 0) {
                    showAlert(alertContainer, "{{ __('messages.DATA_NOT_FOUND_ERROR') }}", [
                        "{{ __('messages.invoice_not_found') }}"
                    ], 'error');
                    return;
                }

                const invoice = response.data.invoiceList[0];
                const transactionId = invoice.transactionId?.toString() || "";

                window.paymentState = {
                    invoiceList: response.data.invoiceList,
                    transactionId: transactionId
                };

                document.querySelector('#transaction-id').value = transactionId;
                document.querySelector('.invoice-number').textContent = invoice.invoiceNo || "-";
                document.querySelector('.payer-name').textContent = invoice.payer?.fullName || "-";
                document.querySelector('.receiver-name').textContent = invoice.receiver?.fullName || "-";
                document.querySelector('.makePayment').textContent = invoice.additionalParams?.dqn || "-";
                document.querySelector('.company-name').textContent = invoice.receiver?.fullName || "-";
                document.querySelector('.amount-due').textContent = (invoice.debt || 0) + " ₼";
                const rawDate = invoice.dueDate || "";
                document.querySelector('.due-date').textContent = rawDate ? `${rawDate.slice(6, 8)}.${rawDate.slice(4, 6)}.${rawDate.slice(0, 4)} ${rawDate.slice(8, 10)}:${rawDate.slice(10, 12)}` : "-";

                document.querySelector('.modal-payment').classList.remove("active");
                modalSuccess.classList.add("active");
                overlay.classList.add("active");
            } catch (error) {
                if (error.response && error.response.data?.response) {
                    const backendMessage = error.response.data.response.message;
                    showAlert(alertContainer, "{{ __('messages.service_error') }}", [backendMessage], 'error');
                } else {
                    showAlert(alertContainer, "{{ __('messages.service_error') }}", [
                        "Unexpected error occurred."
                    ], 'error');
                }
            } finally {
                hideLoader();
            }
        });
    });
</script>

<style>
    .search-type-toggle {
        display: flex;
        padding-left: 2px;
        gap: 20px;
        margin-bottom: 16px;
        align-items: center;
    }

    .search-type-toggle label {
        display: flex;
        align-items: center;
        font-weight: 500;
        cursor: pointer;
    }

    .search-type-toggle input[type="radio"] {
        margin-right: 6px;
    }

    .search-section {
        display: none;
    }

    .search-section.active {
        display: block;
    }

    .plate-invoice-list {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 16px;
    }

    .plate-invoice-list th,
    .plate-invoice-list td {
        padding: 10px 12px;
        border: 1px solid #ddd;
        text-align: left;
    }

    .plate-invoice-list th {
        background-color: #f7f7f7;
        font-weight: 600;
    }

    .plate-invoice-list tr.selected {
        background-color: #e6f7ff;
    }

    .modal-bottom .button-primary {
        margin-top: 16px;
    }
</style>
