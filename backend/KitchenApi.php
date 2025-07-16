<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class KitchenApi extends ResourceController
{
    public function pendingOrders()
    {
        $db = \Config\Database::connect();
        $sql = "SELECT sales.sale_id, sales.sale_time, 
                       GROUP_CONCAT(CONCAT(items.name, ' x', sales_items.quantity_purchased) SEPARATOR ', ') AS order_items
                FROM sales
                JOIN sales_items ON sales.sale_id = sales_items.sale_id
                JOIN items ON items.item_id = sales_items.item_id
                JOIN categories ON categories.id = items.category
                WHERE categories.name = 'FOOD'
                  AND sales.kitchen_status != 'completed'
                GROUP BY sales.sale_id
                ORDER BY sales.sale_time DESC";

        $query = $db->query($sql);
        $orders = $query->getResultArray();

        return $this->respond($orders);
    }

    public function complete($sale_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sales');
        $builder->where('sale_id', $sale_id)
                ->update(['kitchen_status' => 'completed']);

        return $this->respond(['status' => 'ok']);
    }
}
