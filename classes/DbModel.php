<?php

/**
 * Класс для работы с базой данных.
 *
 * Содержит методы для работы с БД.
 */
abstract class DbModel
{
    /**
     * Подключение к базе.
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

    public function __construct(\Doctrine\DBAL\Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Возвращает соединение с базой данных.
     *
     * @return \Doctrine\DBAL\Connection
     */
    protected function getConnection()
    {
        return $this->connection;
    }
}