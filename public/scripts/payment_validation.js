document.addEventListener('DOMContentLoaded', function () {
    var cardForm = document.querySelector('#carte_credit form');
    if (!cardForm) {
        return;
    }

    cardForm.addEventListener('submit', function (event) {
        var cardNumber = document.getElementById('numero_carte');
        var expiration = document.getElementById('expiration');
        var cvv = document.getElementById('cvv');
        var cardValue = cardNumber ? cardNumber.value.split(' ').join('') : '';
        var expirationValue = expiration ? expiration.value : '';
        var cvvValue = cvv ? cvv.value : '';

        if (!cardValue || cardValue.length !== 16) {
            alert('Numéro de carte invalide.');
            event.preventDefault();
            return;
        }

        if (!expirationValue || expirationValue.length !== 5 || expirationValue.charAt(2) !== '/') {
            alert('Date d\'expiration invalide.');
            event.preventDefault();
            return;
        }

        if (!cvvValue || cvvValue.length !== 3) {
            alert('CVV invalide.');
            event.preventDefault();
            return;
        }
    });
});
