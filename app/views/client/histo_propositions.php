<!doctype html>
<html lang="fr" data-theme="dark">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Historique des échanges — Takalo-takalo</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<link href="../assets/css/theme.css" rel="stylesheet">
</head>

<body class="tt-page">

	<?php include('inc/header.php') ?>

	<main class="tt-main">
		<div class="container py-4">
			<div class="tt-hero p-3 p-md-4 mb-3">
				<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
					<div>
						<h1 class="h4 mb-1">Historique des échanges</h1>
						<div class="text-muted-2">Échanges finalisés (acceptés).</div>
					</div>
					<div class="d-flex gap-2">
						<a class="btn btn-tt-ghost" href="/propositions/list"><i class="bi bi-arrow-left me-1"></i>Retour</a>
					</div>
				</div>
			</div>

			<div class="tt-surface p-3 p-md-4">
				<?php if (empty($echanges)) { ?>
					<div class="tt-empty">
						<div class="icon"><i class="bi bi-hourglass-split"></i></div>
						<div class="fw-semibold mt-2">Aucun échange</div>
						<div class="text-muted-2 small mt-1">Vous n'avez pas encore d'échanges finalisés.</div>
					</div>
				<?php } else { ?>
					<?php foreach ($echanges as $e) { ?>
						<div class="tt-card p-3 mb-3">
							<div class="d-flex flex-column flex-md-row justify-content-between gap-3">
								<div class="d-flex gap-3">
									<img src="../assets/img/placeholder.jpg" width="90" height="70"
										style="object-fit:cover;border-radius:12px;border:1px solid var(--tt-border)" alt="">
									<div>
										<div class="fw-semibold">
											Échange : <?= htmlspecialchars($e['wanted_title'] ?? '') ?> ⇄ <?= htmlspecialchars($e['offered_title'] ?? '') ?>
										</div>
										<div class="text-muted-2 small">
											Propriétaire: <span class="fw-semibold" style="color:var(--tt-text)"><?= htmlspecialchars($e['owner_name'] ?? '') ?></span>
											• Demandeur: <span class="fw-semibold" style="color:var(--tt-text)"><?= htmlspecialchars($e['requester_name'] ?? '') ?></span>
										</div>
										<div class="mt-2 d-flex gap-2 flex-wrap">
											<span class="badge badge-soft-success"><?= htmlspecialchars($e['status'] ?? '') ?></span>
											<?php if (!empty($e['traded_at'])) { ?>
												<span class="badge badge-soft"><?= date('d/m/Y H:i', strtotime($e['traded_at'])) ?></span>
											<?php } ?>
										</div>
									</div>
								</div>
								<div class="text-muted-2 small align-self-md-center">
									#<?= htmlspecialchars($e['echange_id'] ?? '') ?>
								</div>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</main>

	<?php include('inc/footer.php') ?>
	<script>document.getElementById('year') && (document.getElementById('year').textContent = new Date().getFullYear());</script>

	<div class="toast-container position-fixed bottom-0 end-0 p-3" id="ttToastContainer"></div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="../assets/js/app.js"></script>
</body>

</html>

