import { requestGET, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFileBucketUrl, openFileDialog } from "./files.js";

const main_content = document.getElementById('main_content');

async function updateView(){
    showPropertieSkeleton();
    showLoader();

    while (main_content.firstChild)
        main_content.removeChild(main_content.firstChild);

    addUpdloadButton();

    try {
        const data = await requestGET('/SAE4_RefactoringSiteADIIL/index.php?page=api_accounting');
        for (let i = 0; i < data.length; i++) {
            addAccountingElement(data[i]);
        }
    } catch (error) {
        toast("Erreur de chargement");
    }

    hidePropertieSkeleton();
    hideLoader();
}

function addAccountingElement(data){
    const container = document.createElement('div');
    container.className = 'file-element';

    const icon = document.createElement('img');
    icon.src = `public/admin/ressources/sheet.png`;
    container.appendChild(icon);

    const text = document.createElement('div');
    const title = document.createElement('p');
    title.textContent = data.nom_comptabilite;
    text.appendChild(title);
    const date = document.createElement('p');
    date.textContent = data.date_comptabilite;
    text.appendChild(date);
    container.appendChild(text);

    const download_button = document.createElement('button');
    download_button.className = 'btn-transparent btn-blue';
    download_button.innerHTML = `<img src="public/admin/ressources/download.svg">`;
    container.appendChild(download_button);
    
    const delete_button = document.createElement('button');
    delete_button.className = 'btn-transparent btn-red';
    delete_button.innerHTML = `<img src="public/admin/ressources/delete.svg">`;
    container.appendChild(delete_button);

    main_content.appendChild(container);

    download_button.onclick = () => {
        const fileUrl = '/SAE4_RefactoringSiteADIIL/files/' + data.url_comptabilite;
        window.open(fileUrl, '_blank');
    }

    delete_button.onclick = async () => {
        swal({
            title: "Êtes-vous sûr ?",
            text: "Voulez-vous vraiment supprimer ce fichier ?",
            icon: "warning",
            buttons: ["Annuler", "Supprimer"],
            dangerMode: true,
        }).then(async (willDelete) => {
            if (willDelete) {
                try {
                    await requestDELETE(`/SAE4_RefactoringSiteADIIL/index.php?page=api_accounting&id=${data.id_comptabilite}`);
                    toast("Fichier supprimé avec succès.");
                    updateView();
                } catch (error) {
                    toast(error.message, true);
                }
            }
        });
    }
}

function addUpdloadButton(){
    const upload_button = document.createElement('button');
    upload_button.innerHTML = `<img src="public/admin/ressources/download.svg"><p>Upload file</p>`;
    upload_button.className = 'btn-transparent btn-blue upload-button';
    main_content.appendChild(upload_button);

    upload_button.onclick = async () => {
        const file = await openFileDialog();
        swal({
            title: "Entrez le nom a donner",
            text: "Veuillez saisir le nom :",
            content: {
              element: "input",
              attributes: {
                placeholder: "Nom du fichier",
                type: "text"
              },
            },
            buttons: ["Annuler", "Valider"],
          }).then(function (value) {
            if (value) {
                uploadFile(file, value, new Date().toISOString().split('T')[0]);
            }
          });
    }
}

async function uploadFile(file, name, date){
    showLoader();
    const form_data = new FormData();
    form_data.append('file', file);
    form_data.append('nom', name);
    form_data.append('date', date);

    try{
        await fetch('/SAE4_RefactoringSiteADIIL/index.php?page=api_accounting', {
            method: 'POST',
            body: form_data
        });
        toast("Fichier uploadé avec succès");
        updateView();
    } catch (error) {
        toast(error.message, true);
        hideLoader();
    }
}

updateView();