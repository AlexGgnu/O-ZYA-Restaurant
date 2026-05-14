document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btn-terminer');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const orderId = btn.dataset.orderId;

        fetch('./delivery.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = './delivery.php';
            } else {
                alert('Erreur impossible de terminer la livraison');
            }
        });
    });
});