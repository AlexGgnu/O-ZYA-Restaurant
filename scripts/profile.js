document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('btn-save-profile');

    document.querySelectorAll('.btn-svg').forEach(btn => {
        btn.addEventListener('click', () => {
            btn.closest('.form-group-line__input-wrapper').querySelector('input').disabled = false;
            saveBtn.style.display = 'block';
        });
    });

    saveBtn.addEventListener('click', () => {
        fetch('./profile.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                lastname:  document.getElementById('name').value,
                firstname: document.getElementById('firstName').value,
                email:     document.getElementById('email').value,
                phone:     document.getElementById('phone').value,
                address:   document.getElementById('address').value
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Erreur lors de la mise à jour');
            }
        });
    });
});