<?php


namespace App\Utilities;


use App\Repository\BienRepository;
use App\Repository\CategorieRepository;
use App\Repository\DomaineRepository;
use Doctrine\ORM\EntityManagerInterface;

class Utility
{
    private $categoryRepository;
    private $domaineRepository;
    private $em;
    private $bienRepository;

    public function __construct(EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, DomaineRepository $domaineRepository, BienRepository $bienRepository)
    {
        $this->em = $entityManager;
        $this->categoryRepository = $categorieRepository;
        $this->domaineRepository = $domaineRepository;
        $this->bienRepository  = $bienRepository;
    }

    /**
     * Augmentation du nombre de catégorie dans le domaine
     *
     * @param $domaineID
     * @return bool
     */
    public function addCategorie($domaineID) :?bool
    {
        $domaine = $this->domaineRepository->findOneBy(['id'=>$domaineID]);
        $nombreCategorie = $domaine->getNombreCategorie() + 1;

        $domaine->setNombreCategorie($nombreCategorie);
        $this->em->flush();

        return true;
    }

    /**
     * Reduction de la quantite du nombre de la categorie
     * 
     * @param $domaineID
     * @return bool
     */
    public function deleteCategorie($domaineID) :?bool
    {
        $domaine = $this->domaineRepository->findOneBy(['id'=>$domaineID]);
        $nombreCategorie = $domaine->getNombreCategorie() - 1;

        $domaine->setNombreCategorie($nombreCategorie);
        $this->em->flush();

        return true;
    }

    /**
     * Augmentation du nombre de produit dans la table bien
     *
     * @param $bienID
     * @return bool|null
     */
    public function addProduit($bienID):?bool
    {
        $bien = $this->bienRepository->findOneBy(['id'=>$bienID]);
        $nombreProduit = $bien->getNombreProduit() + 1;

        $bien->setNombreProduit($nombreProduit);
        $this->em->flush();

        return true;
    }

    /**
     * Reduction du nombre de produit dans la table Bien
     *
     * @param $bienID
     * @return bool|null
     */
    public function deleteProduit($bienID):?bool
    {
        $bien = $this->bienRepository->findOneBy(['id'=>$bienID]);
        $nombreProduit = $bien->getNombreProduit() - 1;

        $bien->setNombreProduit($nombreProduit);
        $this->em->flush();

        return true;
    }

}