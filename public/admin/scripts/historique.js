// Imports
import { requestGET, requestPATCH } from './ajax.js';
import { getToggleStatus } from './toggle.js';

// DOM Elements
const table = document.getElementById('table');
const tbody = table.getElementsByTagName('tbody')[0];
const toggle_boutique = document.getElementById('toggle_boutique');
const toggle_grades =  document.getElementById('toggle_grades');
const toggle_events = document.getElementById('toggle_events');
const userSearch = document.getElementById('userSearch');

// Values (Correction de la route API)
const default_data = await requestGET('/index.php?page=api_purchase');
default_data.forEach(item => {
    item.user = (item.nom_membre || '').toUpperCase() + ' ' + (item.prenom_membre || '');
});

async function validateOrder(idCommande) {
    await requestPATCH('/index.php?page=api_purchase', { id_commande: idCommande });
    const item = default_data.find(row => Number(row.id_commande) === Number(idCommande));
    if (item) {
        item.statut = 'Récupéré';
    }
    loadData();
}

// Load data
function loadData(){

    // Remove all lines of the table
    while (tbody.firstChild)
        tbody.removeChild(tbody.firstChild);

    // Filter data
    let data = default_data;
    if (!getToggleStatus(toggle_boutique)) {
        data = data.filter(item => item.type_transaction !== 'Commande');
    }
    if (!getToggleStatus(toggle_grades)) {
        data = data.filter(item => item.type_transaction !== 'Adhesion');
    }
    if (!getToggleStatus(toggle_events)) {
        data = data.filter(item => item.type_transaction !== 'Inscription');
    }
    if (userSearch.value !== '') {
        data = data.filter(item => item.user.toUpperCase().includes(userSearch.value.toUpperCase()));
    }

    // Add data to the table
    data.forEach(item => {
        const row = document.createElement('tr');

        const typeCell = document.createElement('td');
        typeCell.textContent = item.type_transaction;
        row.appendChild(typeCell);

        const elementCell = document.createElement('td');
        elementCell.textContent = item.element;
        row.appendChild(elementCell);

        const utilisateurCell = document.createElement('td');
        utilisateurCell.textContent = item.user;
        row.appendChild(utilisateurCell);

        const dateCell = document.createElement('td');
        dateCell.textContent = item.date_transaction.split(' ')[0];
        row.appendChild(dateCell);

        const quantiteCell = document.createElement('td');
        quantiteCell.textContent = item.quantite;
        row.appendChild(quantiteCell);

        const prixCell = document.createElement('td');
        prixCell.textContent = parseFloat(item.montant).toFixed(2) + ' €';
        row.appendChild(prixCell);

        const paiementCell = document.createElement('td');
        paiementCell.textContent = item.mode_paiement;
        row.appendChild(paiementCell);

        const statutCell = document.createElement('td');
        const isPending = item.statut === 'En attente';
        statutCell.textContent = item.statut;
        row.appendChild(statutCell);

        const actionCell = document.createElement('td');
        if (item.type_transaction === 'Commande' && isPending && item.id_commande) {
            const validateButton = document.createElement('button');
            validateButton.textContent = 'Valider';
            validateButton.className = 'validate-order-btn';
            validateButton.addEventListener('click', async () => {
                validateButton.disabled = true;
                try {
                    await validateOrder(item.id_commande);
                } catch (error) {
                    validateButton.disabled = false;
                    console.error(error);
                    alert('Impossible de valider la commande.');
                }
            });
            actionCell.appendChild(validateButton);
        } else {
            actionCell.textContent = '-';
        }
        row.appendChild(actionCell);

        tbody.appendChild(row);
    });

}

// Call loadData
loadData();

// Set update events
toggle_boutique.addEventListener('click', loadData);
toggle_grades.addEventListener('click', loadData);
toggle_events.addEventListener('click', loadData);
userSearch.addEventListener('input', loadData);