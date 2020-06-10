<?php


namespace App\Utilities;


use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionMedia
{
    private $mediaPartenaire;
    private $mediaUpload;
    private $mediaBien;
    private $mediaSlide;

    public function __construct($partenaireDirectory, $mediaDirectory, $bienDirectory, $slideDirectory)
    {
        $this->mediaPartenaire = $partenaireDirectory;
        $this->mediaUpload = $mediaDirectory;
        $this->mediaBien = $bienDirectory;
        $this->mediaSlide = $slideDirectory;
    }

    /**
     * Enregistrement du media sur le server
     *
     * @param UploadedFile $file
     * @param null $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null)
    {
        $slugify = new Slugify();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slugify($originalFileName);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'logo') $file->move($this->mediaPartenaire, $newFilename);
            elseif ($media === 'media') $file->move($this->mediaBien, $newFilename);
            elseif ($media === 'slide_media') $file->move($this->mediaSlide, $newFilename);
            else $file->move($this->mediaUpload, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;

    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null)
    {
        if ($media === 'photo') unlink($this->mediaPartenaire.'/'.$ancienMedia);
        elseif ($media === 'media') unlink($this->mediaBien.'/'.$ancienMedia);
        elseif ($media === 'slide_media') unlink($this->mediaSlide.'/'.$ancienMedia);
        else return false;

        return true;
    }
}