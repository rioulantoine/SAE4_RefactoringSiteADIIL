import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
const prop_image = document.getElementById('prop_image');
const prop_name = document.getElementById('prop_name');
const prop_date = document.getElementById('prop_date');
const prop_content = document.getElementById('prop_content');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

/**
 * Reloads the navigation bar with news items.
 */
async function fetchData() {

    // Fetch data
    let actualites = [];
    try{
        actualites = await requestGET('/news.php');
    } catch (error) {
        toast('Erreur lors du chargement des actualites.', true);
    }

    // Transform data to navbar items
    return actualites.map(actualite => ({label: actualite.titre_actualite, id: actualite.id_actualite}));

}

/**
 * Saves the news information.
 *
 * @param {number} id_news - The ID of the news to be saved.
 * @returns {Promise<void>} A promise that resolves when the news is successfully saved.
 */
async function saveNews(id_news){

    // Show loader
    showLoader();

    // Create data
    const data = {
        name: prop_name.value,
        description: prop_content.value,
        date: prop_date.value
    };

    // Send data
    try {
        await requestPUT('/news.php?id=' + id_news.toString(), data);
        toast('Actualité mis à jour avec succès.');
        selectNews(id_news);
    } catch (error) {
        toast(error.message, true);
    }

    // Stop loader
    hideLoader();

}

/**
 * Deletes the news from the DB.
*/
async function deleteNews(id_news){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/news.php?id=${id_news}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectNews);

    // Deleted message
    toast('Actualité supprimé avec succès.');

}

/**
 * Loads and displays event information based on the provided news ID.
 *
 * @param {number} id_news - The ID of the news to be selected.
 * @returns {Promise<void>} A promise that resolves when the news information has been fetched and displayed.
 */
async function selectNews(id_news, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();

    // Fetch news information
    const news = await requestGET(`/news.php?id=${id_news}`);

    // Update displayed information
    if (news.image_actualite) {
        prop_image.src = await getFullFilepath(news.image_actualite, 'none');
    } else {
        prop_image.hidden = true;
    }
    prop_name.value = news.titre_actualite;
    prop_date.value = news.date_actualite.split(" ")[0];
    prop_content.value = news.contenu_actualite;

    // Update save button
    save_btn.onclick = ()=>{
        saveNews(id_news);
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
                deleteNews(id_news);
            }
          });
    };

    // Update name
    prop_name.onkeyup = ()=>{
        li.textContent = prop_name.value;
    };

    // Update image
    document.getElementById('prop_img_edit').onclick = async ()=>{
        
        // Get file form
        const image = await openFileDialog();

        // Update image src
        const url = URL.createObjectURL(image);
        prop_image.src = url;

        // Show loader
        showLoader();

        // Send data
        try {
            await requestPATCH('/news.php?id=' + id_news.toString(), image);
            toast('Image mis à jour avec succès.');
        } catch (error) {
            toast(error.message, true);
        }

        // Stop loader
        hideLoader();

    };

    // Hide loader
    hideLoader();

    // Hide skeleton
    hidePropertieSkeleton();
    
}

// Handle new news
new_btn.onclick = async ()=>{

    // Show loader
    showLoader();

    // Create new news
    try {
        const { id_actualite } = await requestPOST('/news.php');
        refreshNavbar(fetchData, selectNews, id_actualite);
    } catch (error) {
        toast("Erreur lors de la création de l'actualtié", true);
        hideLoader();
    }

};

// Load navbar
refreshNavbar(fetchData, selectNews);