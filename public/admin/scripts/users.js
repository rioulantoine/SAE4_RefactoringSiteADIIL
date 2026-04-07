import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');
const prop_img = document.getElementById('prop_img');
const prop_nom = document.getElementById('prop_nom');
const prop_prenom = document.getElementById('prop_prenom');
const prop_email = document.getElementById('prop_email');
const prop_roles = document.getElementById('prop_roles');
const prop_xp = document.getElementById('prop_xp');
const prop_tp = document.getElementById('prop_tp');

/**
 * Reloads the navigation bar with user items.
 */
async function fetchData() {

    // Fetch data
    let users = [];
    try{
        users = await requestGET('/index.php?page=api_users');
    } catch (error) {
        toast('Erreur lors du chargement des utilisateurs.', true);
    }

    // Transform data to navbar items
    return users
        .filter(user => user.prenom_membre !== 'N/A')
        .map(user => ({label: user.prenom_membre + ' ' + user.nom_membre.toUpperCase(), id: user.id_membre}));

}

/**
 * Saves the user information.
 *
 * @param {number} id_user - The ID of the user to be saved.
 */
async function saveUser(id_user){

    // Show loader
    showLoader();

    // Create data
    const data = {
        name: prop_nom.value === '' ? 'N/A' : prop_nom.value,
        firstname: prop_prenom.value === '' ? 'N/A' : prop_prenom.value,
        email: prop_email.value === '' ? 'N/A' : prop_email.value,
        tp: prop_tp.value,
        xp: prop_xp.value === '' ? 0 : prop_xp.value,
        roles: []
    };

    // Send data
    try {
        await requestPUT('/index.php?page=api_users&id=' + id_user.toString(), data);
        const roles = {roles: Array.from(prop_roles.children).filter(role => role.classList.contains('selected')).map(role => parseInt(role.getAttribute('id')))}
        await requestPUT('/index.php?page=api_userole&id=' + id_user.toString(), roles);
        toast('Utilisateur mis à jour avec succès.');
        selectUser(id_user);
    } catch (error) {
        toast(error.message, true);
    }

    // Stop loader
    hideLoader();

}

/**
 * Deletes the user from the DB.
*/
async function deleteUser(id_user){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/index.php?page=api_users&id=${id_user}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectUser);

    // Deleted message
    toast('Utilisateur supprimé avec succès.');

}

/**
 * Loads and displays user information based on the provided user ID.
 *
 * @param {number} id_member - The ID of the member to be selected.
 */
async function selectUser(id_member, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();

    // Fetch user information
    const user = await requestGET(`/index.php?page=api_users&id=${id_member}`);

    // Update displayed information
    prop_img.src = await getFullFilepath(user.pp_membre, '../ressources/default_images/user.jpg');
    prop_nom.value = user.nom_membre;
    prop_prenom.value = user.prenom_membre;
    prop_email.value = user.email_membre;
    prop_xp.value = user.xp_membre;
    prop_tp.value = user.tp_membre;

    // Update save button
    save_btn.onclick = ()=>{
        saveUser(id_member);
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
                deleteUser(id_member);
            }
          });
    };

    // Update name
    function updateName(){
        li.textContent = prop_prenom.value + ' ' + prop_nom.value.toUpperCase();
    }
    prop_nom.onkeyup = updateName;
    prop_prenom.onkeyup = updateName;

    // Update image
    document.getElementById('prop_img_edit').onclick = async ()=>{
        
        // Get file form
        const image = await openFileDialog();

        // Update image src
        const url = URL.createObjectURL(image);
        prop_img.src = url;

        // Show loader
        showLoader();

        // Send data
        try {
            await requestPATCH('/index.php?page=api_users&id=' + id_member.toString(), image);
            toast('Image mise à jour avec succès.');
        } catch (error) {
            toast(error.message, true);
        }

        // Stop loader
        hideLoader();

    };

    // Delete roles
    while (prop_roles.firstChild)
        prop_roles.removeChild(prop_roles.firstChild);

    // Add roles
    const roles = await requestGET('/index.php?page=api_roles'); // Get all roles
    const user_roles = (await requestGET(`/index.php?page=api_userole&id=${id_member}`)).map(role => role.id_role); // Get user roles;
    roles.forEach(role => {

        // Create role button
        const button = document.createElement('p');
        button.textContent = role.nom_role;
        button.setAttribute('id', role.id_role);
        prop_roles.appendChild(button);

        // Check it if added
        if(user_roles.includes(role.id_role))
            button.classList.add('selected');

        // Add event
        button.onclick = async ()=>{

            // Add or remove role
            if(button.classList.contains('selected')){
                button.classList.remove('selected');
            } else {
                button.classList.add('selected');
            }

        };

    });

    // Hide loader
    hideLoader();

    // Hide skeleton
    hidePropertieSkeleton();
    
}

// Handle new user
new_btn.onclick = async ()=>{

    // Show loader
    showLoader();

    // Create new user
    try {
        const { id_membre } = await requestPOST('/index.php?page=api_users');
        refreshNavbar(fetchData, selectUser, id_membre);
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }

};

// Load navbar
refreshNavbar(fetchData, selectUser);