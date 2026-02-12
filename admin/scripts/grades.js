import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPATCH, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { getFullFilepath, openFileDialog } from "./files.js";

// Show skeleton
showPropertieSkeleton();

// Get inputs
const prop_image_grade = document.getElementById('prop_image_grade');
const prop_nom_grade = document.getElementById('prop_nom_grade');
const prop_description_grade_grade = document.getElementById('prop_description_grade_grade');
const prop_prix_grade = document.getElementById('prop_prix_grade');
const prop_reduction_grade = document.getElementById('prop_reduction_grade');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

/**
 * Reloads the navigation bar with grade items.
 */
async function fetchData() {

    // Fetch data
    let grades = [];
    try{
        grades = await requestGET('/grade.php');
    } catch (error) {
        toast('Erreur lors du chargement des grades.', true);
    }

    // Transform data to navbar items
    return grades.map(grade => ({label: grade.nom_grade, id: grade.id_grade}));

}

/**
 * Saves the grade information.
 *
 * @param {number} id_grade - The ID of the grade to be saved.
 * @returns {Promise<void>} A promise that resolves when the grade is successfully saved.
 * @throws Will alert an error message if the request fails.
 */
async function saveGrade(id_grade){

    // Show loader
    showLoader();

    // Create data
    const data = {
        name: prop_nom_grade.value,
        description: prop_description_grade_grade.value,
        price: prop_prix_grade.value,
        reduction: prop_reduction_grade.value
    };

    // Send data
    try {
        await requestPUT('/grade.php?id=' + id_grade.toString(), data);
        toast('Grade mis à jour avec succès.');
        selectGrade(id_grade);
    } catch (error) {
        toast(error.message, true);
    }

    // Stop loader
    hideLoader();

}

/**
 * Deletes the grade from the DB.
*/
async function deleteGrade(id_grade){

    // Show loader
    showLoader();

    // Send request
    await requestDELETE(`/grade.php?id=${id_grade}`);
    
    /// Update navbar
    refreshNavbar(fetchData, selectGrade);

    // Deleted message
    toast('Grade supprimé avec succès.');

}

/**
 * Loads and displays grade information based on the provided grade ID.
 *
 * @param {number} id_grade - The ID of the grade to be selected.
 * @returns {Promise<void>} A promise that resolves when the grade information has been fetched and displayed.
 */
async function selectGrade(id_grade, li){

    // Show skeleton
    showPropertieSkeleton();

    // Show loader
    showLoader();

    // Fetch grade information
    const grade = await requestGET(`/grade.php?id=${id_grade}`);

    // Update displayed information
    prop_image_grade.src = await getFullFilepath(grade.image_grade, '../ressources/default_images/grade.webp');
    prop_nom_grade.value = grade.nom_grade;
    prop_description_grade_grade.value = grade.description_grade;
    prop_prix_grade.value = grade.prix_grade;
    prop_reduction_grade.value = grade.reduction_grade;

    // Update save button
    save_btn.onclick = ()=>{
        saveGrade(id_grade);
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
                deleteGrade(id_grade);
            }
          });
    };

    // Update name
    prop_nom_grade.onkeyup = ()=>{
        li.textContent = prop_nom_grade.value;
    };

    // Update image
    document.getElementById('prop_image_edit').onclick = async ()=>{
        
        // Get file form
        const image = await openFileDialog();

        // Update image src
        const url = URL.createObjectURL(image);
        prop_image_grade.src = url;

        // Show loader
        showLoader();

        // Send data
        try {
            await requestPATCH('/grade.php?id=' + id_grade.toString(), image);
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

// Handle new grade
new_btn.onclick = async ()=>{

    // Show loader
    showLoader();

    // Create new grade
    try {
        const id = await requestPOST('/grade.php');
        refreshNavbar(fetchData, selectGrade, id);
    } catch (error) {
        toast('Erreur lors de la création du grade.', true);
        hideLoader();
    }

};

// Load navbar
refreshNavbar(fetchData, selectGrade);