document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const reservationId = urlParams.get('id');
    const currentReservationIdSpan = document.getElementById('currentReservationId');
    const statusSelect = document.getElementById('status');
    const reservationForm = document.getElementById('reservationForm');
    const messageDiv = document.getElementById('message');
    currentReservationIdSpan.textContent = reservationId;

    reservationForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const selectedStatus = statusSelect.value;
        fetch('sql/schema.sql', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                reservationId: reservationId,
                status: selectedStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.textContent = "Status succesvol bijgewerkt.";
            } else {
                messageDiv.textContent = "Er is een fout opgetreden.";
            }
        });
    });
});
