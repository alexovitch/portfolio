<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImageRepository")
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     *
     * @Assert\File(maxSize="20M", mimeTypes={"application/octet-stream"}, mimeTypesMessage="Please send a valide octet-stream file type", maxSizeMessage="Please send a file smaller than 20Mo")
     */
    private $file;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set alt
     *
     * @param string $alt
     * @return Image
     */
    public function setAlt($alt)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt
     *
     * @return string 
     */
    public function getAlt()
    {
        return $this->alt;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Image
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get file
     *
     * @return
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file=$file;
        return $this;
    }

    public function upload(){
        if (null===$this->file){
            return;
        }
        $name = $this->file->getClientOriginalName();

        $ext  = pathinfo($name, PATHINFO_EXTENSION);
        $nameWExtention = basename($name,'.'.$ext);
        $name = $nameWExtention.'-'.substr(md5(uniqid()),0,6) .'.'. strtolower($ext);

        $this->file->move($this->getUploadRootDir(),$name);
        $this->url = $name;
        $this->alt = $name;

    }
    public function getUploadDir()
    {
        // On retourne le chemin relatif vers l'image pour un navigateur
        return 'image';
    }
    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    public function getMiniDir()
    {
        // On retourne le chemin relatif vers le répertoire mini
        return 'mini';
    }
    protected function getMiniRootDir()
    {
        return __DIR__.'/../../../web/'.$this->getMiniDir();
    }

    public function removeOldFile(){

        if(is_file($this->getUploadRootDir().'/'.$this->url))
            unlink($this->getUploadRootDir().'/'.$this->url);

        if(is_file($this->getMiniRootDir().'/mini-'.$this->url))
            unlink($this->getMiniRootDir().'/mini-'.$this->url);
    }

}
