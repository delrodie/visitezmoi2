<?php


namespace App\Utilities;


use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionMedia
{
    private $mediaPartenaire;
    private $mediaUpload;

    public function __construct($partenaireDirectory, $mediaDirectory)
    {
        $this->mediaPartenaire = $partenaireDirectory;
        $this->mediaUpload = $mediaDirectory;
    }

    public function upload(UploadedFile $file, $media = null)
    {
        $slugify = new Slugify();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slugify($originalFileName);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'logo') $file->move($this->mediaPartenaire, $newFilename);
            else $file->move($this->mediaUpload, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;

    }
}