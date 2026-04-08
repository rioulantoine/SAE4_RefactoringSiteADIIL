import { refreshNavbar } from "./navbar.js";
import { requestGET, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { openFileDialog } from "./files.js";

showPropertieSkeleton();

const download_btn = document.getElementById('download_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');
const pdf_preview = document.getElementById('pdf_preview');

async function fetchData() {
    let meetings = [];
    try{
        meetings = await requestGET('/index.php?page=api_meeting');
    } catch (error) {
        toast(error.message, true);
    }
    return meetings.map(m => ({label: m.date_reunion.split(' ')[0], id: m.id_reunion}));
}

async function deleteReunion(id_reunion){
    showLoader();
    try {
        await requestDELETE(`/index.php?page=api_meeting&id=${id_reunion}`);
        refreshNavbar(fetchData, selectReunion);
        toast('Réunion supprimée avec succès.');
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function selectReunion(id_reunion, li){
    showPropertieSkeleton();
    showLoader();

    try {
        const meeting = await requestGET(`/index.php?page=api_meeting&id=${id_reunion}`);
        
        const fileUrl = window.location.origin + (window.base || window.parent?.base || '') + 'public/api/files/' + meeting.fichier_reunion;
        pdf_preview.src = fileUrl;

        download_btn.onclick = ()=>{
            window.open(fileUrl, '_blank');
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
                    deleteReunion(id_reunion);
                }
              });
        };
    } catch (error) {
        toast("Erreur lors de la sélection");
    }

    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async ()=>{
    const file = await openFileDialog("application/pdf");
    showLoader();

    const form_data = new FormData();
    form_data.append('file', file);
    form_data.append('date', new Date().toISOString().split('T')[0]);

    try{
        const result = await requestPOST('/index.php?page=api_meeting', form_data);
        refreshNavbar(fetchData, selectReunion, result.id_reunion);
        toast("Fichier uploadé avec succès");
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }
}

refreshNavbar(fetchData, selectReunion);