<?php
/**
 * Created by PhpStorm.
 * User: MB
 * Date: 2019-04-20
 * Time: 11:04
 */

namespace SallePW\SlimApp\Model;


class ImageProduct
{
    private $productImage;


    public function __construct(string $productImage)
    {
        $this->productImage = $productImage;

    }

    /**
     * @return string
     */
    public function getProductImage(): string
    {
        return $this->productImage;
    }

    /**
     * @param string $productImage
     */
    public function setProductImage(string $productImage)
    {
        $this->productImage = $productImage;
    }

}
