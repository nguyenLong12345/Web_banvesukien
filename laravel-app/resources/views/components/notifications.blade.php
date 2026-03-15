<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="liveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                Thông báo thành công!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
    function showAlert(message, type = 'success') {
        const toastEl = document.getElementById('liveToast');
        const toastMessage = document.getElementById('toastMessage');
        
        if (toastEl && toastMessage) {
            // Set message
            toastMessage.textContent = message;
            
            // Set style based on type
            toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'bg-info');
            toastEl.classList.add(`bg-${type}`);
            
            // Show toast
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();
        }
    }
</script>
