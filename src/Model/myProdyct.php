<?php
/**
 * Created by PhpStorm.
 * User: MB
 * Date: 2019-04-20
 * Time: 11:04
 */

namespace SallePW\SlimApp\Model;


class myProduct
{

    private $title;
    private $description;
    private $price;
    private $productImages;
    private $category;
    private $username;


    public function __construct(string $title, string $description, string $price, array $productImages, string $category, string $username) {
        $this -> title = $title;
        $this -> description = $description;
        $this -> price = $price;
        $this -> productImages = $productImages;
        $this -> category = $category;
        $this -> username = $username;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice(string $price)
    {
        $this->price = $price;
    }

    /**
     * @return array
     */
    public function getProductImages(): array
    {
        return $this->productImages;
    }

    /**
     * @param array $productImages
     */
    public function setProductImages(array $productImages)
    {
        $this->productImages = $productImages;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory(string $category)
    {
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }



}