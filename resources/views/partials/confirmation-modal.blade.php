<div class="modal" id="confirmationModal">
    <div class="modal-top">
        <div class="modal-top__left">
            <div class="icon">
                <img src="{{ asset('images/icons/icon-warning.svg') }}" alt="warning" />
            </div>
            <h3 class="modal-title">{{ __('messages.confirmation_title') }}</h3>
        </div>
        <div class="modal-top__right">
            <button class="modal-close cancel-button">
                <img src="{{ asset('images/icons/icon-close.svg') }}" alt="close" />
            </button>
        </div>
    </div>

    <div class="modal-main">
        <p class="modal-text">{{ __('messages.confirm_registration_text') }}</p>
        <div class="modal-actions" style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
            <button class="button button-danger cancel-button">
                {{ __('messages.cancel') }}
            </button>
            <button class="button button-primary confirm-button">
                {{ __('messages.confirm') }}
            </button>
        </div>
    </div>
</div>

<script>
    window.confirmActionWithModal = function () {
        return new Promise((resolve) => {
            const modal = document.getElementById("confirmationModal");
            const overlay = document.querySelector(".overlay");
            modal.classList.add("active");
            overlay.classList.add("active");

            const confirmBtn = modal.querySelector(".confirm-button");
            const cancelBtns = modal.querySelectorAll(".cancel-button");

            function handleConfirm() {
                cleanup();
                resolve(true);
            }

            function handleCancel() {
                cleanup();
                resolve(false);
            }

            function cleanup() {
                modal.classList.remove("active");
                overlay.classList.remove("active");
                confirmBtn.removeEventListener("click", handleConfirm);
                cancelBtns.forEach(btn => btn.removeEventListener("click", handleCancel));
            }

            confirmBtn.addEventListener("click", handleConfirm);
            cancelBtns.forEach(btn => btn.addEventListener("click", handleCancel));
        });
    };


</script>
