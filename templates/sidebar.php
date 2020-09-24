<aside class="col-lg-2 no-padding">
    <ul class="listgroup list-group-flush sidebar">
        <?php if (isset($_SESSION['is_logged_in']) && $count == 0): ?>
            <li class="list-group-item">Da biste mogli posuđivati knjige morate platiti članarinu. 
            <?php if ($_SESSION['is_logged_in']->role != 'admin'  && isVerifyed($_SESSION['is_logged_in']->id, $connect)): ?>
                <a href="<?php echo $path ?>private/users/membership.php">Plati</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li class="list-group-item">Članarina vrijedi godinu dana od dana kada ste platili</li>
        <?php endif; ?>
        <?php if (isset($_SESSION['is_logged_in']) && !isVerifyed($_SESSION['is_logged_in']->id, $connect)): ?>
            <li class="list-group-item">Poruka sa aktivacijskim ključem poslana je na vašu email adresu. 
            Aktivirajte vaš korisnički račun kako bi mogli posuđivati knjige i plačati članarine</li>
        <?php endif; ?>
        <li class="list-group-item list-group-item-primary">Aktivni korisnici</li>
    </ul>
</aside>
