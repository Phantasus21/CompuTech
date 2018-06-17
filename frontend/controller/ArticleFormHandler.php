<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once $_SERVER['DOCUMENT_ROOT'].'/CompuTech/frontend/includes.php';

if (!empty($_POST['subNewArticle']) && !empty($_POST['name']) && !empty($_POST['group']) && !empty($_POST['buyPrice']) && !empty($_POST['sellPrice']) && !empty($_POST['unit']) && !empty($_POST['packUnit']) && !empty($_POST['packSize']) && !empty($_POST['minStock']) && !empty($_POST['vendor']) && !empty($_POST['surcharge'])) {

    $info = "";
    $groupDAO = new ArticleGroupDAO;
    $articleGroupID = $groupDAO->getArtikelGroupID($_POST['group']);

    $vendDAO = new SupplierDAO;

    $vendID = $vendDAO->getSupplierIDByName($_POST['vendor']);

    $delete = 1;
    $article = new Article(null, $_POST['name'], $_POST['group'], $_POST['buyPrice'], $_POST['sellPrice'], $_POST['unit'], $_POST['packUnit'], $_POST['packSize'], $_POST['minStock'], $_POST['vendor'], $_POST['surcharge'], $delete);
    
    header("Location: http://localhost/Computech/frontend/?menu=article");
    
} else if (!empty($_POST['modArticle'])) {
    $info = "";
    $db = new ArticleDAO;

    $article = $db->getArticle($_SESSION['articleNum']);

    if (!empty($_POST['name']) && $_POST['name'] != null && $_POST['name'] != "") {
        $desc = $_POST['name'];
    } else {
        $desc = $article->getArticleDesc();
        $info .="Name wurde nicht verändert.";
    }

    if (!empty($_POST['group']) && $_POST['group'] != null && $_POST['group'] != 0) {
        $group = $_POST['group'];
    } else {
        $group = $article->getArticleGroup();
        $info .=" Artikelgruppe wurde nicht verändert.";
    }
    if (!empty($_POST['buyPrice']) && $_POST['buyPrice'] != null && $_POST['buyPrice'] > 0) {
        $buyPrice = $_POST['buyPrice'];
    } else {
        $buyPrice = $article->getBuyingPrice();
        $info .=" Einkaufspreis wurde nicht verändert.";
    }
    if (!empty($_POST['sellPrice']) && $_POST['sellPrice'] != null && $_POST['sellPrice'] > 0) {
        $sellPrice = $_POST['sellPrice'];
    } else {
        $sellPrice = $article->getSellingPrice();
        $info .=" Verkaufspreis wurde nicht verändert.";
    }

    if (!empty($_POST['unit']) && $_POST['unit'] != null && $_POST['unit'] != "") {
        $unit = $_POST['unit'];
    } else {
        $unit = $article->getUnit();
        $info .=" Einheit wurde nicht verändert.";
    }

    if (!empty($_POST['packUnit']) && $_POST['packUnit'] != null && $_POST['packUnit'] != "") {
        $packUnit = $_POST['packUnit'];
    } else {
        $packUnit = $article->getPackingUnit();
        $info .=" Verpackungseinheit wurde nicht verändert.";
    }

    if (!empty($_POST['packSize']) && $_POST['packSize'] != null && $_POST['packSize'] != 0) {
        $packSize = $_POST['packSize'];
    } else {
        $packSize = $article->getPackingSize();
        $info .=" Verpackungsgröße wurde nicht verändert.";
    }

    if (!empty($_POST['minStock']) && $_POST['minStock'] != null && $_POST['minStock'] > 0) {
        $min = $_POST['minStock'];
    } else {
        $min = $article->getMinimumLevel();
        $info .=" Mindestbestand wurde nicht verändert.";
    }

    if (!empty($_POST['vendor']) && $_POST['vendor'] != null && $_POST['vendor'] != 0) {
        $vendor = $_POST['vendor'];
    } else {
        $vendor = $article->getVendor();
        $info .=" Lieferant wurde nicht verändert.";
    }

    if (!empty($_POST['surcharge']) && $_POST['surcharge'] != null) {
        $surcharge = $_POST['surcharge'];
    } else {
        $surcharge = $article->getSurcharge();
        $info .=" Marge wurde nicht verändert.";
    }
    
    if(isset($_POST['delete'])){
        $delete = 0;
    }else{
        $delete = 1;
    }
    
    $group = utf8_encode($group);
    $article->setArticle($desc, $group, $buyPrice, $sellPrice, $unit, $packUnit, $packSize, $min, $vendor, $surcharge, $delete);
        header("Location: http://localhost/Computech/frontend/?menu=article");
}