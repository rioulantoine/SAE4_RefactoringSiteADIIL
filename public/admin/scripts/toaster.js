// Create toaster and add it to the body
const toaster = document.createElement('div');
toaster.classList = "toast-container";
const toast_text = document.createElement('p');
toast_text.classList = "toast";
toaster.appendChild(toast_text);
document.body.appendChild(toaster);

/**
 * Show a toast.
 */
export function toast(text, error = false){

    // Display toaster
    toaster.style.display = 'initial';
    
    // Set warning
    if (error){
        toaster.classList.add('error');
    } else {
        toaster.classList.remove('error');
    }

    // Set text
    toast_text.textContent = text;

    // Set hide
    setTimeout(() => {
        hideToast();
    }, 3000);

    // Start toaster animation
    setTimeout(() => {
        toaster.classList.add('showed');
    }, 50);
}

/**
 * Hides the loader.
 */
function hideToast(){
    toaster.classList.remove('showed');
    setTimeout(() => {
        if (toaster.classList.contains('showed')) return;
        toaster.style.display = 'none';
    }, 200);
}