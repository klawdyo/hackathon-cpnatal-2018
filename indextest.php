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
    <script type="text/template" id="new-form">
        <section id="form-data-${index}" class="form-data">
            <input type="hidden" name="field[${index}][id]" id="current-id">
            <div class="input-group">
                <select name="field[${index}][type]" id="type-${index}">
                    <option value="coating">Revestimento</option>
                    <option value="diameter">Diâmetro (pol)</option>
                    <option value="filter">Filtro (mm)</option>
                    <option value="complementary">Anular/Complemento</option>
                    <option value="lythologic">Litologia</option>
                </select>
            </div>
            <div class="input-group">
                <input type="text" name="field[${index}][material_name]" id="material-name-${index}" placeholder="Material">
            </div>
            <div class="input-group">
                <input type="text" name="field[${index}][m_initial]" id="initial-footage-${index}" placeholder="Metragem inicial">
            </div>            
            <div class="input-group">
                <input type="text" name="field[${index}][m_final]" id="end-footage-${index}" placeholder="Metragem final">
            </div>
            <div class="input-group">
                <input type="text" name="field[${index}][diameter]" id="diameter-${index}" placeholder="Diâmetro (pol)">
            </div>
            <div class="input-group">
                <input type="text" name="field[${index}][slot]" id="slot-${index}" placeholder="Ranhura (mm)">
            </div>
            <button type="button" onclick="removeItem(${index})">X</button>
        </section>
    </script>
    <main>
        <form id="form" method="post" action="example1.php" target="iframe">
            <div class="buttons">
                <button type="button" id="add-new">Adicionar</button>
                <button type="submit">Enviar</button>
            </div>
        </form>
        <aside>
            <iframe name="iframe" src="example1.php"></iframe>
        </aside>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.5/lodash.min.js"></script>
    <script src="main.js"></script>
</body>
</html>