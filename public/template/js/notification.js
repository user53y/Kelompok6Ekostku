document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.querySelector('.notification-icon');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    // Toggle notification dropdown
    notificationIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        notificationDropdown.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!notificationIcon.contains(e.target)) {
            notificationDropdown.classList.remove('show');
        }
    });
});

function confirmPayment(paymentId) {
    console.log('Confirming payment:', paymentId); // Debug log

    fetch(`/approve-payment/${paymentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response received:', response.status); // Debug log
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Gagal memproses pembayaran');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success data:', data); // Debug log
        if(data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: data.message || 'Pembayaran telah dikonfirmasi',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(error => {
        console.error('Error details:', error); // Debug log
        Swal.fire({
            title: 'Error',
            text: error.message || 'Gagal memproses pembayaran',
            icon: 'error'
        });
    });
}

function rejectPayment(paymentId) {
    fetch(`/reject-payment/${paymentId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if(data.success) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Pembayaran telah ditolak',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        } else {
            throw new Error(data.message || 'Terjadi kesalahan');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Gagal menolak pembayaran', 'error');
    });
}
