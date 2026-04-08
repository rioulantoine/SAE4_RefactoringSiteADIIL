import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";

showPropertieSkeleton();

const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_date = document.getElementById('prop_date');
const prop_content = document.getElementById('prop_content');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

async function fetchData() {
    let actualites = [];
    try {
        // On retire BASE_URL ici car ajax.js s'en occupe
        actualites = await requestGET('/index.php?page=api_news');
    } catch (error) {
        toast('Erreur lors du chargement des actualites.', true);
    }
    return actualites.map(a => ({label: a.titre_actualite, id: a.id_actualite}));
}

async function saveNews(id_news){
    showLoader();
    const data = {
        name: prop_name.value,
        description: prop_content.value,
        date: prop_date.value
    };
    try {
        await requestPUT('/index.php?page=api_news&id=' + id_news.toString(), data);
        toast('Actualité mise à jour avec succès.');
        selectNews(id_news);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteNews(id_news){
    showLoader();
    try {
        await requestDELETE(`/index.php?page=api_news&id=${id_news}`);
        refreshNavbar(fetchData, selectNews);
        toast('Actualité supprimée.');
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function selectNews(id_news, li){
    showPropertieSkeleton();
    showLoader();
    try {
        const news = await requestGET(`/index.php?page=api_news&id=${id_news}`);
        const baseUrl = (window.base || window.parent?.base || '').replace(/\/$/, '');
        const defaultImagePath = window.location.origin + baseUrl + '/public/admin/ressources/default_images/actualite.png';

        prop_image.src = await getFullFilepath(news.image_actualite, defaultImagePath);
        prop_image.hidden = false;
        
        prop_name.value = news.titre_actualite;
        prop_date.value = news.date_actualite ? news.date_actualite.split(" ")[0] : "";
        prop_content.value = news.contenu_actualite;

        save_btn.onclick = () => saveNews(id_news);
        delete_btn.onclick = () => {
            swal({ title: "Sûr ?", text: "Définitif", icon: "warning", buttons: true, dangerMode: true })
            .then((willDelete) => { if (willDelete) deleteNews(id_news); });
        };
        prop_name.onkeyup = () => { if(li) li.textContent = prop_name.value; };

        document.getElementById('prop_img_edit').onclick = async () => {
            const image = await openFileDialog();
            prop_image.src = URL.createObjectURL(image);
            showLoader();
            const formData = new FormData();
            formData.append('file', image);
            try {
                await requestPOST('/index.php?page=api_news&action=update_image&id=' + id_news, formData);
                toast('Image mise à jour.');
            } catch (error) { toast(error.message, true); }
            hideLoader();
        };
    } catch (e) { toast("Erreur sélection"); }
    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async () => {
    showLoader();
    try {
        const res = await requestPOST('/index.php?page=api_news');
        let createdId = null;

        if (Array.isArray(res) && res.length > 0) {
            createdId = res.reduce((highest, item) => {
                return item.id_actualite > highest ? item.id_actualite : highest;
            }, 0);
        } else {
            createdId = res?.id_actualite ?? null;
        }

        refreshNavbar(fetchData, selectNews, createdId);
    } catch (error) {
        toast("Erreur lors de la création", true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectNews);