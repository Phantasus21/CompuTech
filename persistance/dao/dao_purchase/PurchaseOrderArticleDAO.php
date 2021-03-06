<?php

Class PurchaseOrderArticleDAO extends AbstractDAO {

    function __construct() {
        
    }

    function getArticlesFromOrderId($orderId) {
        $this->doConnect();

        $stmt = $this->conn->prepare("select ID, ArticleID, QuantityOrdered, QuantityDelivered, Price, Defective from purchaseorderarticle where OrderId = ?");

        $stmt->bind_param("i", $orderId);

        $stmt->execute();

        $stmt->bind_result($id, $articleId, $quantityOrdered, $quantityDelivered, $price, $defective);

        $orderArticles = array();
        while ($stmt->fetch()) {
            $orderArticle = new PurchaseOrderArticle($id, $articleId, $orderId, $quantityOrdered, $quantityDelivered, $price, $defective);
            array_push($orderArticles, $orderArticle);
        }

        $this->closeConnect();

        return $orderArticles;
    }

    function setPurchaseOrderArticle($id, $articleId, $orderId, $quantityOrdered, $quantityDelivered, $price, $defective) {
        $this->doConnect();

        if ($id == null) {
            $stmt = $this->conn->prepare("insert into purchaseorderarticle (ArticleID, OrderID, QuantityOrdered, QuantityDelivered, Price, Defective) values (?,?,?,?,?,?)");
            $stmt->bind_param("iiiiii", $articleId, $orderId, $quantityOrdered, $quantityDelivered, $price, $defective);
        } else {
            $stmt = $this->conn->prepare("update purchaseorderarticle set ArticleID = ?, OfferID = ?, QuantityOrdered = ?, QuantityDelivered = ?, Price = ?, Defective = ? where ID = ?");
            $stmt->bind_param("iiiiiii", $articleId, $orderId, $quantityOrdered, $quantityDelivered, $price, $defective, $id);
        }

        $stmt->execute();


        if ($id == null && $stmt->fetch()) {
            $id = mysqli_insert_id($stmt);
        }

        $this->closeConnect();
        return $id;
    }

    function setQuantity($id, $quantityOrdered) {
        $this->doConnect();

        $stmt = $this->conn->prepare("update purchaseorderarticle set QuantityOrdered = ? where ID = ?");
        $stmt->bind_param("ii", $quantityOrdered, $id);

        $stmt->execute();

        $this->closeConnect();
        return $id;
    }

}
