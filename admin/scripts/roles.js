import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
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

/**
 * Reloads the navigation bar with grade items.
 */
async function fetchData() {

    // Fetch data
    let roles = [];
    try{
        roles = await requestGET('/role.php');
    } catch (error) {
        toast(error.message, true);
    }

    // Transform data to navbar items
    return roles.map(role => ({label: role.nom_role, id: role.id_role}));

}

/**
 * Saves the grade information.
 *
 * @param {number} id_role - The ID of the role to be saved.
 * @returns {Promise<void>} A promise that resolves when the grade is successfully saved.
 * @throws Will alert an error message if the request fails.
 */
async function saveRole(id_role){

    // Show loader
    showLoader();

    // Create data
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

    // Send data
    try {
        await requestPUT('/role.php?id=' + id_role.toString(), data);
        toast('Role mis à jour avec succès.');
        selectRole(id_role);
    } catch (error) {
        toast(error.message, true);
    }

    // Stop loader
    hideLoader();

}

/**
 * Deletes the grade from the DB.
*/
async function deleteRole(id_role){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/role.php?id=${id_role}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectRole);

    // Deleted message
    toast('Role supprimé avec succès.');

}

/**
 * Loads and displays grade information based on the provided grade ID.
 *
 * @param {number} id_role - The ID of the grade to be selected.
 * @returns {Promise<void>} A promise that resolves when the grade information has been fetched and displayed.
 */
async function selectRole(id_role, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();

    // Fetch grade information
    const role = await requestGET(`/role.php?id=${id_role}`);

    // Update displayed information
    prop_nom_role.value = role.nom_role;
    updateToggleStatus(prop_logs, role.p_log);
    updateToggleStatus(prop_boutique, role.p_boutique);
    updateToggleStatus(prop_users, role.p_utilisateur);
    updateToggleStatus(prop_grades, role.p_grade);
    updateToggleStatus(prop_roles, role.p_role);
    updateToggleStatus(prop_actualites, role.p_actualite);
    updateToggleStatus(prop_events, role.p_evenement);
    updateToggleStatus(prop_comptabilite, role.p_comptabilite);
    updateToggleStatus(prop_historique, role.p_achat);
    updateToggleStatus(prop_moderation, role.p_moderation);
    updateToggleStatus(prop_reunions, role.p_reunion);

    // Update save button
    save_btn.onclick = ()=>{
        saveRole(id_role);
    };

    // Delete button
    delete_btn.onclick = ()=>{
        swal({
            title: "Êtes vous sûr ?",
            text: "Cette action est définitive",
            icon: "warning",
            buttons: true,
            dangerMode: true,
          })
          .then((willDelete) => {
            if (willDelete) {
                deleteRole(id_role);
            }
          });
    };

    // Update name
    function updateName(){
        li.textContent = prop_nom_role.value;
    }
    prop_nom_role.onkeyup = updateName;

    // Hide loader
    hideLoader();

    // Hide skeleton
    hidePropertieSkeleton();
    
}

// Handle new user
new_btn.onclick = async ()=>{

    // Show loader
    showLoader();

    // Create new grade
    try {
        const { id_role } = await requestPOST('/role.php');
        refreshNavbar(fetchData, selectRole, id_role);
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }

};

// Load navbar
refreshNavbar(fetchData, selectRole);