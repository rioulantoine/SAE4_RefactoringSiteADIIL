import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

showPropertieSkeleton();

const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');
const prop_reunions = document.getElementById('prop_reunions');
const prop_nom_role = document.getElementById('prop_nom_role');
const prop_boutique = document.getElementById('prop_boutique');
const prop_users = document.getElementById('prop_users');
const prop_grades = document.getElementById('prop_grades');
const prop_roles = document.getElementById('prop_roles');
const prop_actualites = document.getElementById('prop_actualites');
const prop_events = document.getElementById('prop_events');
const prop_comptabilite = document.getElementById('prop_comptabilite');
const prop_historique = document.getElementById('prop_historique');
const prop_logs = document.getElementById('prop_logs');
const prop_moderation = document.getElementById('prop_moderation');

async function fetchData() {
    let roles = [];
    try{
        roles = await requestGET('index.php?page=api_roles');
    } catch (error) {
        toast(error.message, true);
    }
    return roles.map(role => ({label: role.nom_role, id: role.id_role}));
}

async function saveRole(id_role){
    showLoader();
    const data = {
        name: prop_nom_role.value === '' ? 'N/A' : prop_nom_role.value,
        permissions: {
            p_log: getToggleStatus(prop_logs),
            p_boutique: getToggleStatus(prop_boutique),
            p_reunion: getToggleStatus(prop_reunions),
            p_utilisateur: getToggleStatus(prop_users),
            p_grade: getToggleStatus(prop_grades),
            p_role: getToggleStatus(prop_roles),
            p_actualite: getToggleStatus(prop_actualites),
            p_evenement: getToggleStatus(prop_events),
            p_comptabilite: getToggleStatus(prop_comptabilite),
            p_achat: getToggleStatus(prop_historique),
            p_moderation: getToggleStatus(prop_moderation)
        }
    };

    try {
        await requestPUT('index.php?page=api_roles&id=' + id_role.toString(), data);
        toast('Role mis à jour avec succès.');
        selectRole(id_role);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteRole(id_role){
    showLoader();
    try {
        await requestDELETE(`index.php?page=api_roles&id=${id_role}`);
        refreshNavbar(fetchData, selectRole);
        toast('Role supprimé avec succès.');
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function selectRole(id_role, li){
    showPropertieSkeleton();
    showLoader();
    try {
        const role = await requestGET(`index.php?page=api_roles&id=${id_role}`);

        prop_nom_role.value = role.nom_role;
        updateToggleStatus(prop_logs, role.p_log_role);
        updateToggleStatus(prop_boutique, role.p_boutique_role);
        updateToggleStatus(prop_users, role.p_utilisateur_role);
        updateToggleStatus(prop_grades, role.p_grade_role);
        updateToggleStatus(prop_roles, role.p_roles_role);
        updateToggleStatus(prop_actualites, role.p_actualite_role);
        updateToggleStatus(prop_events, role.p_evenements_role);
        updateToggleStatus(prop_comptabilite, role.p_comptabilite_role);
        updateToggleStatus(prop_historique, role.p_achats_role);
        updateToggleStatus(prop_moderation, role.p_moderation_role);
        updateToggleStatus(prop_reunions, role.p_reunion_role);

        save_btn.onclick = () => saveRole(id_role);
        delete_btn.onclick = () => {
            swal({ title: "Êtes vous sûr ?", text: "Action définitive", icon: "warning", buttons: true, dangerMode: true })
            .then((willDelete) => { if (willDelete) deleteRole(id_role); });
        };
        prop_nom_role.onkeyup = () => { if(li) li.textContent = prop_nom_role.value; };
    } catch (e) { toast("Erreur lors de la sélection"); }
    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async ()=>{
    showLoader();
    try {
        const res = await requestPOST('index.php?page=api_roles');
        refreshNavbar(fetchData, selectRole, res.id_role);
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectRole);