<h3>Total Argent Disponible: <?= number_format($totalArgent,2) ?> MGA</h3>

<h4>Effectuer un achat</h4>
<form method="POST" action="/achats">
    <select name="ville_id" required>
        <?php foreach ($besoins as $b): ?>
            <option value="<?= $b['id_ville'] ?>"><?= $b['ville'] ?></option>
        <?php endforeach; ?>
    </select>

    <select name="article_id" required>
        <?php foreach ($besoins as $b): ?>
            <option value="<?= $b['id_article'] ?>"><?= $b['article'] ?></option>
        <?php endforeach; ?>
    </select>

    <input type="number" step="0.01" name="quantite" placeholder="Quantité / Montant" required>
    <button type="submit">Acheter</button>
</form>

<h4>Besoins restants</h4>
<table border="1">
    <tr>
        <th>Ville</th>
        <th>Article</th>
        <th>Besoin Initial</th>
        <th>Quantité Déjà Achetée</th>
        <th>Restant</th>
    </tr>
    <?php foreach ($besoins as $b): ?>
        <tr>
            <td><?= $b['ville'] ?></td>
            <td><?= $b['article'] ?></td>
            <td><?= $b['besoin_initial'] ?></td>
            <td><?= $b['quantite_achetee'] ?></td>
            <td><?= $b['restant'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<!-- Historique des achats -->
<div class="section">
    <h3>Historique des Achats</h3>
    <table border="1">
        <tr>
            <th>ID Achat</th>
            <th>Ville</th>
            <th>Article</th>
            <th>Quantité</th>
            <th>Total HT</th>
            <th>Total TTC</th>
            <th>Taux Frais (%)</th>
            <th>Date Achat</th>
        </tr>
        <?php foreach ($achats as $a): ?>
            <tr>
                <td><?= $a['id_achat'] ?></td>
                <td><?= htmlspecialchars($a['ville']) ?></td>
                <td><?= htmlspecialchars($a['article']) ?></td>
                <td><?= $a['quantite_achetee'] ?></td>
                <td><?= $a['total_ht'] ?></td>
                <td><?= $a['total_ttc'] ?></td>
                <td><?= $a['taux_frais'] ?></td>
                <td><?= $a['date_achat'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
