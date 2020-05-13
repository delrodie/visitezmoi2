<?php


namespace App\Utilities;


use App\Repository\CategorieRepository;
use App\Repository\DomaineRepository;
use Doctrine\ORM\EntityManagerInterface;

class Utility
{
    private $categoryRepository;
    private $domaineRepository;
    private $em;

    public function __construct(EntityManagerInterface $entityManager, CategorieRepository $categorieRepository, DomaineRepository $domaineRepository)
    {
        $this->em = $entityManager;
        $this->categoryRepository = $categorieRepository;
        $this->domaineRepository = $domaineRepository;
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
}