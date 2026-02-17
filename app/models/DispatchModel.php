<?php

namespace app\models;

use PDO;

class DispatchModel
{
    private PDO $pdo;

    // Valeurs de statut par defaut, ovaina ra miova ny base
    private const DON_STATUS_NON_DISPATCHE = 'NON_DISPATCHE';
    private const DON_STATUS_PARTIEL       = 'PARTIEL';

    private const BESOIN_STATUS_NON_SATISFAIT = 'non_satisfait';
    private const BESOIN_STATUS_PARTIEL       = 'partiel';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getDonDisponible()
    {
        $sql = "
            SELECT
                d.id_don,
                d.id_article,
                a.name AS article_name,
                tb.name AS type_besoin_name,
                d.quantite,
                d.date_don,
                d.source,
                d.statut AS status
            FROM don d
            JOIN article a ON a.id_article = d.id_article
            JOIN type_besoin tb ON tb.id_type = a.id_type
            WHERE d.statut IN (?, ?)
            ORDER BY d.date_don ASC, d.id_don ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            self::DON_STATUS_NON_DISPATCHE,
            self::DON_STATUS_PARTIEL,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getBesoinOuvert()
    {
        $sql = "
            SELECT
                b.id_besoin,
                b.id_ville,
                v.name AS ville_name,
                b.id_article,
                a.name AS article_name,
                tb.name AS type_besoin_name,
                b.quantite,
                b.date_saisie,
                b.status
            FROM besoin_ville b
            JOIN ville v ON v.id_ville = b.id_ville
            JOIN article a ON a.id_article = b.id_article
            JOIN type_besoin tb ON tb.id_type = a.id_type
            WHERE b.status IN (?, ?)
            ORDER BY b.date_saisie ASC, b.id_besoin ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            self::BESOIN_STATUS_NON_SATISFAIT,
            self::BESOIN_STATUS_PARTIEL,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function simulateDispatch()
    {
        $dons = $this->getDonDisponible();
        $besoins = $this->getBesoinOuvert();

        $donRemaining = [];
        foreach ($dons as $d) {
            $donRemaining[(int)$d['id_don']] = (float)$d['quantite'];
        }

        $besoinRemaining = [];
        foreach ($besoins as $b) {
            $besoinRemaining[(int)$b['id_besoin']] = (float)$b['quantite'];
        }

        $allocations = [];
        $nbAlloc = 0;

        foreach ($dons as $don) {
            $idDon = (int)$don['id_don'];
            $idArticle = (int)$don['id_article'];

            $resteDon = $donRemaining[$idDon] ?? 0.0;
            if ($resteDon <= 0) {
                continue;
            }

            foreach ($besoins as $besoin) {
                if ($resteDon <= 0) {
                    break;
                }

                $idBesoin = (int)$besoin['id_besoin'];
                $idArticleBesoin = (int)$besoin['id_article'];

                if ($idArticleBesoin !== $idArticle) {
                    continue;
                }

                $resteBesoin = $besoinRemaining[$idBesoin] ?? 0.0;
                if ($resteBesoin <= 0) {
                    continue;
                }

                $attribue = min($resteDon, $resteBesoin);

                $resteDon -= $attribue;
                $resteBesoin -= $attribue;

                $donRemaining[$idDon] = $resteDon;
                $besoinRemaining[$idBesoin] = $resteBesoin;

                // ligne utilse et afficher
                $allocations[] = [
                    'id_don' => $idDon,
                    'id_besoin' => $idBesoin,
                    'id_ville' => (int)$besoin['id_ville'],
                    'ville_name' => $besoin['ville_name'] ?? null,
                    'id_article' => $idArticle,
                    'article_name' => $don['article_name'] ?? ($besoin['article_name'] ?? null),
                    'type_besoin_name' => $don['type_besoin_name'] ?? ($besoin['type_besoin_name'] ?? null),
                    'attribue' => $attribue,

                    'date_don' => $don['date_don'],
                    'date_saisie' => $besoin['date_saisie'],
                    'reste_don' => $resteDon,
                    'reste_besoin' => $resteBesoin,
                ];
                $nbAlloc++;
            }
        }

        $totalDon = array_sum(array_map(fn($d) => (float)$d['quantite'], $dons));
        $totalBesoin = array_sum(array_map(fn($b) => (float)$b['quantite'], $besoins));
        $totalAttribue = array_sum(array_map(fn($a) => (float)$a['attribue'], $allocations));

        $coverage = ($totalBesoin > 0) ? round(($totalAttribue / $totalBesoin) * 100, 2) : 0.0;

        return [
            'dons' => $dons,
            'besoins' => $besoins,
            'allocations' => $allocations,
            'donRemaining' => $donRemaining,
            'besoinRemaining' => $besoinRemaining,
            'stats' => [
                'nb_dons' => count($dons),
                'nb_besoins' => count($besoins),
                'nb_allocations' => $nbAlloc,
                'total_don' => $totalDon,
                'total_besoin' => $totalBesoin,
                'total_attribue' => $totalAttribue,
                'coverage_percent' => $coverage,
            ],
        ];
    }

    public function validateSimulation($simulation)
    {
        $allocations = $simulation['allocations'] ?? [];
        if (empty($allocations)) {
            throw new \Exception("Impossible de valider : aucune allocation à enregistrer.");
        }

        // Pour recalculer les totaux attribués par don / par besoin
        $sumByDon = [];    // id_don => total attribué (dans cette validation)
        $sumByBesoin = []; // id_besoin => total attribué

        foreach ($allocations as $a) {
            $idDon = (int)($a['id_don'] ?? 0);
            $idBesoin = (int)($a['id_besoin'] ?? 0);
            $qty = (float)($a['attribue'] ?? 0);

            $sumByDon[$idDon] = ($sumByDon[$idDon] ?? 0) + $qty;
            $sumByBesoin[$idBesoin] = ($sumByBesoin[$idBesoin] ?? 0) + $qty;
        }

        try {
            $this->pdo->beginTransaction();

            $sqlInsertDispatch = "
            INSERT INTO dispatch (id_don, id_ville, quantite_attribuee, date_dispatch)
            VALUES (?, ?, ?, NOW())
        ";
            $stmtInsert = $this->pdo->prepare($sqlInsertDispatch);

            $nbDispatchInserted = 0;
            foreach ($allocations as $a) {
                $stmtInsert->execute([
                    $a['id_don'],
                    $a['id_ville'],
                    $a['attribue'],
                ]);
                $nbDispatchInserted++;
            }

            $stmtDonQty = $this->pdo->prepare("SELECT quantite FROM don WHERE id_don = ? FOR UPDATE");
            $stmtDonSumDispatch = $this->pdo->prepare("SELECT COALESCE(SUM(quantite_attribuee),0) AS used FROM dispatch WHERE id_don = ?");
            $stmtUpdateDon = $this->pdo->prepare("UPDATE don SET statut = ? WHERE id_don = ?");

            $nbDonUpdated = 0;
            foreach (array_keys($sumByDon) as $idDon) {

                $stmtDonQty->execute([$idDon]);
                $donRow = $stmtDonQty->fetch(PDO::FETCH_ASSOC);
                if (!$donRow) {
                    throw new \Exception("Don introuvable: id_don={$idDon}");
                }
                $donQty = (float)$donRow['quantite'];

                $stmtDonSumDispatch->execute([$idDon]);
                $used = (float)($stmtDonSumDispatch->fetch(PDO::FETCH_ASSOC)['used'] ?? 0);

                $reste = $donQty - $used;

                if ($reste <= 0.000001) {
                    $newStatus = 'DISPATCHE';   
                } else {
                    $newStatus = self::DON_STATUS_PARTIEL;
                }

                $stmtUpdateDon->execute([$newStatus, $idDon]);
                $nbDonUpdated++;
            }

            $nbBesoinUpdated = 0;


            $stmtBesoinQty = $this->pdo->prepare("SELECT quantite FROM besoin_ville WHERE id_besoin = ? FOR UPDATE");
            $stmtBesoinSum = $this->pdo->prepare("SELECT COALESCE(SUM(quantite_attribuee),0) AS used FROM dispatch WHERE id_besoin = ?");
            $stmtUpdateBesoin = $this->pdo->prepare("UPDATE besoin_ville SET status = ? WHERE id_besoin = ?");

            foreach (array_keys($sumByBesoin) as $idBesoin) {
                $stmtBesoinQty->execute([$idBesoin]);
                $bRow = $stmtBesoinQty->fetch(PDO::FETCH_ASSOC);
                if (!$bRow) throw new \Exception("Besoin introuvable: id_besoin={$idBesoin}");
                $bQty = (float)$bRow['quantite'];

                $stmtBesoinSum->execute([$idBesoin]);
                $used = (float)($stmtBesoinSum->fetch(PDO::FETCH_ASSOC)['used'] ?? 0);

                $reste = $bQty - $used;
                $newStatus = ($reste <= 0.000001) ? "satisfait" : self::BESOIN_STATUS_PARTIEL;

                $stmtUpdateBesoin->execute([$newStatus, $idBesoin]);
                $nbBesoinUpdated++;
            }

            $this->pdo->commit();

            return [
                'dispatch_inserted' => $nbDispatchInserted,
                'dons_updated' => $nbDonUpdated,
                'besoins_updated' => $nbBesoinUpdated,
            ];
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
