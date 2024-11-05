<?php

//TOKEN - token de seguranca para validar e saber que estamos chamando a api
//ACAO - o que vamos fazer?
//ID - ID DO CLIENTE?
//VALOR - Nome do cliente, ou atualizacao do cliente

define('TOKEN', 'fheuifch3789326748923bsduicbdi');

if (isset($_GET['token'])) {
	$token = $_GET['token'];
	if ($token == TOKEN) {
		//PODEMOS CONTINUAR NA API! TEMOS ACESSO

		if (isset($_GET['acao'])) {
			$pdo = new PDO('mysql:host=localhost;dbname=api_curso', 'root', '');
			$acao = $_GET['acao'];

			if ($acao == 'novo_contato') {
				$nome = isset($_GET['nome']) ? $_GET['nome'] : '';

				$sql = $pdo->prepare('INSERT INTO `clientes` VALUES (null,?)');
				if ($sql->execute(array($nome))) {

					die(json_encode(array('sucesso' => true, 'inserido' => $nome)));
				} else {
					die(json_encode(array('sucesso' => false, 'erro' => 'Não foi possível inserir seu contato.')));
				}
			} else if ($acao == 'deletar_contato') {
				if (!isset($_GET['id']))
					die(json_encode(array('erro' => 'Precisamo de um id.')));
				$id = (int)$_GET['id'];

				$pdo->exec("DELETE FROM `clientes` WHERE id = $id");

				die(json_encode(array('sucesso' => true, 'deletado' => $id)));
			} else if ($acao == 'atualizar_contato') {

				if (!isset($_GET['id']))
					die(json_encode(array('erro' => 'Precisamo de um id para retornar um usuário.')));

				$id = (int)$_GET['id'];

				if (!isset($_GET['val']))
					die(json_encode(array("erro" => 'Precisamos do parâmetro valor.')));

				$val = $_GET['val'];

				$sql = $pdo->prepare("UPDATE clientes SET nome = ? WHERE id = ?");
				if (
					$sql->execute(array($val, $id))
				) {
					die(json_encode(array('resposta' => 'O usuário com ID: ' . $id . ' teve o nome atualizado para: ' . $val)));
				}
			} else if ($acao == 'visualizar_contato') {
				if (!isset($_GET['id']))
					die(json_encode(array('erro' => 'Precisamo de um id para retornar um usuário.')));

				$id = (int)$_GET['id'];

				$sql = $pdo->prepare("SELECT * FROM clientes WHERE id = ?");
				$sql->execute(array($id));

				if ($sql->rowCount() >= 1) {
					$dados = $sql->fetch();
					die(json_encode($dados));
				} else {
					die("Não encontramos nenhum usuário com este id.");
				}
			} else {
				die("A acao especificada não é válida em nosso sistema de API.");
			}
		} else {
			die('Você não pode conectar na API sem uma acao definida.');
		}
	} else {
		die('Não foi possível conectar na API seu token está errado.');
	}
} else {
	die('Você precisa especificar um token de segurança.');
}
