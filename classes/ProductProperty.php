<?php

require_once BASE_PATH . '/classes/DbModel.php';

/**
 * Модель для работы с таблицами ProductPropertyGroup и ProductPropertyValue.
 */
class ProductProperty extends DbModel
{
    /**
     * Возвращает все записи групп.
     *
     * @return array
     */
    public function fetchGroups()
    {
        return $this->getConnection()->fetchAll("SELECT `id`, `name` FROM `product_property_group`");
    }

    /**
     * Возвращает записи значений для свойст товара по ID группы.
     * Результатом является ассоциативный массив `id` => `value`.
     *
     * @param int $groupId
     * @return array
     */
    public function fetchValuesByGroup($groupId)
    {
        $compiled = [];

        $values = $this->getConnection()->fetchAll(
            "SELECT `id`, `value` FROM `product_property_value` WHERE `group_id` = ?",
            array((int) $groupId)
        );

        foreach ($values as $value) {
            $compiled[ $value['id'] ] = $value['value'];
        }

        return $compiled;
    }

    /**
     * Возвращает массив, в которой ключем является название группы, а значением ассоциативный массив из свойств.
     *
     * ex: 'Операционная система' => array(1 => 'IOS', 2 => 'Android')
     * @return array
     */
    public function fetchAll()
    {
        $compiled = [];

        $groups = $this->fetchGroups();
        foreach ($groups as $group) {
            $compiled[ $group['name'] ] = $this->fetchValuesByGroup($group['id']);
        }

        return $compiled;
    }

    /**
     * Возвращает список товаров, содержащих все перечисленные свойства.
     *
     * @param array $properties Массив из `product_property_value`.`id`
     * @return array Ассоциативный массив, name + price.
     */
    public function getProductContainingProperties($properties)
    {
        /**
         * @var $stmt Doctrine\DBAL\Driver\PDOStatement
         */
        $stmt = $this->getConnection()->executeQuery(
            "SELECT DISTINCT `product`.`id` FROM `product`
                JOIN `product2property`
                    ON `product2property`.`product_id` = `product`.`id`
                WHERE `product2property`.`property_value_id` IN (?)",
            array($properties),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $compiled = [];
        foreach ($products as $product) {
            $compiled[] = $this->getProductInfo($product['id']);
        }

        return $compiled;
    }

    /**
     * Возвращает полную информацию о продукте по ID.
     *
     * @param int $productId
     * @return array array('name' => 'iphone', 'price' => 28000, 'properties' => array('Вес' => 100, 'Цвет' => 'Белый'))
     */
    public function getProductInfo($productId)
    {
        $product = $this->getConnection()->fetchAll(
            "SELECT * FROM `product` WHERE `id` = ? LIMIT 1",
            array((int) $productId)
        );

        $product = current($product);
        $product['properties'] = $this->getProductProperties($productId);
        return $product;
    }

    /**
     * Возвращает все свойства продукта в виде ассоциативного массива.
     *
     * @param int $productId
     * @return array
     */
    public function getProductProperties($productId)
    {
        $properties = $this->getConnection()->fetchAll(
            "SELECT `product_property_group`.`name`, `product_property_value`.`value` FROM `product2property`
                JOIN `product_property_value`
                    ON `product_property_value`.`id` = `product2property`.`property_value_id`
                JOIN `product_property_group`
                    ON `product_property_group`.`id` = `product_property_value`.`group_id`
            WHERE `product2property`.`product_id` = ?",
            array((int) $productId)
        );

        $compiled = [];
        foreach ($properties as $property) {
            $compiled[ $property['name'] ] = $property['value'];
        }

        return $compiled;
    }
}