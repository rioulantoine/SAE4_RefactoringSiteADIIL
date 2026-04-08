document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('#carte_credit form');
    if (!form) {
        return;
    }

    form.addEventListener('submit', function (event) {
        var card = document.getElementById('numero_carte').value.split(' ').join('');
        var exp = document.getElementById('expiration').value;
        var cvv = document.getElementById('cvv').value;

        if (card.length !== 16 || isNaN(card)) {
            alert('Numéro de carte invalide.');
            event.preventDefault();
            return;
        }

        if (exp.length !== 5 || exp.charAt(2) !== '/') {
            alert('Date d\'expiration invalide.');
            event.preventDefault();
            return;
        }

        var mois = exp.substring(0, 2);
        var annee = exp.substring(3, 5);
        var now = new Date();
        var anneeActuelle = now.getFullYear() % 100;

            if (isNaN(mois) || isNaN(annee) 
                || Number(mois) < 1 || Number(mois) > 12 
                || Number(annee) < anneeActuelle || (Number(annee) === anneeActuelle && Number(mois) < (now.getMonth() + 1))) {
            alert('Date d\'expiration invalide.');
            event.preventDefault();
            return;
        }

        if (cvv.length !== 3 || isNaN(cvv)) {
            alert('CVV invalide.');
            event.preventDefault();
            return;
        }
    });
});
