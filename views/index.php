<section class="card">
    <h2>Créer un portefeuille</h2>
    <p class="subtitle">Enregistrez un nouveau portefeuille avec un code unique.</p>

    <form method="POST" action="/wallet/create">
        <div class="form-row">
            <div class="form-group">
                <label for="prenom">Prénom *</label>
                <input type="text" id="prenom" name="prenom" required placeholder="Hozil">
            </div>
            <div class="form-group">
                <label for="nom">Nom *</label>
                <input type="text" id="nom" name="nom" required placeholder="Diallo">
            </div>
        </div>

        <div class="form-group">
            <label for="telephone_create">Téléphone *</label>
            <input type="tel" id="telephone_create" name="telephone" required placeholder="70 123 45 67">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="code">Code portefeuille *</label>
                <input type="text" id="code" name="code" required placeholder="HOZ-001">
            </div>
            <div class="form-group">
                <label for="solde">Solde initial (CFA)</label>
                <input type="number" id="solde" name="solde" min="0" step="1" value="0" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Créer le portefeuille</button>
    </form>
</section>

<section class="card">
    <h2>Dépôt ou retrait</h2>
    <p class="subtitle">Effectuez une opération sur un portefeuille existant.</p>

    <div class="info-box">
        Frais de retrait : 1 % du montant, plafonné à 5 000 CFA.
    </div>

    <div class="form-group">
        <label for="telephone_op">Téléphone du portefeuille *</label>
        <input type="tel" id="telephone_op" name="telephone" form="deposit-form" placeholder="70 123 45 67" required>
    </div>

    <div class="form-group">
        <label for="montant">Montant (CFA) *</label>
        <input type="number" id="montant" name="montant" form="deposit-form" min="1" step="1" placeholder="10000" required>
    </div>

    <div class="btn-group">
        <form id="deposit-form" method="POST" action="/wallet/deposit" style="display:inline;">
            <input type="hidden" name="telephone" id="deposit-telephone">
            <input type="hidden" name="montant" id="deposit-montant">
            <button type="submit" class="btn btn-success" onclick="return submitOperation('deposit')">Dépôt</button>
        </form>
        <form id="withdraw-form" method="POST" action="/wallet/withdraw" style="display:inline;">
            <input type="hidden" name="telephone" id="withdraw-telephone">
            <input type="hidden" name="montant" id="withdraw-montant">
            <button type="submit" class="btn btn-warning" onclick="return submitOperation('withdraw')">Retrait</button>
        </form>
    </div>
</section>

<section class="card">
    <h2>Historique des transactions</h2>
    <p class="subtitle">Toutes les opérations enregistrées, les plus récentes en premier.</p>

    <div class="table-wrapper">
        <?php if (empty($transactions)): ?>
            <div class="empty-state">
                <p>Aucune transaction pour le moment.</p>
                <p>Créez un portefeuille et effectuez votre première opération.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Portefeuille</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Frais</th>
                        <th>Date & heure</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($tx->code) ?></code></td>
                            <td><?= htmlspecialchars(trim(($tx->walletPrenom ?? '') . ' ' . ($tx->walletNom ?? ''))) ?></td>
                            <td><?= htmlspecialchars($tx->walletTelephone ?? '') ?></td>
                            <td>
                                <span class="badge badge-<?= $tx->type === 'Dépôt' ? 'depot' : 'retrait' ?>">
                                    <?= htmlspecialchars($tx->type) ?>
                                </span>
                            </td>
                            <td class="amount"><?= number_format($tx->montant, 0, ',', ' ') ?> CFA</td>
                            <td class="fees">
                                <?= $tx->frais > 0
                                    ? number_format($tx->frais, 0, ',', ' ') . ' CFA'
                                    : '—' ?>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($tx->dateHeure))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</section>

<script>
    function submitOperation(type) {
        const telephone = document.getElementById('telephone_op').value.trim();
        const montant = document.getElementById('montant').value;

        if (!telephone || !montant || parseFloat(montant) <= 0) {
            alert('Veuillez renseigner un téléphone valide et un montant positif.');
            return false;
        }

        if (type === 'deposit') {
            document.getElementById('deposit-telephone').value = telephone;
            document.getElementById('deposit-montant').value = montant;
        } else {
            document.getElementById('withdraw-telephone').value = telephone;
            document.getElementById('withdraw-montant').value = montant;
        }

        return true;
    }
</script>
