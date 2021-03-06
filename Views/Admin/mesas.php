<?php require_once "header.php"?>

<main class="container py-5">
    <section>
    <a href="principal.php" class="btn btn-outline-danger mb-3"><i class="fas fa-arrow-left mr-2" style="font-size: 10pt;"></i>Voltar</a>
        <h1>Mesas</h1>
        <hr>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Mesas</th>
                    <th scope="col" colspan="2">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                require_once "../../Models/DeskDAO.php";
                $deskDAO = new DeskDAO();
                $retDesk = $deskDAO->allDesks();

                if(count($retDesk) > 0){
                    foreach($retDesk as $index => $desk){
                        echo "<tr><th scope='col' class='align-middle'>".$index."</th>
                        <td class='align-middle'>".$desk->descriptive."</td>";
                        echo "<td class='align-middle'><a href='mesasCRUD.php?oper=a&id={$desk->idDesk}'><i class='fas fa-sync-alt text-info'></i></a></td>";
                ?>
                        <td class='align-middle'><a href = "mesasCRUD.php?oper=e&id=<?php echo $desk->idDesk?>" onclick = 
                        " return confirm('Deseja excluir esta Mesa?')"><i class="fas fa-times fa-1x text-danger"></i></a></td></tr>

                <?php

                    }
                }

                ?>
            </tbody>

        </table>

        <a href="mesasCRUD.php?oper=i" class="btn btn-danger btn-b"><i class="fas fa-plus mr-2"></i>Nova Mesa</a>
    </section>

</main>

<?php require_once "footer.php"?>