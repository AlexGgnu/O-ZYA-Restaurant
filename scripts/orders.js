document.addEventListener('DOMContentLoaded', () => {
    const selects = document.querySelectorAll('select');

    selects.forEach(select => {
        select.addEventListener('change', () => {
            const form = select.closest('form');
            const orderId = form.querySelector('input[name="order_id"]').value;
            const statut = select.name === 'statut' ? select.value : form.querySelector('input[name="statut"]').value;
            const idLivreur = select.name === 'id_livreur' ? select.value : '';

            fetch('./orders.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId, statut: statut, id_livreur: idLivreur })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) window.location.reload();
            });
        });
    });
});