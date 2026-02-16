<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Gestion des Achats</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        form { margin-bottom: 30px; }
        .section { margin-bottom: 50px; }
    </style>
</head>
<body>

<h2>Achat via dons en ARGENT</h2>

<div class="section">
    <h3>Nouvel Achat</h3>

    <form method="POST" action="/achats">

        <label>Ville :</label>
        <select name="ville_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($villes as $v) : ?>
                <option value="<?= $v['id_ville'] ?>">
                    <?= htmlspecialchars($v['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Article :</label>
        <select name="article_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($articles as $a) : ?>
                <?php if ($a['pu'] != 1) : ?>
                    <option value="<?= $a['id_article'] ?>">
                        <?= htmlspecialchars($a['name']) ?> (PU: <?= $a['pu'] ?>)
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <br><br>

        <label>Quantité :</label>
        <input type="number" step="0.01" name="quantite" required>

        <br><br>

        <button type="submit">Valider Achat</button>
    </form>
</div>


<div class="section">
    <h3>Besoins Restants</h3>

    <table>
        <tr>
            <th>Ville</th>
            <th>Article</th>
            <th>Quantité Totale</th>
            <th>Date Saisie</th>
        </tr>

        <?php foreach ($besoins as $b) : ?>
            <tr>
                <td><?= htmlspecialchars($b['ville']) ?></td>
                <td><?= htmlspecialchars($b['article']) ?></td>
                <td><?= $b['quantite'] ?></td>
                <td><?= $b['date_saisie'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>


<div class="section">
    <h3>Liste des Achats</h3>

    <table>
        <tr>
            <th>ID</th>
            <th>Ville</th>
            <th>Total HT</th>
            <th>Total TTC</th>
            <th>Taux Frais (%)</th>
            <th>Date</th>
        </tr>

        <?php foreach ($achats as $a) : ?>
            <tr>
                <td><?= $a['id_achat'] ?></td>
                <td><?= htmlspecialchars($a['ville']) ?></td>
                <td><?= $a['total_ht'] ?></td>
                <td><?= $a['total_ttc'] ?></td>
                <td><?= $a['taux_frais'] ?></td>
                <td><?= $a['date_achat'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

</body>
</html>
