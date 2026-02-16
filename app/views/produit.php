<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit - E-commerce</title>
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <header>
        <div class="container">
            <nav>
                <a href="index.html" class="logo">E-Varotra</a>
                <ul class="menu">
                    <li><a href="../index">Accueil</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>

        <section class="product-detail">
            <img src="/assets/images/<?= $image  ?>" alt="Produit 1">
            <div class="info">
                <h1><?= $name  ?></h1>
                <p><?= $description  ?></p>
                <p><strong>Prix :</strong> <?= $prix  ?>Ar</p>
            </div>
        </section>


    </main>

    <footer>
        <p>&copy; 2025 E-Varotra</p>
    </footer>
</body>

</html>