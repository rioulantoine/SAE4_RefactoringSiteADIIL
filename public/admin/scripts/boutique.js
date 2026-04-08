import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";
import { getToggleStatus, updateToggleStatus } from "./toggle.js";

showPropertieSkeleton();

const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_price = document.getElementById('prop_price');
const prop_xp = document.getElementById('prop_xp');
const prop_categorie = document.getElementById('prop_categorie');
const prop_qte = document.getElementById('prop_qte');
const prop_reductions = document.getElementById('prop_reductions');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

async function fetchData() {
    let articles = [];
    try{
        articles = await requestGET('/index.php?page=api_item');
    } catch (error) {
        toast('Erreur lors du chargement des articles.', true);
    }
    return articles.map(article => ({label: article.nom_article, id: article.id_article}));
}

async function saveArticle(id_article){
    showLoader();
    const data = {
        name: prop_name.value,
        xp: prop_xp.value,
        stocks: prop_qte.value,
        price: prop_price.value,
        categorie: prop_categorie.value,
        reduction: getToggleStatus(prop_reductions)
    };

    try {
        await requestPUT('/index.php?page=api_item&id=' + id_article.toString(), data);
        toast('Article mis à jour avec succès.');
        selectArticle(id_article);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteArticle(id_article){
    showLoader();
    await requestDELETE(`/index.php?page=api_item&id=${id_article}`);
    refreshNavbar(fetchData, selectArticle);
    toast('Article supprimé avec succès.');
}

async function selectArticle(id_article, li){
    showPropertieSkeleton();
    showLoader();

    const article = await requestGET(`/index.php?page=api_item&id=${id_article}`);
    const defaultImagePath = window.location.origin + (window.base || window.parent?.base || '') + 'public/admin/ressources/default_images/boutique.png';
    
    if (article.image_article && article.image_article.startsWith('http')) {
        prop_image.src = article.image_article;
    } else if (article.image_article && article.image_article !== "default.png") {
        prop_image.src = window.location.origin + (window.base || window.parent?.base || '') + 'files/' + article.image_article;
    } else {
        prop_image.src = defaultImagePath;
    }

    prop_name.value = article.nom_article;
    prop_xp.value = article.xp_article;
    prop_qte.value = article.stock_article;
    prop_categorie.value = article.categorie_article || "";
    prop_price.value = article.prix_article;
    updateToggleStatus(prop_reductions, article.reduction_article);

    save_btn.onclick = ()=>{
        saveArticle(id_article);
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
                deleteArticle(id_article);
            }
          });
    };

    prop_name.onkeyup = ()=>{
        li.textContent = prop_name.value;
    };

    document.getElementById('prop_image_edit').onclick = async ()=>{
        const image = await openFileDialog();
        const url = URL.createObjectURL(image);
        prop_image.src = url;

        showLoader();

        try {
            const formData = new FormData();
            formData.append('file', image);

            const response = await fetch(window.location.origin + (window.base || window.parent?.base || '') + 'index.php?page=api_item&action=update_image&id=' + id_article.toString(), {
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

    hideLoader();
    hidePropertieSkeleton();
}

new_btn.onclick = async ()=>{
    showLoader();
    try {
        const id = await requestPOST('/index.php?page=api_item');
        refreshNavbar(fetchData, selectArticle, id);
    } catch (error) {
        toast("Erreur lors de la création de l'article", true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectArticle);