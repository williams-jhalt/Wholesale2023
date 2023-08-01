<?php

namespace App\Model;

class ProductImage
{
    private $file;
    private $imageUrl;
    private $originalFilename;

    /**
     * Get the value of file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the value of file
     *
     * @return  self
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the value of imageUrl
     */ 
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Set the value of imageUrl
     *
     * @return  self
     */ 
    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * Get the value of originalFilename
     */ 
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * Set the value of originalFilename
     *
     * @return  self
     */ 
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }
}
