<?php

    //Classe Dashboard
    class Dashboard {
        public $data_inicio;
        public $data_fim;
        public $numeroVendas;
        public $totalVendas;
        public $totalDespesas;
        public $clientesAtivos;
        public $clientesInativos;
        public $totalReclamaoes;
        public $totalElogios;
        public $totalSugestoes;

        public function __get($atributo){
            return $this->$atributo;
        }

        public function __set($atributo, $valor){
            $this->$atributo = $valor;
            return $this;
        }
    }

    //Classe de Conexão com o BD
    class Conexao  
    {
        private $host = 'localhost';
        private $dbname = 'dashboard';
        private $user = 'root';
        private $password = '';

        public function conectar(){
            try {
                $conexao = new PDO(
                    "mysql:host=$this->host;dbname=$this->dbname",
                    "$this->user",
                    "$this->password"
                );

                $conexao->exec('set charset set utf8');

                return $conexao;
            } catch (PDOException $e) {
                echo '<p>' . $e->getMessege(). '</p>';
            }
        }
    }

    //Classe (model)

    class Bd  {
        private $conexao;
        private $dashboard;

        public function __construct(Conexao $conexao, Dashboard $dashboard){
            $this->conexao = $conexao->conectar();
            $this->dashboard = $dashboard;
        }

        public function getNumeroVendas(){
            $query = 'SELECT COUNT(*) AS numero_vendas FROM tb_vendas WHERE data_venda BETWEEN :data_inicio AND :data_fim ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
        }

        public function getTotalVendas(){
            $query = 'SELECT SUM(total) AS total_vendas FROM tb_vendas WHERE data_venda BETWEEN :data_inicio AND :data_fim ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
        }

        public function getTotalDespesas(){
            $query = 'SELECT SUM(total) AS total_despesas FROM tb_despesas WHERE data_despesa BETWEEN :data_inicio AND :data_fim ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
            $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_despesas;
        }

        public function getClienteAtivo(){
            $query = 'SELECT COUNT(*) AS total_clientes_ativos FROM tb_clientes WHERE cliente_ativo = :valor ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', 1);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_ativos;
        }

        public function getClienteInativo(){
            $query = 'SELECT COUNT(*) AS total_clientes_inativos FROM tb_clientes WHERE cliente_ativo = :valor ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', 0);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_clientes_inativos;
        }

        public function getTotalReclamacoes(){
            $query = 'SELECT COUNT(*) AS total_reclamacoes FROM tb_contatos WHERE tipo_contato = :valor ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', 1);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacoes;
        }

        public function getTotalElogios(){
            $query = 'SELECT COUNT(*) AS total_elogios FROM tb_contatos WHERE tipo_contato = :valor ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', 2);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_elogios;
        }

        public function getTotalSugestoes(){
            $query = 'SELECT COUNT(*) AS total_sugestoes FROM tb_contatos WHERE tipo_contato = :valor ';

            $stmt = $this->conexao->prepare($query);
            $stmt->bindValue(':valor', 3);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestoes;
        }
    }

    $dashboard = new Dashboard();
    $conexao = new Conexao();

    if(array_key_exists('competencia', $_GET)){
        $competencia = explode('-', $_GET['competencia']);
        $ano = $competencia[0];
        $mes = $competencia[1];
        $dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

        $dashboard->__set('data_inicio', "$ano-$mes-01");
        $dashboard->__set('data_fim', "$ano-$mes-$dias_do_mes");

        $bd = new Bd($conexao, $dashboard);
        
        $dashboard->__set('numeroVendas', $bd->getNumeroVendas());
        $dashboard->__set('totalVendas', $bd->getTotalVendas());
        $dashboard->__set('totalDespesas', $bd->getTotalDespesas());

        echo json_encode($dashboard);
    } else {
        $bd = new Bd($conexao, $dashboard);
        $dashboard->__set('clientesAtivos', $bd->getClienteAtivo());
        $dashboard->__set('clientesInativos', $bd->getClienteInativo());
        $dashboard->__set('totalReclamaoes', $bd->getTotalReclamacoes());
        $dashboard->__set('totalElogios', $bd->getTotalElogios());
        $dashboard->__set('totalSugestoes', $bd->getTotalSugestoes());
        echo json_encode($dashboard);
    }

   
?>