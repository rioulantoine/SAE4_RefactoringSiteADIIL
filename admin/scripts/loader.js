// Create loader and add it to the body
let loader = document.createElement('div');
loader.classList = "loader-container";
loader.innerHTML = `<div class="loader"></div>`;
document.body.appendChild(loader);

/**
 * Shows the loader.
 */
export function showLoader(){
    loader.style.display = 'flex';
    setTimeout(() => {
        loader.classList.add('showed');        
    }, 1);
}

/**
 * Hides the loader.
 */
export function hideLoader(){
    loader.classList.remove('showed');
    setTimeout(() => {
        if (!loader.classList.contains('showed'))
            loader.style.display = 'none';
    }, 200);
}