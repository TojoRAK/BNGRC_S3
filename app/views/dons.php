<h2>Saisie Don</h2>

<form method="POST" action="/dons">

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

    <label>Date du don :</label>
    <input type="datetime-local" name="date_don" required>

    <label>Source :</label>
    <input type="text" name="source">

    <button type="submit">Enregistrer</button>
</form>

<hr>

<h3>Dons récents</h3>

<table border="1">
    <tr>
        <th>Article</th>
        <th>Quantité</th>
        <th>Date</th>
        <th>Source</th>
        <th>Statut</th>
    </tr>

    <?php foreach ($dons as $d): ?>
        <tr>
            <td><?= $d['article'] ?></td>
            <td><?= $d['quantite'] ?></td>
            <td><?= $d['date_don'] ?></td>
            <td><?= $d['source'] ?></td>
            <td><?= $d['statut'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
