<?php

namespace SallePW\SlimApp\Controller;

use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class FavouritesController
{


    /**
     * HelloController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $repository = $this->container->get('user_repo');
        $id_user = $_SESSION["user_id"];
        $id_user = $repository->findUserById($id_user);

        $favourite = $repository->getFavouriteProduct($id_user);



        $images = $repository->getImagesOfProductById();




        return $this->container->get('view')->render($response, 'favourites.twig', [
            'products' => $favourite,

            'images' => $images


        ]);
    }


}