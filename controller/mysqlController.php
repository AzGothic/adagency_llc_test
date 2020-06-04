<?php
namespace app\controller;

use app\base\application;
use app\base\db\SimpleQueryBuilderMySQL;
use app\base\LogicException;
use app\helper\cliHelper;

class mysqlController
{
    public function indexAction()
    {
        cliHelper::e('');

        cliHelper::e('Get Books list:');
        cliHelper::e('$ php app mysql/books');

        cliHelper::e('');

        cliHelper::e('Get Authors list:');
        cliHelper::e('$ php app mysql/authors');

        cliHelper::e('');

        cliHelper::e('Get Books with Authors list:');
        cliHelper::e('$ php app mysql/all');

    }

    /**
     * $ php app mysql/books
     * test MySQL Simple Query Builder
     * @throws LogicException
     */
    public function booksAction()
    {
        $builder = new SimpleQueryBuilderMySQL();
        $builder
            ->from('books')
            ->orderBy('id')
        ;

        cliHelper::e("");

        $countQuery = $builder->buildCount();
        cliHelper::e("Count:\n> " . $countQuery);
        cliHelper::e('Result: ' . application::$db->query($countQuery)->fetchColumn());

        cliHelper::e("");

        $getQuery  = $builder->build();
        cliHelper::e("Build:\n> " . $getQuery);

        $rows = application::$db->query($getQuery);
        cliHelper::e("Result:");
        cliHelper::e('ID | Title');
        foreach ($rows as $row) {
            cliHelper::e(implode(' | ', $row));
        }
    }

    /**
     * $ php app mysql/authors
     * test MySQL Simple Query Builder
     * @throws LogicException
     */
    public function authorsAction()
    {
        $builder = new SimpleQueryBuilderMySQL();
        $builder
            ->from('authors')
            ->orderBy('id')
        ;

        cliHelper::e("");

        $countQuery = $builder->buildCount();
        cliHelper::e("Count:\n> " . $countQuery);
        cliHelper::e('Result: ' . application::$db->query($countQuery)->fetchColumn());

        cliHelper::e("");

        $getQuery  = $builder->build();
        cliHelper::e("Build:\n> " . $getQuery);

        $rows = application::$db->query($getQuery);
        cliHelper::e("Result:");
        cliHelper::e('ID | Name');
        foreach ($rows as $row) {
            cliHelper::e(implode(' | ', $row));
        }
    }

    /**
     * $ php app mysql/all
     * test MySQL Simple Query Builder
     * @throws LogicException
     */
    public function allAction()
    {
        $builder = new SimpleQueryBuilderMySQL();
        $builder
            ->select([
                'book_title' => 'books.title',
                'authors' => 'GROUP_CONCAT(DISTINCT authors.name ORDER BY authors.name ASC SEPARATOR ", ")',
            ])
            ->from(['books', 'authors', 'book_authors'])
            ->where(['book_authors.book_id', 'books.id'])
            ->where(['book_authors.author_id', 'authors.id'])
            ->groupBy('books.id')
        ;

        cliHelper::e("");

        $countQuery = $builder->buildCount();
        cliHelper::e("Count:\n> " . $countQuery);
        cliHelper::e('Result: ' . application::$db->query($countQuery)->fetchColumn());

        cliHelper::e("");

        $getQuery  = $builder->build();
        cliHelper::e("Build:\n> " . $getQuery);

        $rows = application::$db->query($getQuery);
        cliHelper::e("Result:");
        cliHelper::e('Title | Authors');
        foreach ($rows as $row) {
            cliHelper::e(implode(' | ', $row));
        }
    }
}