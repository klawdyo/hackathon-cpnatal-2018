<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Exemplo</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <main>
        <form method="post" action="example1.php" target="iframe">
            <div class="input-group">
                <select name="field[0][type]" id="type">
                    <option value="coating">Revestimento</option>
                    <option value="diameter">Diâmetro (pol)</option>
                    <option value="filter">Filtro (mm)</option>
                    <option value="complementary">Anular/Complemento</option>
                    <option value="lythologic">Litologia</option>
                </select>
            </div>
            <div class="input-group">
                <input type="text" name="field[0][material_name]" id="material-name" placeholder="Material">
            </div>
            <div class="input-group">
                <input type="text" name="field[0][m_initial]" id="initial-footage" placeholder="Metragem inicial">
            </div>            
            <div class="input-group">
                <input type="text" name="field[0][m_final]" id="end-footage" placeholder="Metragem final">
            </div>
            <div class="input-group">
                <input type="text" name="field[0][diameter]" id="diameter" placeholder="Diâmetro (pol)">
            </div>
            <div class="input-group">
                <input type="text" name="field[0][slot]" id="slot" placeholder="Ranhura (mm)">
            </div>
            <button type="submit">Enviar</button>
        </form>
        <aside>
            <iframe name="iframe" src="example1.php"></iframe>
            <!-- <img src="example1.php" alt=""> -->
        </aside>
    </main>

    <script typesrc="main.js">
</body>
</html>