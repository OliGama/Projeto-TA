<?php
require_once "config.php";


// Inicialize a sessão
session_start();

// Verifique se o usuário está logado, se não, redirecione-o para uma página de login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Relátorio</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font: 14px sans-serif;
            text-align: center;
        }
    </style>
</head>

<body>
    <h1 class="my-5">Bem vindo, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b> a pagina de Relatorio.</h1>
    <?php
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    ?>

    <form method="POST" action="">
        <?php
            $data_inicio = "";
            if(isset($dados['data_inicio'])){
                $data_inicio = $dados['data_inicio'];
            }
        ?>
        <?php
            $data_final = "";
            if(isset($dados['data_final'])){
                $data_final = $dados['data_final'];
            }
        ?>
        <label>Data de Inicio: </label>
        <input type="date" name="data_inicio" value="<?php echo $data_inicio; ?>"><br><br>

        <label>Data Final: </label>
        <input type="date" name="data_final" value="<?php echo $data_final; ?>"><br><br>

        <input type="submit" value="Pesquisar" name="PesqEntreData">
    </form>
    <?php
        $otimo = 0;
        $ruim = 0;
        $razoavel = 0;
    ?>
    <div class="m-4 border border-dark">
        <table class="table">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Avaliação</th>
                    <th scope="col">Data</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($dados["PesqEntreData"])) {
                        $querry_avaliacaos = "SELECT id, nome, created_at FROM avaliacao WHERE created_at BETWEEN :data_inicio AND :data_final ";
                        $result_avaliacaos = $pdo->prepare($querry_avaliacaos);
                        $result_avaliacaos->bindParam(':data_inicio', $dados['data_inicio']);
                        $result_avaliacaos->bindParam(':data_final', $dados['data_final']);
                        $result_avaliacaos->execute();

                        while($row_avalicao = $result_avaliacaos->fetch(PDO::FETCH_ASSOC)){
                            if($row_avalicao['nome'] == 'Otimo'){
                                $otimo += 1;
                            }elseif($row_avalicao['nome'] == 'Ruim'){
                                $ruim += 1;
                            }elseif($row_avalicao['nome'] == 'Razoável'){
                                $razoavel += 1;
                            }
                            echo "<tr>";
                                echo "<td>".$row_avalicao['id']."</td>";
                                echo "<td>".$row_avalicao['nome']."</td>";
                                echo "<td>".date('d/m/Y H:i:s', strtotime($row_avalicao['created_at']))."</td>";
                            echo "</tr>";
                        }
                    }
                ?>
            </tbody>
        </table>
    </div>
    <?php
        echo "Nesse Periodo teve de Avaliações" . "<br>";
        echo "Otimos: " . $otimo . " Razoáveis: " . $razoavel . " Ruim: " . $ruim;
    ?>
    <div class="mt-5 ">
        <a href="logout.php" class="btn btn-danger ml-3">Sair da conta</a>
    </div>

</body>

</html>