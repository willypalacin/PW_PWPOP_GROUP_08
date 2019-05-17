<?php
namespace SallePW\SlimApp\Model;
interface UserRepositoryInterface
{
    public function save(User $user);
    public function findUser(User $user);
    public function findUserByUsername(User $user) : bool;
    public function findUserById(string $id) : string;
    public function validateAccount(string $username);
    public function findUserByLoginEmail(string $email, string $pass) : bool;
    public function findUserByLoginUser(string $user, string $pass) : bool;
    public function getUserById(string $id) : User;
}