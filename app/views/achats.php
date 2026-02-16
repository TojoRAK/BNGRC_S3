<div class="section">
    <h3>Total Argent Disponible: <?= number_format($totalArgent,2) ?> </h3>
</div>

<div class="section">
    <h3>Effectuer un Achat</h3>
    <form method="POST" action="/achats">
        <label>Ville:</label>
        <select name="ville_id" required>
            <option value="">-- Sélectionner Ville --</option>
            <?php foreach ($villes as $v): ?>
                <option value="<?= $v['id_ville'] ?>"><?= htmlspecialchars($v['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Article:</label>
        <select name="article_id" required>
            <option value="">-- Sélectionner Article --</option>
            <?php foreach ($articles as $a): ?>
                <option value="<?= $a['id_article'] ?>"><?= htmlspecialchars($a['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Quantité:</label>
        <input type="number" step="0.01" name="quantite" required>

        <button type="submit">Acheter</button>
    </form>
</div>

<div class="section">
    <h3>Besoins Restants par Ville</h3>
    <table>
        <tr>
            <th>Ville</th>
            <th>Article</th>
            <th>Besoin Initial</th>
            <th>Quantité Déjà Achetée</th>
            <th>Restant</th>
        </tr>
        <?php foreach ($besoins as $b): ?>
            <tr>
                <td><?= htmlspecialchars($b['ville']) ?></td>
                <td><?= htmlspecialchars($b['article']) ?></td>
                <td><?= $b['besoin_initial'] ?></td>
                <td><?= $b['quantite_achetee'] ?></td>
                <td><?= $b['restant'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="section">
    <h3>Historique des Achats</h3>
    <table>
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
