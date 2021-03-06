<?php 

    require_once "Sql.php";
    require_once "Order.php";

    class OrderDAO {

        public function insert($order){
            $sql = new Sql();
            try {

                $result = $sql->select("CALL sp_orders_store(:total, :desk, :status, :waiter, :orderName)", [
                    ":total"=> $order->getTotalPrice(),
                    ":desk"=> $order->getDesk()->getIdDesk(),
                    ":status"=> $order->getStatus()->getIdStatus(),
                    ":waiter"=> (int)$order->getWaiter()->getIdWaiter(),
                    ":orderName"=> strtoupper($order->getOrderName())
                ]);
                $sql->close();
                return $result[0];

            } catch (Exception $e) {
                die($e->getMessage);
            }
        }
        
        public function insertItem($order){

            $sql = new Sql();
            try {
            
                for ($i=0; $i < count($order->getOrderItem()); $i++) { 
                    
                    if($order->getOrderItem()[$i]->getProduct() !== null){

                        $sql->query("CALL sp_ordersItems_store(:qtd, :order, :prod)", [
                            ":qtd" => $order->getOrderItem()[$i]->getQuantity(),
                            ":order" => $order->getIdOrder(),
                            ":prod" => $order->getOrderItem()[$i]->getProduct()->getIdProduct()
                        ]); 

                    } 

                }
                
                $sql->close();

            } catch (Exception $e) {
                die($e->getMessage);
            }
        }

        public function ordersByDesk($desk){

            $sql = new Sql();
            try {
                $result = $sql->select("SELECT * FROM tb_orders INNER JOIN tb_status USING(idStatus) WHERE idDesk = :id", [
                    ":id"=> $desk->getIdDesk()
                ]);
                $sql->close();
                return $result;
            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public function listAllOrders(){

            $sql = new Sql();
            try {
                
                $result = $sql->select("SELECT * FROM tb_orders
                INNER JOIN tb_desk USING(idDesk) ORDER BY dt_register DESC");
                $sql->close();
                return $result;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public function listOrderItems($order){

            $sql = new Sql();
            try {
                $result = $sql->select("
                    SELECT i.idOrderItem 'idOrderItem', p.descriptive 'product', p.price 'price' , 
                    c.descriptive 'category', i.totalPrice 'totalPrice', i.quantity 'quantity'
                    FROM tb_orderItems i
                    INNER JOIN tb_products p USING(idProduct)
                    INNER JOIN tb_categories c USING(idCategory)
                    WHERE idOrder = :id
                    ORDER BY i.idOrderItem ASC;
                ", [":id"=> $order->getIdOrder()]);
                $sql->close();
                return $result;
            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public function finishOrder($order){

            $sql = new Sql();
            try {

                $result = $sql->query("UPDATE tb_orders SET idStatus = 2 WHERE idOrder = :id", [
                    ":id"=> $order->getIdOrder()
                ]);
        
                return $result;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public function deleteOrder($order){

            $sql = new Sql();
            try {

                $result = $sql->query("DELETE FROM tb_orders WHERE idOrder = :id AND idStatus = 1", [
                    ':id'=> $order->getIdOrder()
                ]);

                return $result;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public function deleteOrderItem($order){

            $sql = new Sql();

            try {
                
                $result = $sql->query("CALL sp_orderItems_delete(:id, :qtd, :order)", [
                    ":id"=> $order->getOrderItem()[0]->getIdOrderItem(),
                    ":qtd"=> $order->getOrderItem()[0]->getQuantity(),
                    ":order"=> $order->getIdOrder()
                ]);

                return $result;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public static function verifyOrders($desk){

            $sql = new Sql();
            try {

                $result = $sql->select("SELECT idOrder FROM tb_orders WHERE idDesk = :desk AND idStatus = 1", [
                    ":desk"=> $desk->getIdDesk()
                ]);   

                (count($result) > 0) ? $ret = $result[0] : $ret = false;
                $sql->close();
                return $ret;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public static function countOpenOrders(){

            $sql = new Sql();

            try {

                $result = $sql->select("
                    SELECT COUNT(idOrder) 'total' FROM tb_orders WHERE idStatus = 1
                ");

                if(count($result) > 0){
                    return $result[0];
                } else {
                    return false;
                }

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        public static function countFinishedOrders(){

            $sql = new Sql();

            try {

                $result = $sql->select("
                    SELECT COUNT(idOrder) 'total' FROM tb_orders WHERE idStatus = 2
                ");

                if(count($result) > 0){
                    return $result[0];
                } else {
                    return false;
                }

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        function allFinishedOrders(){

            $sql = new Sql();

            try {

                $result = $sql->select("
                    SELECT * FROM tb_orders INNER JOIN tb_waiters using(idWaiter) 
                    INNER JOIN tb_desk using(idDesk)
                    INNER JOIN tb_users using(idUser)
                    WHERE idStatus = 2
                ");

                if(count($result) > 0){
                    return $result;
                } else {
                    return false;
                }

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

        function searchOrdersByName($order){

            $sql = new Sql();

            try {

                $result = $sql->select("SELECT * FROM tb_orders o
                INNER JOIN tb_desk d USING(idDesk)
                WHERE o.orderName LIKE :search ", [
                    ":search" => "%" . strtoupper($order->getOrderName()) . "%"
                ]);

                return $result;

            } catch (Exception $e) {
                die($e->getMessage());
            }

        }

    }

?>