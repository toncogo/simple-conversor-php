<?php 
	date_default_timezone_set('America/Sao_Paulo');

	function buscarCotacaoDolar($date) {
        $url = "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/" .
           "CotacaoDolarDia(dataCotacao=@dataCotacao)?" .
           "@dataCotacao='$date'&\$top=1&\$format=json";

        $response = file_get_contents($url);

        if ($response === false) {
     		throw new Exception('API connect failed.');	
        	return null;
        }	

        $data = json_decode($response, true);

        if(empty($data['value'])) {
     		return null;
        }

        return $data['value'][0];
    }

    // today init
    $timestamp = time();
    $cotacao = null;

 	try {
	 	for ($i = 0; $i < 7; $i++) {
	    	$date = date('m-d-Y', $timestamp);
	    	$cotacao = buscarCotacaoDolar($date);

	    	if ($cotacao !== null) {
	    		break;
	 		}

	 		$timestamp = strtotime('-1 day', $timestamp);
	 	}

	 	if ($cotacao === null) {
	 		throw new Exception('Invalid value.');
	 	}

	 	$num = $_POST['money'] ?? 0;

	 	if(!is_numeric($num)) {
    		throw new Exception('Wrong type in the value information.');
    	}

    	$dolarCompra = $cotacao['cotacaoCompra'];
    	$resultado = $num * $dolarCompra;
 	} catch (Exception $e) {
 		http_response_code(400);
 		echo "Error: " .$e->getMessage();
 		echo "<br><br><a href=\"javascript:history.go(-1)\">Voltar</a>";
 		die();
 	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>Resultado</title>
</head>
<body>
	<main>
		<div>
		<?php
			echo "Valor em dólar: US$ <strong>$resultado</strong>";
 		?>
		</div>
		<button>
			<a 
			href="javascript:history.go(-1)" 
			style="
			color: #ededed;">
			Voltar
			</a>
		</button>
	</main>
</body>
</html>