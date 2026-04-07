import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
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
    try{
        actualites = await requestGET('/index.php?page=api_news');
    } catch (error) {
        toast('Erreur lors du chargement des actualites.', true);
    }

    return actualites.map(actualite => ({label: actualite.titre_actualite, id: actualite.id_actualite}));

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
        toast('Actualité mis à jour avec succès.');
        selectNews(id_news);
    } catch (error) {
        toast(error.message, true);
    }

    hideLoader();

}

async function deleteNews(id_news){

    showLoader();

    await requestDELETE(`/index.php?page=api_news&id=${id_news}`);
    
    refreshNavbar(fetchData, selectNews);

    toast('Actualité supprimé avec succès.');

}

async function selectNews(id_news, li){

    showPropertieSkeleton();

    showLoader();

    const news = await requestGET(`/index.php?page=api_news&id=${id_news}`);

    if (news.image_actualite) {
        prop_image.src = await getFullFilepath(news.image_actualite, 'none');
    } else {
        prop_image.hidden = true;
    }
    prop_name.value = news.titre_actualite;
    prop_date.value = news.date_actualite.split(" ")[0];
    prop_content.value = news.contenu_actualite;

    save_btn.onclick = ()=>{
        saveNews(id_news);
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
                deleteNews(id_news);
            }
          });
    };

    prop_name.onkeyup = ()=>{
        li.textContent = prop_name.value;
    };

    document.getElementById('prop_img_edit').onclick = async ()=>{
        
        const image = await openFileDialog();

        const url = URL.createObjectURL(image);
        prop_image.src = url;

        showLoader();

        try {
            await requestPATCH('/index.php?page=api_news&id=' + id_news.toString(), image);
            toast('Image mis à jour avec succès.');
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
        const { id_actualite } = await requestPOST('/index.php?page=api_news');
        refreshNavbar(fetchData, selectNews, id_actualite);
    } catch (error) {
        toast("Erreur lors de la création de l'actualtié", true);
        hideLoader();
    }

};

refreshNavbar(fetchData, selectNews);