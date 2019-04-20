<?php
/**
 * Created by PhpStorm.
 * User: MB
 * Date: 2019-04-20
 * Time: 11:04
 */

namespace SallePW\SlimApp\Model;


class Product
{

    private $title;
    private $description;
    private $price;
    private $productImages;
    private $category;


   public function __construct(string $title, string $description, string $price, array $productImages, string $category) {
       $this -> title = $title;
       $this -> description = $description;
       $this -> price = $price;
       $this -> productImages = $productImages;
       $this -> category = $category;
   }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getProductImages()
    {
        return $this->productImages;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @param array $productImages
     */
    public function setProductImages($productImages)
    {
        $this->productImages = $productImages;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

}