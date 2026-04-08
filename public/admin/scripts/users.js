import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";

showPropertieSkeleton();

const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');
const prop_img = document.getElementById('prop_img');
const prop_nom = document.getElementById('prop_nom');
const prop_prenom = document.getElementById('prop_prenom');
const prop_email = document.getElementById('prop_email');
const prop_roles = document.getElementById('prop_roles');
const prop_xp = document.getElementById('prop_xp');
const prop_tp = document.getElementById('prop_tp');

async function fetchData() {
    let users = [];
    try{
        users = await requestGET('/index.php?page=api_users');
    } catch (error) {
        toast('Erreur lors du chargement des utilisateurs.', true);
    }
    return users
        .filter(user => user.prenom_membre !== 'N/A')
        .map(user => ({label: user.prenom_membre + ' ' + user.nom_membre.toUpperCase(), id: user.id_membre}));
}

async function saveUser(id_user){
    showLoader();
    const data = {
        name: prop_nom.value === '' ? 'N/A' : prop_nom.value,
        firstname: prop_prenom.value === '' ? 'N/A' : prop_prenom.value,
        email: prop_email.value === '' ? 'N/A' : prop_email.value,
        tp: prop_tp.value,
        xp: prop_xp.value === '' ? 0 : prop_xp.value,
        roles: []
    };

    try {
        await requestPUT('/index.php?page=api_users&id=' + id_user.toString(), data);
        const roles = {roles: Array.from(prop_roles.children).filter(role => role.classList.contains('selected')).map(role => parseInt(role.getAttribute('id')))}
        await requestPUT('/index.php?page=api_userole&id=' + id_user.toString(), roles);
        toast('Utilisateur mis à jour avec succès.');
        selectUser(id_user);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteUser(id_user){
    showLoader();
    await requestDELETE(`/index.php?page=api_users&id=${id_user}`);
    refreshNavbar(fetchData, selectUser);
    toast('Utilisateur supprimé avec succès.');
}

async function selectUser(id_member, li){
    showPropertieSkeleton();
    showLoader();

    const user = await requestGET(`/index.php?page=api_users&id=${id_member}`);
    const defaultImagePath = window.location.origin + (window.base || window.parent?.base || '') + 'public/admin/ressources/default_images/user.jpg';
    
    if (user.pp_membre && user.pp_membre.startsWith('http')) {
        prop_img.src = user.pp_membre;
    } else if (user.pp_membre && user.pp_membre !== "default.png" && user.pp_membre !== "N/A") {
        prop_img.src = window.location.origin + (window.base || window.parent?.base || '') + 'public/api/files/' + user.pp_membre;
    } else {
        prop_img.src = defaultImagePath;
    }

    prop_nom.value = user.nom_membre;
    prop_prenom.value = user.prenom_membre;
    prop_email.value = user.email_membre;
    prop_xp.value = user.xp_membre;
    prop_tp.value = user.tp_membre;

    save_btn.onclick = ()=>{
        saveUser(id_member);
    };

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

    function updateName(){
        if(li) li.textContent = prop_prenom.value + ' ' + prop_nom.value.toUpperCase();
    }
    prop_nom.onkeyup = updateName;
    prop_prenom.onkeyup = updateName;

    document.getElementById('prop_img_edit').onclick = async ()=>{
        const image = await openFileDialog();
        const url = URL.createObjectURL(image);
        prop_img.src = url;

        showLoader();

        try {
            const formData = new FormData();
            formData.append('file', image);

            const response = await fetch(window.location.origin + (window.base || window.parent?.base || '') + 'index.php?page=api_users&action=update_image&id=' + id_member.toString(), {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Image non traitée');
            }

            toast('Image mise à jour avec succès.');
        } catch (error) {
            toast(error.message, true);
        }

        hideLoader();
    };

    while (prop_roles.firstChild)
        prop_roles.removeChild(prop_roles.firstChild);

    const roles = await requestGET('/index.php?page=api_roles'); 
    const user_roles = (await requestGET(`/index.php?page=api_userole&id=${id_member}`)).map(role => role.id_role); 
    roles.forEach(role => {
        const button = document.createElement('p');
        button.textContent = role.nom_role;
        button.setAttribute('id', role.id_role);
        prop_roles.appendChild(button);

        if(user_roles.includes(role.id_role))
            button.classList.add('selected');

        button.onclick = async ()=>{
            if(button.classList.contains('selected')){
                button.classList.remove('selected');
            } else {
                button.classList.add('selected');
            }
        };
    });

    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async ()=>{
    showLoader();
    try {
        const result = await requestPOST('/index.php?page=api_users');
        refreshNavbar(fetchData, selectUser, result.id_membre);
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectUser);