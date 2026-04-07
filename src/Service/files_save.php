<?php
function generateUUID(){
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return bin2hex($data);
}

function saveFile() : string | null
    {
        // Retourne le nom du fichier si l'enregistrement a réussi, null sinon.

        $name = generateUUID() . '.' . pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

        // Dossier physique: [racine_projet]/public/api/files/
        // __DIR__ = [racine_projet]/src/Service
        $uploadDir = __DIR__ . '/../../public/api/files/';

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                error_log('saveFile: impossible de créer le dossier ' . $uploadDir);
                return null;
            }
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $name)) {
            return $name;
        }

        error_log('saveFile: move_uploaded_file a échoué vers ' . $uploadDir . $name);
        return null;
    }

function saveImage() : string | null
    {
        // Vérification des données de l'image, puis enregistrement.
        // Retourne Faux si l'image n'en est pas une, ou si elle n'a pas pu être enregistrée.

        if (!isset($_FILES['file']) || $_FILES['file']['tmp_name'] === '') {
            error_log('saveImage: aucun fichier reçu dans $_FILES["file"]');
            return null;
        }

        // Vérifie le type MIME avec finfo
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($_FILES['file']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mimeType, $allowedTypes)) {
            error_log('saveImage: type MIME non autorisé: ' . $mimeType);
            return null;
        }

        // On s'assure que l'extension du fichier ne causerait pas de problèmes
        $fileName = saveFile();
        if ($fileName === null) {
            error_log('saveImage: saveFile() a retourné null');
        }
        return $fileName;
    }

function deleteFile(string $fileName) : bool
    {
        $uploadDir = __DIR__ . '/../../public/api/files/';

        if (file_exists($uploadDir . $fileName)) {
            unlink($uploadDir . $fileName);
            return true;
        }

        return false;
    }
?>