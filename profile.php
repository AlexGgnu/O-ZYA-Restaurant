    <?php
        require_once('./php/function_account.php');

        require_once('./php/header.php');
        require_once('./php/footer.php');

        if (is_logged() && isset($_SESSION['uuid'])) {
            $account_data = get_account_by_id($_SESSION['uuid']);
            $orders = get_orders_by_user($_SESSION['uuid']);
        } else {
            header("Location: ./connection.php");
            exit();
        }
    ?>
    <!DOCTYPE html>
    <html lang="fr-FR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">

            <title>Mon profil - O'ZYA Restaurant</title>

            <link rel="icon" type="image/x-icon" href="./assets/icons/favicon.ico">
            <link rel="stylesheet" href="./styles/main.css">

            <script src="./scripts/common.js" defer></script>
        </head>
        <body>
            <?php echo get_header(true, false); ?>

            <main class="flex-col gap-40 ph-40 pv-20">
                <h1 class="w-full">Mon Profil</h1>

                <div class="flex-row items-stretch gap-20 w-full lg-flex-col">
                    <section class="flex-1 w-full flex-col gap-20">
                        <!-- MARK: - Personal informations -->
                        <div class="form-card gap-24 max-w-full m-0">
                            <h2 class="text-primary font-600">Informations personnelles</h2>

                            <div class="flex-col gap-12">
                                <div class="form-group-line">
                                    <label for="name">Nom</label>

                                    <div class="form-group-line__input-wrapper">
                                        <input type="text" id="name" name="name" value="<?= $account_data['lastname'] ?>" disabled/>
                                        <button class="btn btn-svg btn-primary">
                                            <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group-line">
                                    <label for="firstName">Prénom</label>
                                    
                                    <div class="form-group-line__input-wrapper">
                                        <input type="text" id="firstName" name="firstName" value="<?= $account_data['firstname'] ?>" disabled/>
                                        <button class="btn btn-svg btn-primary">
                                            <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group-line">
                                    <label for="email">E-mail</label>
                                    
                                    <div class="form-group-line__input-wrapper">
                                        <input type="text" id="email" name="email" value="<?= $account_data['email'] ?>" disabled/>
                                        <button class="btn btn-svg btn-primary">
                                            <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group-line">
                                    <label for="phone">N° de téléphone</label>
                                    
                                    <div class="form-group-line__input-wrapper">
                                        <input type="text" id="phone" name="phone" value="<?= $account_data['phone'] ?>" disabled/>
                                        <button class="btn btn-svg btn-primary">
                                            <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="form-group-line">
                                    <label for="address">Adresse</label>
                                    
                                    <div class="form-group-line__input-wrapper">
                                        <input type="text" id="address" name="address" value="<?= $account_data['address'] ?>" disabled/>
                                        <button class="btn btn-svg btn-primary">
                                            <svg class="svg-24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                                                <path xmlns="http://www.w3.org/2000/svg" d="M36.4 360.9L13.4 439 1 481.2C-1.5 489.7 .8 498.8 7 505s15.3 8.5 23.7 6.1L73 498.6l78.1-23c12.4-3.6 23.7-9.9 33.4-18.4c1.4-1.2 2.7-2.5 4-3.8L492.7 149.3c21.9-21.9 24.6-55.6 8.2-80.5c-2.3-3.5-5.1-6.9-8.2-10L453.3 19.3c-25-25-65.5-25-90.5 0L58.6 323.5c-10.4 10.4-18 23.3-22.2 37.4zm46 13.5c1.7-5.6 4.5-10.8 8.4-15.2c.6-.6 1.1-1.2 1.7-1.8L321 129 383 191 154.6 419.5c-4.7 4.7-10.6 8.2-17 10.1l-23.4 6.9L59.4 452.6l16.1-54.8 6.9-23.4z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- MARK: - Loyalty Account -->
                        <div class="form-card gap-24 max-w-full m-0">
                            <h2 class="text-primary font-600">Compte fidélité</h2>

                            <div class="flex-col gap-14">
                                <div class="flex-row justify-between items-center">
                                    <p>Points :</p>
                                    <p class="font-bold">120 pts</p>
                                </div>
                                <hr>
                                <div class="flex-row justify-between items-center">
                                    <p>Réduction disponible :</p>
                                    <p class="font-bold">-10%</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="flex-1 w-full min-h-0">
                        <!-- MARK: - Orders History -->
                        <div class="form-card h-full gap-24 max-w-full m-0 lg-h-500">
                            <h2 class="text-primary font-600">Historique des commandes</h2>

                            <div class="scrollable-wrapper">
                                <div class="scrollable-container">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>ID Commande</th>
                                                <th>Detail</th>
                                                <th>Total</th>
                                                <th>Statut</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($orders)) : ?>
                                                <tr>
                                                    <td colspan="5">Aucune commande</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($orders as $order) : ?>
                                                    <tr>
                                                        <td><?= $order['date_heure'] ?></td>
                                                        <td>#<?= $order['id_order'] ?></td>
                                                        <td><?= implode(", ", $order['details']) ?></td>
                                                        <td><?= $order['total'] ?>€</td>
                                                        <td>
                                                            <button class="btn btn-status" data-status="<?= $order['statut'] ?>">
                                                                <?= format_status($order['statut']) ?>
                                                            </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
            
            <?php echo get_footer(); ?>
        </body>
    </html>