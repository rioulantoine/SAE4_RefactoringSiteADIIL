/**
 * Retrieves the full file path for a given filename.
 * If the filename is invalid (empty, null, or "N/A"), returns the default file path.
 * If the file does not exist on the server, returns the default file path.
 *
 * @param {string} filename - The name of the file to retrieve the path for.
 * @param {string} defaultFile - The default file path to return if the filename is invalid or the file does not exist.
 * @returns {Promise<string>} The full file path or the default file path.
 */
const BASE_URL = ((window.base || (window.parent && window.parent.base) || '')).replace(/\/$/, '');

export async function getFullFilepath(filename, defaultFile) {
    // Vérifiez si le filename est invalide (vide, null ou "N/A")
    if (!filename || filename === "N/A") {
        return defaultFile;
    }

    const fullFilePath = getFileBucketUrl(filename);
    
    // Si c'est une URL externe (ex: http://files.bdeinfo.fr/...), on la retourne directement.
    // Cela évite que le fetch() ci-dessous échoue à cause des sécurités CORS du navigateur.
    if (fullFilePath.startsWith('http://') || fullFilePath.startsWith('https://')) {
        return fullFilePath;
    }

    try {
        // Utilisation de la méthode HEAD pour juste vérifier l'existence sans télécharger tout le fichier
        const response = await fetch(fullFilePath, { method: 'HEAD' }); 
        if (!response.ok) {
            return defaultFile;
        }
        return fullFilePath;
    } catch {
        return defaultFile;
    }
}

/**
 * Opens a file dialog for image selection and returns a FormData object with the selected file.
 * Only accepts image/jpeg, image/png, and image/webp MIME types.
 *
 * @returns {Promise<File|Blob>} A promise that resolves to a FormData object containing the selected file.
 */
export async function openFileDialog(accept = 'image/*') {
    return new Promise((resolve, reject) => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = accept;

        input.onchange = () => {
            const file = input.files[0];
            if (file) {
                resolve(file);
            } else {
                reject(new Error('No file selected'));
            }
        };

        input.click();
    });
}

/**
 * Retrieves the URL of a file stored in the file bucket.
 * * @param {string} filename - The name of the file to retrieve the URL for.
 * @returns {string} The URL of the file.
 */
export function getFileBucketUrl(filename) {
    if (!filename || filename === "N/A" || filename === "default.png") {
        return "";
    }
    
    if (filename.startsWith('http')) {
        return filename;
    }

    return BASE_URL ? window.location.origin + BASE_URL + 'files/' + filename : window.location.origin + '/files/' + filename;
}