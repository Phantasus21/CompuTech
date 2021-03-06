<?php
//include '../../model/WarehouseLocation.php';
//include '../dao_purchase/ArticleDAO.php';

Class WarehouseLocationDAO extends AbstractDAO
{

    function __construct() { }

    function getWarehouseLocation($id)
    {
        $this->doConnect();
        $stmt = $this->conn->prepare("SELECT Rack, Position from warehouselocation where ID = ?");
        $stmt->bind_param("i", $id);

        $stmt->execute();

        $rack = "";
        $position = "";
        $stmt->bind_result($rack, $position);

        if($stmt->fetch())
        {
            $warehouseLocation = new WarehouseLocation($id, $rack, $position);
        }

        $this->closeConnect();
        return $warehouseLocation;
    }

    function getWarehouseLocations()
    {
        $this->doConnect();
        $stmt = $this->conn->prepare("SELECT Id, Rack, Position from warehouselocation");

        $stmt->execute();

        $id = 0;
        $rack = "";
        $position = "";
        $stmt->bind_result($id, $rack, $position);

        $warehouseLocations = array();

        while($stmt->fetch())
        {
            $warehouseLocation = new WarehouseLocation($id, $rack, $position);

            array_push($warehouseLocations, $warehouseLocation);
        }

        $this->closeConnect();
        return $warehouseLocations;
    }

    function getWarehouseLocationArticles($warehouseLocationId)
    {
        $this->doConnect();
        $stmt = $this->conn->prepare("SELECT ArticleID, QuantityStored from warehouselocationarticle where warehouseLocationID = ?");
        $stmt->bind_param("i", $warehouseLocationId);

        $stmt->execute();

        $articleID = 0;
        $quantity = 0;
        $stmt->bind_result($articleID, $quantity);

        $articleArray = array();

        while($stmt->fetch())
        {
            $articleGetter = new ArticleDAO();
            $article = $articleGetter->getArticle($articleID);

            $articleArrayEntry = array($article, $quantity);

            array_push($articleArray, $articleArrayEntry);
        }

        $this->closeConnect();
        return $articleArray;
    }

    function removeStock($warehouseLocationID, $articleID, $quantity){
        $this->doConnect();
        $stmt = $this->conn->prepare("SELECT ID, QuantityStored from warehouselocationarticle where warehouseLocationID = coalesce(?, warehouseLocationID) and ArticleID = ? order by QuantityStored desc");
        $stmt->bind_param("ii", $warehouseLocationID, $articleID);

        $stmt->execute();

        $warehouseLocationArticleID = 0;
        $quantityStock = 0;
        $stmt->bind_result($warehouseLocationArticleID, $quantityStock);

        $locationarray = array();
        while($stmt->fetch())
        {
            $locationArrayEntry = array($warehouseLocationArticleID, $quantityStock);

            array_push($locationarray, $locationArrayEntry);
        }

        $i = 0;
        while($quantity > 0){
            $warehouseLocationArticleID = $locationarray[$i][0];
            $quantityStock = $locationarray[$i][1];

            $quantitynew = $quantity - $quantityStock;
            if($quantityStock - $quantity > 0) $quantityStock -= $quantity;
            else $quantityStock = 0;

            $quantity = $quantitynew;

            $stmt2 = $this->conn->prepare("update warehouselocationarticle set quantityStored = ? where ID = ?");
            $stmt2->bind_param("ii", $quantityStock, $warehouseLocationArticleID);

            $stmt2->execute();

            $i++;
        }

        $this->closeConnect();
    }

    function addWarehouseLoation($rack, $position){
        $this->doConnect();

        $stmt = $this->conn->prepare("insert into warehouselocation (rack, position) values (?, ?)");
        $stmt->bind_param('ss', $rack, $position);

        $stmt->execute();
        $this->closeConnect();
    }

    function addStock($warehouseLocationID, $articleID, $quantity){
        $this->doConnect();

        $stmt = $this->conn->prepare("select ID from warehouselocationarticle where warehouseLocationID = ? and articleid = ?");
        $stmt->bind_param('ii', $warehouseLocationID, $articleID);

        $stmt->execute();
        $update = false;

        while($stmt->fetch()) {
            $update = true;
        }

        if($update){

            $stmt2 = $this->conn->prepare("update warehouselocationarticle set quantityStored = quantityStored + ? where warehouseLocationID = ? and articleid = ?");
            $stmt2->bind_param('iii', $quantity, $warehouseLocationID, $articleID);

            $stmt2->execute();
        }else {
            $stmt2 = $this->conn->prepare("insert into warehouselocationarticle (QuantityStored, warehouseLocationID, articleid) values (?, ?, ?)");
            $stmt2->bind_param('iii', $quantity, $warehouseLocationID, $articleID);

            $stmt2->execute();
        }
        $this->closeConnect();
    }

}
