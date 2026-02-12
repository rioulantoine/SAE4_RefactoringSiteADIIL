/**
 * Import the loader
 */
import { showLoader, hideLoader } from "./loader.js";

/**
 * Selects the navbar element from the DOM.
 */
const navbar = document.getElementById('content_navbar');
const skeleton = document.getElementById('skeleton_navbar');
const empty_navbar = document.getElementById('empty_navbar');
const main = document.querySelector('main');

/**
 * Refreshes the navbar by fetching new data and updating the items.
 *
 * @param {Function} fetchData - An async function that fetches the data for the navbar items.
 * @param {Function} selectItem - A function that handles the selection of a navbar item.
 * @param {string|null} [defaultSelectedItem=null] - The ID of the item to be selected by default. If null, the first item will be selected.
 */
export async function refreshNavbar(fetchData, selectItem, defaultSelectedItem = null) {

    // Show loader
    showLoader();

    // Clear navbar
    navbar.innerHTML = '';

    // Hide empty navbar
    empty_navbar.hidden = true;

    // Show fetching skeleton
    skeleton.hidden = false;

    // Fetch data
    const items = await fetchData();

    // Set default select item to the first of items if not defined
    if (defaultSelectedItem === null && items.length > 0)
        defaultSelectedItem = items[0].id;

    // Hide fetching skeleton
    skeleton.hidden = true;

    // Add items to the navbar
    let needToBeSelectedLi = null;
    for (const item of items) {
        addNavbarItem(item.label, li => selectItem(item.id, li));
        if (item.id === defaultSelectedItem)
            needToBeSelectedLi = navbar.lastChild;
    }

    // Shows empty navbar if no items
    if (items.length === 0)
        empty_navbar.hidden = false;

    // Deselect
    for (const item of navbar.children)
        item.classList.remove('active');

    // Select default item
    if (needToBeSelectedLi !== null)
        needToBeSelectedLi.click();

    // If empty
    if (items.length === 0)
        main.style = 'display: none';
    else
        main.style = '';

    // Hide loader
    hideLoader();

}

/**
 * Adds an item to the navbar.
 *
 * @param {string} label - The label of the item.
 * @param {Function(HTMLElement): void} onClick - The callback function to be called when the item is clicked.
 */
function addNavbarItem(label, onClick){

    // Create item
    let li = document.createElement('li');
    li.textContent = label;

    // Add event listener
    li.onclick = () => {

        // Remove active class from all items
        for (const item of navbar.children)
            item.classList.remove('active');

        // Add active class to the clicked item
        li.classList.add('active');

        // Call the callback function
        onClick(li);
    
    }

    // Add item to the navbar
    navbar.appendChild(li);

}