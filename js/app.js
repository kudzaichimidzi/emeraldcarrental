
document.addEventListener('DOMContentLoaded', function() {
    const cardOption = document.querySelector('input[value="card"]');
    const cashOption = document.querySelector('input[value="cash"]');
    const cardFields = document.getElementById('card-fields');

    function toggleFields() {
        if (cardOption.checked) {
            cardFields.style.display = 'block';
        } else {
            cardFields.style.display = 'none';
        }
    }

    cardOption.addEventListener('change', toggleFields);
    cashOption.addEventListener('change', toggleFields);
});

