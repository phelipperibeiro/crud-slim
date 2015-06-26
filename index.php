<?php
require 'vendor/Slim/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

#############################################################################
#ROTAS
#############################################################################

$app->get('/', function () {
echo "SlimProdutos";
});

$app->get('/categorias','getCategorias');

$app->post('/produtos','addProduto');

$app->get('/produtos/:id','getProduto');

$app->post('/produtos/:id','saveProduto');

$app->delete('/produtos/:id','deleteProduto');

$app->get('/produtos','getProdutos');


$app->run();


#############################################################################
#FUNCOES
#############################################################################

function getConn()
{
return new PDO('mysql:host=localhost;dbname=Slim','root','',array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

function getCategorias()
{
$stmt = getConn()->query("SELECT * FROM Categorias");
$categorias = $stmt->fetchAll(PDO::FETCH_OBJ);
echo "{categorias:".json_encode($categorias)."}";
}

function addProduto()
{
$request = \Slim\Slim::getInstance()->request();
$produto = json_decode($request->getBody());
$sql = "INSERT INTO produtos (nome,preco,dataInclusao,idCategoria) values (:nome,:preco,:dataInclusao,:idCategoria) ";
$conn = getConn();
$stmt = $conn->prepare($sql);
$stmt->bindParam("nome",$produto->nome);
$stmt->bindParam("preco",$produto->preco);
$stmt->bindParam("dataInclusao",$produto->dataInclusao);
$stmt->bindParam("idCategoria",$produto->idCategoria);
$stmt->execute();
$produto->id = $conn->lastInsertId();
echo json_encode($produto);
}

function getProduto($id)
{
$conn = getConn();
$sql = " SELECT * FROM PRODUTOS PROD INNER JOIN CATEGORIAS CAT ON (PROD.IDCATEGORIA = CAT.ID) WHERE PROD.ID = :id ";
$stmt = $conn->prepare($sql);
$stmt->bindParam("id",$id);
$stmt->execute();
$produto = $stmt->fetchObject();

echo json_encode($produto);
}

function saveProduto($id)
{
$request = \Slim\Slim::getInstance()->request();
$produto = json_decode($request->getBody());
$sql = "UPDATE produtos SET nome=:nome,preco=:preco,dataInclusao=:dataInclusao,idCategoria=:idCategoria WHERE   id=:id";
$conn = getConn();
$stmt = $conn->prepare($sql);
$stmt->bindParam("nome",$produto->nome);
$stmt->bindParam("preco",$produto->preco);
$stmt->bindParam("dataInclusao",$produto->dataInclusao);
$stmt->bindParam("idCategoria",$produto->idCategoria);
$stmt->bindParam("id",$id);
$stmt->execute();

echo json_encode($produto);

}

function deleteProduto($id)
{
$sql = "DELETE FROM produtos WHERE id=:id";
$conn = getConn();
$stmt = $conn->prepare($sql);
$stmt->bindParam("id",$id);
$stmt->execute();
echo "{'message':'Produto apagado'}";
}

function getProdutos()
{
$sql = "SELECT *,Categorias.nome as nomeCategoria FROM Produtos,Categorias WHERE Categorias.id=Produtos.idCategoria";
$stmt = getConn()->query($sql);
$produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
echo json_encode($produtos);exit;

echo "{\"produtos\":".json_encode($produtos)."}";
}