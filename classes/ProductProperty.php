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
        $compiled = array();

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
     * ex: 'Операционная система' => array('IOS' => 1, 'Android' => 2)
     * @return array
     */
    public function fetchAll()
    {
        $compiled = array();

        $groups = $this->fetchGroups();
        foreach ($groups as $group) {
            $compiled[ $group['name'] ] = $this->fetchValuesByGroup($group['id']);
        }

        return $compiled;
    }

    /**
     * Возвращает массив из всех IDшников возможных значений атрибутов.
     *
     * @return array
     */
    public function getAllPropertyIds()
    {
        $compiled = array();

        $values = $this->getConnection()->fetchAll("SELECT `id` FROM `product_property_value`");

        foreach ($values as $value) {
            $compiled[] =  $value['id'];
        }

        return $compiled;
    }

    /**
     * Возвращает список IDшников товаров, содержащих все перечисленные свойства.
     *
     * Данный запрос требует дополнительного объяснения.
     * Он выбирает из товаров все те, у которых есть совпадения по переданным аттрибутам,
     * а чтобы отфильтровать товары, у которых совпали не все аттрибуты, использует группировку.
     *
     * При inner join товар появится в запросе столько раз, сколько переданных аттрибутов у него есть.
     * В итоге нам нужно только посчитать сколько раз появился товар при помощи group by,
     * и отфильтровать те товары, которые появились меньшее количество раз, чем количество переданных аттрибутов
     * при помощи having count.
     *
     * @param array $properties Массив из `product_property_value`.`id`
     * @return array Массив ID товаров.
     */
    public function getProductIdsContainingProperties($properties)
    {
        /**
         * @var $stmt Doctrine\DBAL\Driver\PDOStatement
         */
        $stmt = $this->getConnection()->executeQuery(
            "SELECT `product`.`id` FROM `product`
                JOIN `product2property`
                    ON `product2property`.`product_id` = `product`.`id`
                WHERE `product2property`.`property_value_id` IN (?)
                GROUP BY `product`.`id`
                HAVING COUNT(`product`.`id`) = " . count($properties),
            array($properties),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ids  = array();
        foreach ($rows as $row) {
            $ids[] = $row['id'];
        }

        return $ids;
    }

    /**
     * Возвращает список товаров (со всей информацией и свойствами), содержащих все перечисленные свойства.
     *
     * @param array $properties Массив из `product_property_value`.`id`
     * @return array Ассоциативный массив, name + price + properties.
     */
    public function getProductContainingProperties($properties)
    {
        $products = $this->getProductIdsContainingProperties($properties);

        $compiled = array();
        foreach ($products as $product) {
            $compiled[] = $this->getProductInfo((int) $product);
        }

        return $compiled;
    }

    /**
     * Возвращает массив из IDшников свойств товаров, соотвествующих переданным товарам.
     *
     * @param array $productIds
     * @return array
     */
    public function getPropertyIdsByProductIds($productIds)
    {
        $stmt = $this->getConnection()->executeQuery(
            "SELECT DISTINCT `product_property_value`.`id` FROM `product_property_value`
                JOIN `product2property`
                    ON `product2property`.`property_value_id` = `product_property_value`.`id`
                WHERE `product2property`.`product_id` IN (?)",
            array($productIds),
            array(\Doctrine\DBAL\Connection::PARAM_INT_ARRAY)
        );

        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $compiled = array();
        foreach ($properties as $property) {
            $compiled[] = $property['id'];
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

        $compiled = array();
        foreach ($properties as $property) {
            $compiled[ $property['name'] ] = $property['value'];
        }

        return $compiled;
    }
}