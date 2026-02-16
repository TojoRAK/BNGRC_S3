CREATE OR REPLACE VIEW v_details_ville AS
SELECT
  r.name AS region,
#   v.name AS ville,
  v.*,
  b.besoins_nature    AS besoins_nature_montant,
  b.besoins_materiaux AS besoins_materiaux_montant,
  b.besoins_argent    AS besoins_argent_montant,
  d.dons_attribues    AS dons_attribues_montant
FROM ville v
JOIN region r ON r.id_region = v.id_region

LEFT JOIN (
  SELECT
    bv.id_ville,
    SUM(IF(tb.name='NATURE',    bv.quantite*a.pu, 0)) AS besoins_nature,
    SUM(IF(tb.name='MATERIAUX', bv.quantite*a.pu, 0)) AS besoins_materiaux,
    SUM(IF(tb.name='ARGENT',    bv.quantite*a.pu, 0)) AS besoins_argent
  FROM besoin_ville bv
  JOIN article a      ON a.id_article = bv.id_article
  JOIN type_besoin tb ON tb.id_type   = a.id_type
  GROUP BY bv.id_ville
) b ON b.id_ville = v.id_ville

LEFT JOIN (
  SELECT
    dp.id_ville,
    SUM(dp.quantite_attribuee * a.pu) AS dons_attribues
  FROM dispatch dp
  JOIN don dn    ON dn.id_don     = dp.id_don
  JOIN article a ON a.id_article  = dn.id_article
  GROUP BY dp.id_ville
) d ON d.id_ville = v.id_ville

ORDER BY r.name, v.name;