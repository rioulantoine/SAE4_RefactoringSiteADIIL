import { refreshNavbar } from "./navbar.js";
import { requestGET, requestPUT, requestDELETE, requestPOST } from './ajax.js';
import { showLoader, hideLoader } from "./loader.js";
import { toast } from "./toaster.js";
import { showPropertieSkeleton, hidePropertieSkeleton } from "./propertieskeleton.js";
import { openFileDialog } from "./files.js";

showPropertieSkeleton();

const prop_image_grade = document.getElementById('prop_image_grade');
const prop_nom_grade = document.getElementById('prop_nom_grade');
const prop_description_grade_grade = document.getElementById('prop_description_grade_grade');
const prop_prix_grade = document.getElementById('prop_prix_grade');
const prop_reduction_grade = document.getElementById('prop_reduction_grade');
const save_btn = document.getElementById('save_btn');
const delete_btn = document.getElementById('delete_btn');
const new_btn = document.getElementById('new_btn');

async function fetchData() {
    let grades = [];
    try{
        grades = await requestGET('/index.php?page=api_grade');
    } catch (error) {
        toast('Erreur lors du chargement des grades.', true);
    }
    return grades.map(grade => ({label: grade.nom_grade, id: grade.id_grade}));
}

async function saveGrade(id_grade){
    showLoader();
    const data = {
        name: prop_nom_grade.value,
        description: prop_description_grade_grade.value,
        price: prop_prix_grade.value,
        reduction: prop_reduction_grade.value
    };

    try {
        await requestPUT('/index.php?page=api_grade&id=' + id_grade.toString(), data);
        toast('Grade mis à jour avec succès.');
        selectGrade(id_grade);
    } catch (error) {
        toast(error.message, true);
    }
    hideLoader();
}

async function deleteGrade(id_grade){
    showLoader();
    await requestDELETE(`/index.php?page=api_grade&id=${id_grade}`);
    refreshNavbar(fetchData, selectGrade);
    toast('Grade supprimé avec succès.');
}

async function selectGrade(id_grade, li){
    showPropertieSkeleton();
    showLoader();

    const grade = await requestGET(`/index.php?page=api_grade&id=${id_grade}`);
    const defaultImagePath = window.location.origin + (window.base || window.parent?.base || '') + 'public/api/files/grade.webp';

    if (grade.image_grade && grade.image_grade.startsWith('http')) {
        prop_image_grade.src = grade.image_grade;
    } else if (grade.image_grade && grade.image_grade !== "default.png" && grade.image_grade !== "N/A") {
        prop_image_grade.src = window.location.origin + (window.base || window.parent?.base || '') + 'public/api/files/' + grade.image_grade;
    } else {
        prop_image_grade.src = defaultImagePath;
    }

    prop_nom_grade.value = grade.nom_grade;
    prop_description_grade_grade.value = grade.description_grade;
    prop_prix_grade.value = grade.prix_grade;
    prop_reduction_grade.value = grade.reduction_grade;

    save_btn.onclick = ()=>{
        saveGrade(id_grade);
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
                deleteGrade(id_grade);
            }
          });
    };

    prop_nom_grade.onkeyup = ()=>{
        if(li) li.textContent = prop_nom_grade.value;
    };

    document.getElementById('prop_image_edit').onclick = async ()=>{
        const image = await openFileDialog();
        const url = URL.createObjectURL(image);
        prop_image_grade.src = url;

        showLoader();

        try {
            const formData = new FormData();
            formData.append('file', image);

            const response = await fetch(window.location.origin + (window.base || window.parent?.base || '') + 'index.php?page=api_grade&action=update_image&id=' + id_grade.toString(), {
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
        const result = await requestPOST('/index.php?page=api_grade');
        refreshNavbar(fetchData, selectGrade, result.id_grade);
    } catch (error) {
        toast('Erreur lors de la création du grade.', true);
        hideLoader();
    }
};

refreshNavbar(fetchData, selectGrade);