<?php

namespace model;

require_once __DIR__ . '/BaseModel.php';

use finfo;
use JsonSerializable;
use tools;

class File implements JsonSerializable
{
    private string $fileName;

    public function getFileName(): string
    {
        return $this->fileName;
    }

    private function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public static function getFile(string | null $fileName): File | null
    {
        if (!is_null($fileName) && file_exists('public/api/files/' . $fileName)) {
            return new File($fileName);
        }

        return null;
    }

    public static function saveFile(): File | null
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'POST') {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                return null;
            }

            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $name = tools::generateUUID() . '.' . $extension;

            if (move_uploaded_file($_FILES['file']['tmp_name'], 'public/api/files/' . $name)) {
                chmod('public/api/files/' . $name, 0644);
                return new File($name);
            }
            return null;
        }

        if ($method === 'PUT' || $method === 'PATCH') {
            $putData = fopen('php://input', 'r');
            $tempFile = tempnam(sys_get_temp_dir(), 'upload_');
            chmod($tempFile, 0644);
            $tempHandle = fopen($tempFile, 'w');
            
            stream_copy_to_stream($putData, $tempHandle);
            
            fclose($putData);
            fclose($tempHandle);

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($tempFile);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'image/gif' => 'gif',
                'application/pdf' => 'pdf',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                'application/vnd.ms-excel' => 'xls',
            ];

            $extension = $extensions[$mimeType] ?? 'bin';
            $name = tools::generateUUID() . '.' . $extension;

            if (rename($tempFile, 'public/api/files/' . $name)) {
                return new File($name);
            }

            @unlink($tempFile);
            return null;
        }

        return null;
    }

    public static function saveImage(): File | null
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif'
        ];

        $isRaw = false;

        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $tmpFile = $_FILES['file']['tmp_name'];
        } else {
            $rawData = file_get_contents('php://input');
            if (empty($rawData)) {
                return null;
            }
            $tmpFile = tempnam(sys_get_temp_dir(), 'upload_');
            file_put_contents($tmpFile, $rawData);
            $isRaw = true;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpFile);

        if (!in_array($mimeType, $allowedTypes)) {
            if ($isRaw) @unlink($tmpFile);
            return null;
        }

        $extension = $extensions[$mimeType];
        $name = tools::generateUUID() . '.' . $extension;

        if (!is_dir('public/api/files')) {
            mkdir('public/api/files', 0777, true);
        }

        if ($isRaw) {
            if (rename($tmpFile, 'public/api/files/' . $name)) {
                chmod('public/api/files/' . $name, 0644);
                return new File($name);
            }
        } else {
            if (move_uploaded_file($tmpFile, 'public/api/files/' . $name)) {
                chmod('public/api/files/' . $name, 0644);
                return new File($name);
            }
        }

        if ($isRaw) @unlink($tmpFile);
        return null;
    }

    public function deleteFile() : bool
    {
        if (file_exists('public/api/files/' . $this->fileName)) {
            unlink('public/api/files/' . $this->fileName);
            return true;
        }

        return false;
    }

    public function __toString() : string
    {
        return $this->fileName;
    }

    public function jsonSerialize(): string
    {
        return $this->fileName;
    }
}