<?php
namespace app\controller;

use app\base\application;
use app\helper\cliHelper;

class migrateController
{
    public function indexAction()
    {
        cliHelper::e('Preparing table `books`...');
        $query = '
            CREATE TABLE IF NOT EXISTS `books`
            (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255)
            )
        ';
        application::$db->exec($query);

        cliHelper::e('Preparing table `authors`...');
        $query = '
            CREATE TABLE IF NOT EXISTS `authors`
            (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255)
            )
        ';
        application::$db->exec($query);

        cliHelper::e('Preparing table `book_authors`...');
        $query = '
            CREATE TABLE IF NOT EXISTS `book_authors`
            (
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `book_id` INT,
                `author_id` INT
            )
        ';
        application::$db->exec($query);




        cliHelper::e('Inserting data to `books`...');
        $query = '
            INSERT INTO `books` (`id`, `title`)
            VALUES (1, "First book"),
                   (2, "Second book"),
                   (3, "Untitled book")
        ';
        application::$db->exec($query);

        cliHelper::e('Inserting data to `authors`...');
        $query = '
            INSERT INTO `authors` (`id`, `name`)
            VALUES (1, "John Smith"),
                   (2, "Eva Green"),
                   (3, "Garry Oldman")
        ';
        application::$db->exec($query);

        cliHelper::e('Inserting data to `book_authors`...');
        $query = '
            INSERT INTO `book_authors` (`id`, `book_id`, `author_id`)
            VALUES (1, 1, 1),
                   (2, 1, 2),
                   (3, 2, 3)
        ';
        application::$db->exec($query);
    }
}