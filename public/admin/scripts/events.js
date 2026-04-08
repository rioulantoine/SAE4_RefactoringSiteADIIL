import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { openFileDialog } from "./files.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

showPropertieSkeleton();

const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_desc = document.getElementById('prop_desc');
const prop_lieu = document.getElementById('prop_lieu');
const prop_xp = document.getElementById('prop_xp');
const prop_date = document.getElementById('prop_date');
const prop_places = document.getElementById('prop_places');
const prop_price = document.getElementById('prop_price');
const prop_reductions = document.getElementById('prop_reductions');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

async function fetchData() {
    let events = [];
    try{
        events = await requestGET('/index.php?page=api_event');
    } catch (error) {
        toast('Erreur lors du chargement des evenements.', true);
    }
    return events.map(event => ({label: event.nom_evenement, id: event.id_evenement}));
}

async function saveEvent(id_event){
    showLoader();
    const data = {
        nom: prop_name.value,
        xp: prop_xp.value,
        places: prop_places.value,
        prix: prop_price.value,
        lieu: prop_lieu.value,
        date: prop_date.value,
        reductions: getToggleStatus(prop_reductions)
    };

    try {
        await requestPUT('/index.php?page=api_event&id=' + id_event.toString(), data);
        toast('Evenement mis à jour avec succès.');
        selectEvent(id_event);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteEvent(id_event){
    showLoader();
    await requestDELETE(`/index.php?page=api_event&id=${id_event}`);
    refreshNavbar(fetchData, selectEvent);
    toast('Evenement supprimé avec succès.');
}

async function selectEvent(id_event, li){
    showPropertieSkeleton();
    showLoader();

    const event = await requestGET(`/index.php?page=api_event&id=${id_event}`);
    const defaultImagePath = window.location.origin + (window.base || window.parent?.base || '') + 'public/admin/ressources/default_images/event.jpg';

    if (event.image_evenement && event.image_evenement.startsWith('http')) {
        prop_image.src = event.image_evenement;
    } else if (event.image_evenement && event.image_evenement !== "default.png") {
        prop_image.src = window.location.origin + (window.base || window.parent?.base || '') + 'public/api/files/' + event.image_evenement;
    } else {
        prop_image.src = defaultImagePath;
    }

    prop_name.value = event.nom_evenement;
    prop_desc.value = event.description_evenement || "";
    prop_xp.value = event.xp_evenement;
    prop_places.value = event.places_evenement;
    prop_price.value = event.prix_evenement;
    updateToggleStatus(prop_reductions, event.reductions_evenement);
    prop_lieu.value = event.lieu_evenement;
    prop_date.value = event.date_evenement ? event.date_evenement.split(' ')[0] : "";

    save_btn.onclick = ()=>{
        saveEvent(id_event);
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
                deleteEvent(id_event);
            }
          });
    };

    prop_name.onkeyup = ()=>{
        if(li) li.textContent = prop_name.value;
    };

    document.getElementById('prop_image_edit').onclick = async ()=>{
        const image = await openFileDialog();
        const url = URL.createObjectURL(image);
        prop_image.src = url;

        showLoader();

        try {
            const formData = new FormData();
            formData.append('file', image);

            const response = await fetch(window.location.origin + (window.base || window.parent?.base || '') + 'index.php?page=api_event&action=update_image&id=' + id_event.toString(), {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Image non traitée');
            }

            toast('Image mise à jour (Fichier sauvegardé).');
        } catch (error) {
            toast(error.message, true);
        }

        hideLoader();
    };

    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async ()=>{
    showLoader();
    try {
        const result = await requestPOST('/index.php?page=api_event');
        refreshNavbar(fetchData, selectEvent, result.id_evenement);
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectEvent);