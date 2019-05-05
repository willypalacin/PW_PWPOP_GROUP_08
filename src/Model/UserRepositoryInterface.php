<?php


namespace SallePW\SlimApp\Model;


interface UserRepositoryInterface
{
    public function save(User $user);
    public function findUser(User $user);
}