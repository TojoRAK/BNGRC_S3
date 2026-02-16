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
                d.quantite,
                d.date_don,
                d.source,
                d.status
            FROM don d
            WHERE d.status IN (?, ?)
            ORDER BY d.date_don ASC, d.id_don ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            self::DON_STATUS_NON_DISPATCHE,
            self::DON_STATUS_PARTIEL,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getBesoinOuvert(): array
    {
        $sql = "
            SELECT
                b.id_besoin,
                b.id_ville,
                b.id_article,
                b.quantite,
                b.date_saisie,
                b.status
            FROM besoin_ville b
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

    public function simulateDispatch(): array
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
                    'id_article' => $idArticle,
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
}
