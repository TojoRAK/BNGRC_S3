<h2>Saisie Besoin</h2>

<form method="POST" action="/besoins">

    <label>Ville :</label>
    <select name="ville_id" required>
        <?php foreach ($villes as $v): ?>
            <option value="<?= $v['id_ville'] ?>">
                <?= htmlspecialchars($v['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Article :</label>
    <select name="article_id" required>
        <?php foreach ($articles as $a): ?>
            <option value="<?= $a['id_article'] ?>">
                <?= htmlspecialchars($a['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Quantité / Montant :</label>
    <input type="number" step="0.01" name="quantite" required>

    <button type="submit">Enregistrer</button>
</form>

<hr>

<h3>Besoin récents</h3>

<table border="1">
    <tr>
        <th>Ville</th>
        <th>Article</th>
        <th>Quantité</th>
        <th>Date</th>
    </tr>

    <?php foreach ($besoins as $b): ?>
        <tr>
            <td><?= $b['ville'] ?></td>
            <td><?= $b['article'] ?></td>
            <td><?= $b['quantite'] ?></td>
            <td><?= $b['date_saisie'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
